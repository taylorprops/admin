if(document.URL.match(/esign_add_fields/)) {

    $(function () {

        // highlight active thumb when clicked and scroll into view
        $(document).on('click', '.file-view-thumb-container', function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            window.location = '#page_' + id;
            //document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });

        $(document).on('click', '.field-div', function () {
            hide_active_field();
            $(this).addClass('show');
        });

        // change highlighted thumb on scroll when doc is over half way in view
        $('#file_viewer').on('scroll', function () {

            // Stop the loop once the first is found
            let cont = 'yes';

            $('.file-view-page-container').each(function () {

                if (cont == 'yes') {
                    let id, page, center, start, end;
                    id = $(this).data('id');
                    page = $(this).data('page');

                    // see if scrolled past half way
                    center = $(window).height() / 2;
                    start = $(this).offset().top;
                    end = start + $(this).height();

                    if (start < center && end > center) {
                        // set opacity to 1 for active and .2 for not active
                        $('.file-view-page-container').removeClass('active');
                        $(this).addClass('active');
                        $('#active_page').val(page);
                        // add border to thumb and scroll into view
                        $('.file-view-thumb-container').removeClass('active');
                        $('#thumb_' + id).addClass('active');
                        document.getElementById('thumb_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                        cont = 'no';
                    }
                }

            });

        });

        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image-bg', function (e) {
            add_field(e);
        });

        $('.edit-form-action').on('click', function() {
            $('.text-yellow').removeClass('active text-yellow').addClass('text-primary-dark');
            $(this).removeClass('text-primary-dark').addClass('active text-yellow');
        });

        $('.file-image-bg').on('click', function() {
            hide_active_field();
        });

        $(document).on('click', '.close-field-button', hide_active_field);

        $(document).on('click', '#send_for_signatures_button', show_send_for_signatures);

        // remove field
        $(document).on('click', '.remove-field', function () {
            $(this).closest('.field-div').remove();
        });

        $(document).on('change', '.signer-select', function () {
            show_signer($(this));
        });

        $('#active_signer').val($('.signer-select-option:first').val());


        ///////////////////// Functions //////////////////////

        function show_send_for_signatures() {

            if ($('.field-div').length > 0) {

                $('#send_for_signatures_modal').modal('show');

                $('#save_send_for_signatures_button').off('click').on('click', function() {

                    let form = $('#send_for_signatures_form');
                    let validate = validate_form(form);

                    if(validate == 'yes') {
                        send_for_signatures();
                    }
                });

            } else {

                toastr['error']('You must add signature fields before sending');

            }

        }

        function send_for_signatures() {

            let envelope_id = $('#envelope_id').val();
            let subject = $('#envelope_subject').val();
            let message = $('#envelope_message').val();
            let data = [];
            let document_ids = [];
            $('.file-view-page-container').each(function() {
                document_ids.push($(this).data('document-id'));
            });
            document_ids = document_ids.filter(global_array_unique);

            if ($('.field-div').length > 0) {

                $('.field-div').each(function () {

                    let field_div = $(this);
                    let field_id = field_div.data('field-id');
                    let field_type = field_div.data('field-type');
                    let document_id = field_div.data('document-id');
                    let signer_id = field_div.find('.signer-select option:selected').data('signer-id');
                    let signer = field_div.find('.signer-select').val();
                    let required_input = field_div.find('.signature-required');
                    let required = 'no';
                    if(required_input.is(':checked')) {
                        required = 'yes';
                    }



                    let field_data = {
                        'document_id': document_id,
                        'field_id': field_id,
                        'field_type': field_type,
                        'signer_id': signer_id,
                        'signer': signer,
                        'required': required,
                        'page': field_div.data('page'),
                        'left_perc': field_div.data('xp'),
                        'top_perc': field_div.data('yp'),
                        'height_perc': field_div.data('hp'),
                        'width_perc': field_div.data('wp')
                    }

                    data.push(field_data);

                });

            }

            let fields = JSON.stringify(data);

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            formData.append('subject', subject);
            formData.append('message', message);
            formData.append('document_ids', document_ids);
            formData.append('fields', fields);
            axios.post('/esign/esign_send_for_signatures', formData, axios_options)
            .then(function (response) {
                console.log(response);
            })
            .catch(function (error) {

            });
        }

        function add_field(e) {

            let field_type = $('.edit-form-action.active').data('field-type');
            let document_id = $('.file-view-page-container.active').data('document-id');

            if(field_type) {

                hide_active_field();

                let container = $(e.target.parentNode);

                let coords = set_and_get_field_coordinates(e, null, 'no', field_type);
                let x_perc = coords.x_perc;
                let y_perc = coords.y_perc;
                let h_perc = coords.h_perc;
                let w_perc = coords.w_perc;

                // create unique id for field
                let field_id = Date.now();
                let field_id_date = '';
                if(field_type == 'signature') {
                    field_id_date = parseInt(Date.now()) + 1;
                }

                let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, $('#active_page').val(), field_type, document_id);
                let field_date = '';
                if(field_type == 'signature') {
                    field_date = field_html(h_perc - 1, w_perc, x_perc + 18, y_perc + 1, field_id_date, $('#active_page').val(), 'date', document_id);
                }

                $('.field-div.show').removeClass('show');

                // append new field
                container.append(field);
                if(field_type == 'signature') {
                    container.append(field_date);
                }


                let ele = $('.field-div.show[data-field-type="'+field_type+'"]');
                let ele_date = '';
                if(field_type == 'signature') {
                    ele_date = $('.field-div.show[data-field-type="date"]');
                }


                let selected_option = ele.find('.signer-select-option[data-name="'+$('#active_signer').val()+'"]');
                selected_option.prop('selected', true);
                let field_name = selected_option.data('name');
                let field_div_html = '';
                let field_name_date = '';
                let field_div_html_date = '';

                if(field_type == 'signature') {
                    let selected_option_date = ele_date.find('.signer-select-option[data-name="'+$('#active_signer').val()+'"]');
                    selected_option_date.prop('selected', true);
                    field_name_date = selected_option_date.data('name');

                }

                if(field_type == 'signature') {
                    field_div_html = '<div><i class="fal fa-signature mr-2"></i> <span class="field-div-name">'+field_name+'</span></div>';
                    field_div_html_date = '<div><i class="fal fa-calendar mr-2"></i> <span class="field-div-name">'+field_name_date+'</span></div>';
                } else if(field_type == 'initials') {
                    let initials_array = field_name.match(/\b(\w)/g);
                    let initials = initials_array.join('');
                    field_div_html = '<span class="field-div-name">'+initials+'</span>';
                } else if(field_type == 'date') {
                    field_div_html = '<div><i class="fal fa-calendar mr-2"></i>  <span class="field-div-name">'+field_name+'</span></div>';
                }


                ele.find('.field-html').html(field_div_html);
                if(field_type == 'signature') {
                    ele_date.find('.field-html').html(field_div_html_date);
                }

                // run this again in case it was placed out of bounds
                set_and_get_field_coordinates(null, ele, 'no', field_type);
                set_field_options(ele, field_type);

                if(field_type == 'signature') {
                    set_and_get_field_coordinates(null, ele_date, 'no', 'date');
                    set_field_options(ele_date, 'date');
                    $('.field-div.show[data-field-type="date"]').removeClass('show');
                }

            }

        }

        function set_field_options(ele, field_type) {

            let container = ele.closest('.fields-container');

            let handles = {
                'nw': '.ui-resizable-nw', 'ne': '.ui-resizable-ne', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw'
            }

            // make field draggable
            ele.draggable({
                containment: container,
                handle: '.field-handle',
                cursor: 'grab',
                stop: function (e, ui) {
                    let dragged_ele = $(e.target);
                    set_and_get_field_coordinates(null, dragged_ele, 'yes');
                }
            });

            let max_height = 40;
            let min_height = 25;
            let min_width = 30;
            if(field_type == 'date') {
                max_height = 25;
            }
            // make field resizable
            ele.resizable({
                containment: container,
                handles: handles,
                maxHeight: max_height,
                minHeight: min_height,
                minWidth: min_width,
                stop: function (e, ui) {
                    let resized_ele = $(e.target);
                    set_and_get_field_coordinates(null, resized_ele, 'yes');
                }
            });

        }

        function field_html(h_perc, w_perc, x_perc, y_perc, field_id, page, field_type, document_id) {

            let signer_options = $('#signer_options_html').html();

            let field_html = ' \
            <div class="field-div show" style="position: absolute; top: '+y_perc+'%; left: '+x_perc+'%; height: '+h_perc+'%; width: '+w_perc+'%;" id="field_'+field_id+'" data-field-id="'+field_id+'" data-field-type="'+field_type+'" data-page="'+page+'" data-document-id="'+ document_id + '"> \
                <div class="field-html w-100 h-100 text-center text-primary small"></div> \
                <div class="field-options-holder"> \
                    <div class="d-flex justify-content-around"> \
                        <div class="btn-group" role="group" aria-label="Field Options"> \
                            <a type="button" class="btn btn-primary field-handle ml-0"><i class="fal fa-arrows fa-lg"></i></a> \
                            <a type="button" class="btn btn-danger remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
                        </div> \
                    </div> \
                </div> \
                <div class="select-signer-div font-8 p-2"> \
                    '+ucwords(field_type)+' for:\
                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel signer-select"> \
                        '+signer_options+' \
                    </select> \
                    <input type="checkbox" class="custom-form-element form-checkbox signature-required" value="yes" checked data-label="Required"> \
                </div> \
                <div class="field-handle ui-resizable-handle ui-resizable-nw"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-ne"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-se"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-sw"></div> \
            </div> \
            ';

            return field_html;
        }

        function show_signer(ele) {

            let container = ele.closest('.field-div');
            let field_type = container.data('field-type');
            let name = ele.find('option:selected').val();
            if(field_type == 'initials') {
                let initials_array = name.match(/\b(\w)/g);
                name = initials_array.join('');
            }
            container.find('.field-div-name').text(name);
            $('#active_signer').val(name);
        }

        function set_and_get_field_coordinates(e, ele, existing, field_type) {

            let container, x, y;

            // if from dblclick to add field
            if(e) {

                // get container
                container = $(e.target.parentNode);
                ele = $(e.target);
                // get bounding box coordinates
                let target_boundaries = e.target.getBoundingClientRect();

                // get target coordinates
                // subtract bounding box coordinates from target coordinates to get top and left positions
                // coordinates are relative to bounding box coordinates
                x = parseInt(Math.round(e.clientX - target_boundaries.left));
                y = parseInt(Math.round(e.clientY - target_boundaries.top));

            // coordinates of existing field
            } else {

                container = ele.parent();
                x = ele.position().left;
                y = ele.position().top;

            }

            // convert to percent
            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y, container);

            //set heights
            let ele_h_perc = 2.7;
            let ele_w_perc = 15;
            if(field_type == 'initials') {
                ele_w_perc = 4;
            } else if(field_type == 'date') {
                ele_h_perc = 1.8;
            }
            if(e) {
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;
            }

            // set w and h for new field
            h_perc = existing == 'no' ? ele_h_perc : (ele.height() / ele.parent().height()) * 100;
            w_perc = existing == 'no' ? ele_w_perc : (ele.width() / ele.parent().width()) * 100;
            h_perc = parseFloat(h_perc);
            w_perc = parseFloat(w_perc);

            // field data percents
            ele.data('hp', h_perc);
            ele.data('wp', w_perc);
            ele.data('xp', x_perc);
            ele.data('yp', y_perc);


            // keep in view
            if (x_perc < 0) {
                ele.animate({ left: 0 + '%' }).data('wp', '0');
            }
            if ((x_perc + w_perc) > 100) {
                let pos = 100 - w_perc;
                ele.animate({ left: pos + '%' }).data('wp', pos);
            }

            if (y_perc < 0) {
                ele.animate({ top: '0%' }).data('yp', '0');
            }

            setTimeout(function() {
                ele.find('.field-options-holder, .select-signer-div').removeClass('right');
                if(x_perc > 50) {
                    ele.find('.field-options-holder, .select-signer-div').addClass('right');
                }
            }, 10);

            return {
                h_perc: h_perc,
                w_perc: w_perc,
                x_perc: x_perc,
                y_perc: y_perc
            }

        }

        function hide_active_field() {

            let field_div_container = $('.field-div.show');
            if(field_div_container.length > 0) {
                field_div_container.removeClass('show');
            }

        }

        function pix_2_perc_xy(type, px, container) {
            if (type == 'x') {
                return (100 * parseFloat(px / parseFloat(container.width())));
            } else {
                return (100 * parseFloat(px / parseFloat(container.height())));
            }
        }

    });

}
