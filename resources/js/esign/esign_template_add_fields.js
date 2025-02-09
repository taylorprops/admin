if(document.URL.match(/esign_template_add_fields/)) {

    $(function() {

        let template_type = $('#template_type').val();
        $('#active_signer').val($('.signer-select-option:first').data('signer-role'));

        resize_docs();
        $(window).on('resize', resize_docs);

        $(document).on('click', '.edit-signers-button', function() {
            save_template();
            window.location = '/esign/esign_template_add_documents_and_signers/'+template_type+'/'+$('#template_id').val();
        });


        // highlight active thumb when clicked and scroll into view
        $(document).on('click', '.file-view-thumb-container', function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            window.location = '#page_' + id;
            //document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });

        // hide active field and show new one
        $(document).on('click', '.field-div', function () {
            if(!$(this).hasClass('show')) {
                hide_active_field();
                $(this).addClass('show');
                if(!$(this).find('.form-select-dropdown').hasClass('active')) {
                    $(this).find('.form-select-dropdown').addClass('active');
                }
            }

        });

        // change highlighted thumb on scroll when doc is over half way in view
        $('#file_viewer').on('scroll', on_scroll);


        // create new field
        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image-bg', function (e) {
            add_field(e);
        });

        // highlight form action buttons
        $('.edit-form-action').off('click').on('click', function() {
            $('.text-yellow').removeClass('active text-yellow').addClass('text-primary-dark');
            $(this).removeClass('text-primary-dark').addClass('active text-yellow');
        });

        // hide all active fields when clicked outside
        $('.file-image-bg').on('click', function() {
            hide_active_field();
        });

        // close active field
        //$(document).on('click', '.close-field-button', hide_active_field);

        // remove field
        $(document).on('click', '.remove-field', function () {
            $(this).closest('.field-div').remove();
        });

        $(document).on('change', '.signature-required', function() {
            if($(this).is(':checked')) {
                $(this).closest('.field-div').addClass('required');
            } else {
                $(this).closest('.field-div').removeClass('required');
            }
        });


        // update signer name or initials in field div
        $(document).on('change', '.signer-select', function () {
            let ele = $(this);
            setTimeout(function() {
                show_signer(ele);
            }, 10);
        });

        $(document).on('keyup change', '.text-input', function () {
            add_text($(this));
        });

        // init functions for fields
        setTimeout(function() {
            $('.field-div').each(function () {
                set_and_get_field_coordinates(null, $(this), 'yes', $(this).data('field_type'));
                set_field_options($(this), $(this).data('field_type'));
            });
        }, 500);

        $('#save_template_button').on('click', save_template)


    });

    function save_template() {

        let template_id = $('#template_id').val();
        let data = [];

        let pass = 'yes';

        if ($('.field-div').length > 0) {

            $('.field-div').each(function () {

                let field_div = $(this);
                let field_id = field_div.data('field-id');
                let field_type = field_div.data('field-type');
                let signer_role = field_div.find('.signer-select option:selected').val();
                let field_value = field_div.find('.text-input').val() ?? null;
                let required_input = field_div.find('.signature-required');
                let required = '0';
                if(field_type != 'text') {
                    if(required_input.is(':checked')) {
                        required = '1';
                    }
                } else {
                    if(field_value == '') {
                        $('#field_'+field_id).trigger('click');
                        document.getElementById('field_'+field_id).scrollIntoView();
                        $('.file-view').scrollTop($('.file-view').scrollTop() - 250);
                        toastr['error']('All text fields are required');
                        pass = 'no';
                    }
                }

                if(field_value) {
                    signer_role = 'OWNER';
                }

                let field_data = {
                    'template_id': template_id,
                    'field_id': field_id,
                    'field_type': field_type,
                    'signer_role': signer_role,
                    'field_value': field_value ?? null,
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

        if(pass == 'yes') {

            let fields = JSON.stringify(data);

            let formData = new FormData();
            formData.append('template_id', template_id);
            formData.append('fields', fields);

            axios.post('/esign/save_template', formData, axios_options)
            .then(function (response) {
                if(response.data.template_type == 'user') {
                    $('#modal_success').modal().find('.modal-body').html('Template Successfully Saved<br><br><a href="/esign?tab=templates" class="btn btn-primary">Return to Templates</a>');
                } else if(response.data.template_type == 'system') {
                    toastr['success']('Template Successfully Saved');
                }

            })
            .catch(function (error) {

            });

        }

    }

    function resize_docs() {

        let win = $(window);

        $('.file-view-page-container').each(function() {

            let h = $(this).data('height');
            let w = $(this).data('width');

            let height_perc = h / w;

            h = parseFloat(h) * 1.2;
            w = parseFloat(w) * 1.2;

            if (win.width() <= 1000) {
                w = parseFloat(w) * win.width() / 1000;
                h = w * height_perc;
            }

            $(this).css({ 'height': h+'pt', 'width': w+'pt' })/* .find('image').css({ 'height': h+'pt', 'width': w+'pt' }) */;

        });

    }

    function add_field(event) {

        let field_type = $('.edit-form-action.active').data('field-type');
        let template_id = $('#template_id').val();

        if(field_type) {

            hide_active_field();

            let container = $(event.target.parentNode);

            let coords = set_and_get_field_coordinates(event, null, 'no', field_type);
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

            $('.field-div.show').removeClass('show');

            let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, $('#active_page').val(), field_type, template_id, field_id);
            // append new field
            container.append(field);

            let field_date = '';
            if(field_type == 'signature') {
                field_date = field_html(parseFloat(h_perc) - 1, 12, parseFloat(x_perc) + 19, parseFloat(y_perc) + 1, field_id_date, $('#active_page').val(), 'date', template_id, field_id);
                container.append(field_date);
            }


            let ele = $('.field-div.show[data-field-type="'+field_type+'"]');
            let ele_date = '';
            if(field_type == 'signature') {
                ele_date = $('.field-div.show[data-field-type="date"]');
            }

            let selected_option = ele.find('.signer-select-option[data-signer-role="'+$('#active_signer').val()+'"]');
            if(selected_option.length == 0) {
                selected_option = ele.find('.signer-select-option:first');
            }


            selected_option.prop('selected', true);
            let field_name = selected_option.data('signer-role');

            let field_div_html = '';
            let field_name_date = '';
            let field_div_html_date = '';

            if(field_type == 'signature') {

                let selected_option_date = ele_date.find('.signer-select-option[data-signer-role="'+$('#active_signer').val()+'"]');
                if(selected_option_date.length == 0) {
                    selected_option_date = ele.find('.signer-select-option:first');
                }
                selected_option_date.prop('selected', true);
                field_name_date = selected_option_date.data('signer-role');

                field_div_html = '<div class="field-div-details"><i class="fad fa-signature mr-2"></i> <span class="field-div-name">'+field_name+'</span></div>';
                field_div_html_date = '<div class="field-div-details"><i class="fad fa-calendar mr-2"></i> <span class="field-div-name">'+field_name_date+'</span></div>';

            } else if(field_type == 'initials') {

                let initials_array = field_name.match(/\b(\w)/g);
                let initials = initials_array.join('');
                field_div_html = '<span class="field-div-name">'+initials+'</span>';

            } else if(field_type == 'date') {

                field_div_html = '<div class="field-div-details"><i class="fad fa-calendar mr-2"></i>  <span class="field-div-name">'+field_name+'</span></div>';

            } else if(field_type == 'name') {

                field_div_html = '<div class="field-div-details"><span class="field-div-name">'+field_name+'</span></div>';

            } else if(field_type == 'text') {

                field_div_html = '<div class="field-div-details"><span class="field-div-name">Text</span></div>';

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

            // show dropdown
            setTimeout(function() {
                ele.find('.form-select-dropdown').addClass('active');
            }, 100);

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

        let max_height = 50;
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

    function field_html(h_perc, w_perc, x_perc, y_perc, field_id, page, field_type, template_id, connector_id) {

        signer_options = $('#signer_options_template_html').html();

        let text_html = '';
        let non_text_html = '';
        let text_class = '';

        if(field_type == 'text') {
            text_html = ' \
            <input type="hidden" class="signature-required" value="0"> \
            <input type="text" class="custom-form-element form-input text-input" data-label="Enter Text"> \
            ';
            text_class = 'text';
        } else {
            non_text_html = ' \
            <input type="checkbox" class="custom-form-element form-checkbox signature-required" value="1" checked data-label="Required"> \
            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel signer-select" data-connector-id="'+ connector_id + '"> \
                '+signer_options+' \
            </select> \
            ';
        }

        let field_html = ' \
        <div class="field-div required show" style="position: absolute; top: '+y_perc+'%; left: '+x_perc+'%; height: '+h_perc+'%; width: '+w_perc+'%;" id="field_'+field_id+'" data-field-id="'+field_id+'" data-field-type="'+field_type+'" data-page="'+page+'" data-template-id="'+ template_id + '"> \
            <div class="field-html '+text_class+' w-100"></div> \
            <div class="field-options-holder"> \
                <div class="d-flex justify-content-around"> \
                    <div class="btn-group field-options-group" role="group" aria-label="Field Options"> \
                        <a type="button" class="btn btn-primary field-handle ml-0 pt-2"><i class="fal fa-arrows fa-lg"></i></a> \
                        <a type="button" class="btn btn-danger remove-field pt-2"><i class="fad fa-times-circle fa-lg"></i></a> \
                    </div> \
                </div> \
            </div> \
            <div class="select-signer-div font-9 p-2"> \
                '+ucwords(field_type)+'<br> \
                '+non_text_html+' \
                '+text_html+' \
            </div> \
            <div class="field-handle ui-resizable-handle ui-resizable-nw"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-ne"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-se"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-sw"></div> \
        </div> \
        ';

        return field_html;
    }

    function set_and_get_field_coordinates(event, ele, existing, field_type) {

        let container, x, y;

        // if from dblclick to add field
        if(event) {

            // get container
            container = $(event.target.parentNode);
            ele = $(event.target);
            // get bounding box coordinates
            let target_boundaries = event.target.getBoundingClientRect();

            // get target coordinates
            // subtract bounding box coordinates from target coordinates to get top and left positions
            // coordinates are relative to bounding box coordinates
            x = parseInt(Math.round(event.clientX - target_boundaries.left));
            y = parseInt(Math.round(event.clientY - target_boundaries.top));

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
        if(field_type == 'signature') {
            ele_w_perc = 18;
        } else if(field_type == 'initials') {
            ele_h_perc = 2;
            ele_w_perc = 3;
        } else if(field_type == 'date') {
            ele_h_perc = 1.8;
            ele_w_perc = 12;
        } else if(field_type == 'name') {
            ele_h_perc = 2.2;
        } else if(field_type == 'text') {
            ele_h_perc = 1.7;
        }

        if(event) {
            // remove element height from top position
            y_perc = y_perc - ele_h_perc;
        }

        // set w and h for new field
        h_perc = existing == 'no' ? ele_h_perc : (ele.height() / ele.parent().height()) * 100;
        w_perc = existing == 'no' ? ele_w_perc : (ele.width() / ele.parent().width()) * 100;
        h_perc = parseFloat(h_perc).toFixed(2);
        w_perc = parseFloat(w_perc).toFixed(2);

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

    function show_signer(ele) {

        let container = ele.closest('.field-div');
        let field_type = container.data('field-type');
        let connector_id = ele.data('connector-id');

        let orig_name = ele.find('option:selected').val();
        let name = orig_name;

        if(field_type == 'initials') {
            let initials_array = orig_name.match(/\b(\w)/g);
            name = initials_array.join('');
        }

        $('[data-connector-id="'+connector_id+'"]').each(function() {
            $(this).val(orig_name);
            $(this).closest('.field-div').find('.field-div-name').text(name);
        });

        $('#active_signer').val(orig_name);

    }

    function add_text(ele) {
        let text = ele.val();
        let field_html = ele.closest('.field-div').find('.field-html.text');
        field_html.text(text);
    }

    function pix_2_perc_xy(type, px, container) {
        if (type == 'x') {
            return (100 * parseFloat(px / parseFloat(container.width()))).toFixed(2);
        } else {
            return (100 * parseFloat(px / parseFloat(container.height()))).toFixed(2);
        }
    }

    function on_scroll() {

        // Stop the loop once the first is found
        let cont = 'yes';

        $('.file-view-page-container').each(function () {

            if (cont == 'yes') {
                let id, page, center, start, end;
                id = $(this).data('page-id');
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

    }


}
