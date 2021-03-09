const writtenNumber = require('written-number');
const datepicker = require('js-datepicker');


if (document.URL.match(/edit_files/)) {

    $(function () {

        get_edit_file_docs();

        // highlight active thumb when clicked and scroll into view
        $(document).on('click', '.file-view-thumb-container', function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            window.location = '#page_' + id;
            //document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });



        // Functions

        function init() {
            // apply functions to fields
            $('.user-field-div').each(function() {
                set_field_options($(this).closest('.field-div-container'), $(this).data('type'));
            });

            $(document).on('click', '.field-div', function(e) {
                field_div_clicked($(this));
            });

            $('.file-image-bg').on('click', function() {
                hide_active_field();
            });

            $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image-bg', function (e) {
                add_field(e);
            });

            $(document).on('click', '.close-field-button', hide_active_field);

            $(document).on('click', '#save_file_button', save_edit_file);

            $('.edit-form-action').on('click', function() {
                $('.text-yellow').removeClass('active text-yellow').addClass('text-primary-dark');
                $(this).removeClass('text-primary-dark').addClass('active text-yellow');
            });

            // remove field
            $(document).on('click', '.remove-field', function () {
                $(this).closest('.field-div-container').remove();
            });

            // rotate files
            if($('#rotate_form_button').length > 0) {
                $(document).on('click', '.rotate-form-option', function() {
                    rotate_form($(this).data('degrees'));
                });
            }

            if ($('.field-datepicker').length > 0) {

                $('.field-datepicker').each(function() {
                    let id = $(this).prop('id');
                    window.picker = datepicker('#'+id, {
                        onSelect: (instance, date) => {

                            const value = date.toLocaleDateString();
                            $('#' + instance.el.id).closest('.field-div-container').find('div.data-div').html(value);
                            $('#' + instance.el.id).closest('.field-div-container').find('.field-input').val(value);

                        },
                        onShow: instance => {

                            let field_div_container = $('#' + instance.el.id).closest('.field-div-container');
                            let clear_datepicker_button = field_div_container.find('.clear-datepicker');
                            if(clear_datepicker_button.length == 0) {
                                field_div_container.find('.qs-datepicker').append('<div class="my-2 text-center w-100"><a href="javascript:void(0)" class="clear-datepicker text-danger"><i class="fad fa-times-circle mr-2"></i> Clear</a></div>');
                            }
                            clear_datepicker_button.on('click', function() {
                                clear_datepicker(clear_datepicker_button);
                            });

                        },
                        onHide: instance => {
                            //$('.field-div-container.show').removeClass('show');
                        },
                        formatter: (input, date, instance) => {
                            const value = date.toLocaleDateString();
                            input.value = value;
                        },
                        showAllDates: true,
                    });
                });

            }

            inline_editor();

            // change highlighted thumb on scroll when doc is over half way in view
            $('#file_viewer').on('scroll', function () {

                // Stop the loop once the first is found
                let cont = 'yes';

                $('.file-view-page-container').each(function () {

                    if (cont == 'yes') {
                        let id, center, start, end;
                        id = $(this).data('id');
                        // see if scrolled past half way
                        center = $(window).height() / 2;
                        start = $(this).offset().top;
                        end = start + $(this).height();
                        if (start < center && end > center) {
                            // set opacity to 1 for active and .2 for not active
                            $('.file-view-page-container').removeClass('active');
                            $(this).addClass('active');
                            $('#active_page').val(id);
                            // add border to thumb and scroll into view
                            $('.file-view-thumb-container').removeClass('active');
                            $('#thumb_' + id).addClass('active');
                            document.getElementById('thumb_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                            cont = 'no';
                        }
                    }
                });

            });

            $('.field-div[data-type="name"], .field-div[data-type="address"], .field-div[data-category="number"]').each(function() {
                set_field_text($(this));
            });

            $('.field-input').each(function() {
                $(this).data('original-value', $(this).val());
            });

            window.addEventListener("beforeunload", function (e) {

                let changes = 'no';
                $('.field-input').each(function() {
                    if(changes == 'no') {
                        if($(this).val() != $(this).data('original-value')) {
                            changes = 'yes';
                        }
                    }
                });

                if(changes == 'yes') {
                    var confirmationMessage = 'You have unsaved changes,'
                                            + 'are you sure you want to leave this page?';

                    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                    return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
                }
                return true;

            });
        }

        function get_edit_file_docs() {

            let document_id = $('#document_id').val();
            axios.get('/agents/doc_management/transactions/get_edit_file_docs', {
                params: {
                    document_id: document_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {
                $('#files_div').html(response.data);
                init();
            })
            .catch(function (error) {

            });
        }

        function add_field(e) {

            let field_type = $('.edit-form-action.active').data('field-type');

            if(field_type) {

                hide_active_field();

                let container = $(e.target.parentNode);

                let coords = set_and_get_field_coordinates(e, null, 'no');
                let x_perc = coords.x;
                let y_perc = coords.y;
                let h_perc = coords.h;
                let w_perc = coords.w;

                // create unique id for field
                let field_id = Date.now();

                let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, field_id, $('#active_page').val(), field_type);

                $('.field-div-container.show').removeClass('show');

                // append new field
                container.append(field);

                let ele = $('.field-div-container.show');

                // run this again in case it was placed out of bounds
                set_and_get_field_coordinates(null, ele, 'no');

                set_field_options(ele, field_type);

                if(field_type == 'user_text') {
                    inline_editor();
                }

            }

        }

        function set_field_options(ele, field_type) {

            let container = ele.closest('.fields-container');

            let handles = {
                'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
            };

            if(field_type == 'highlight') {
                handles = {
                    'nw': '.ui-resizable-nw', 'ne': '.ui-resizable-ne', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw'
                }
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

            // make field resizable
            ele.resizable({
                containment: container,
                handles: handles,
                stop: function (e, ui) {
                    let resized_ele = $(e.target);
                    set_and_get_field_coordinates(null, resized_ele, 'yes');
                }
            });

        }

        function field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, page, field_type) {

            let field_class = '';
            let field_html = '';
            let handles = ' \
            <div class="field-handle ui-resizable-handle ui-resizable-e"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-w"></div> \
            ';

            if(field_type == 'highlight') {
                handles = ' \
                <div class="field-handle ui-resizable-handle ui-resizable-nw"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-ne"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-se"></div> \
                <div class="field-handle ui-resizable-handle ui-resizable-sw"></div> \
                ';
            }

            let inline = '';
            if(field_type == 'user_text') {
                field_class = 'user-field-div textline-div';
                field_html = '<div class="data-div textline-html inline-editor"></div> \
                <input type="hidden" class="field-input user-field-input" data-id="" data-field-id="'+field_id+'" data-group-id="'+group_id+'" data-field-type="'+field_type+'">';
                inline = 'inline';
            } else if (field_type == 'strikeout') {
                field_class = 'user-field-div strikeout-div';
                field_html = '<div class="data-div strikeout-html"></div>';
            } else if (field_type == 'highlight') {
                field_class = 'user-field-div highlight-div';
                field_html = '<div class="data-div highlight-html"></div>';
            }

            return ' \
            <div class="field-div-container show '+field_type+'" style="position: absolute; top: '+y_perc+'%; left: '+x_perc+'%; height: '+h_perc+'%; width: '+w_perc+'%;"> \
                <div class="field-div new '+field_class+' group_'+group_id+' '+inline+'" style="position: absolute; top: 0%; left: 0%; height: 100%; width: 100%;" id="field_'+field_id+'" data-field-id="'+field_id+'" data-group-id="'+group_id+'" data-type="'+field_type+'" data-category="'+field_type+'" data-page="'+page+'"></div> \
                <div class="field-options-holder w-100"> \
                    <div class="d-flex justify-content-around"> \
                        <div class="btn-group" role="group" aria-label="Field Options"> \
                            <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                            <a type="button" class="btn btn-danger remove-field"><i class="fad fa-times-circle fa-lg"></i></a> \
                        </div> \
                    </div> \
                </div> \
                '+handles+' \
                '+field_html+' \
            </div> \
            ';
        }

        function set_and_get_field_coordinates(e, ele, existing) {

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
            let ele_h_perc = 1.3;
            if(e) {
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;
            }

            // set w and h for new field
            h_perc = existing == 'no' ? 1.3 : (ele.height() / ele.parent().height()) * 100;
            w_perc = existing == 'no' ? 15 : (ele.width() / ele.parent().width()) * 100;
            h_perc = parseFloat(h_perc);
            w_perc = parseFloat(w_perc);

            // field data percents
            let field_div = ele.find('.field-div');
            field_div.data('hp', h_perc);
            field_div.data('wp', w_perc);
            field_div.data('xp', x_perc);
            field_div.data('yp', y_perc);

            ele.css({ height: h_perc+'%' });
            ele.css({ width: w_perc+'%' });
            ele.css({ left: x_perc+'%' });
            ele.css({ top: y_perc+'%' });


            // keep in view
            if (x_perc < 0) {
                ele.animate({ left: 0 + '%' }).find('field-div').data('wp', '0');
            }
            if ((x_perc + w_perc) > 100) {
                let pos = 100 - w_perc;
                ele.animate({ left: pos + '%' }).find('field-div').data('wp', pos);
            }

            if (y_perc < 0) {
                ele.animate({ top: '0%' }).find('field-div').data('yp', '0');
            }

            return {
                h: h_perc,
                w: w_perc,
                x: x_perc,
                y: y_perc
            }

        }

        function save_edit_file() {

            $('#save_file_button').prop('disabled', true).html('<i class="fad fa-save fa-lg"></i><br>Saving <span class="spinner-border spinner-border-sm ml-2"></span>');

            // save system field input values
            let inputs = [];
            $('.field-input').not('.user-field-input').each(function() {
                let input = {
                    id: $(this).data('id'),
                    value: $(this).val()
                }
                inputs.push(input);
            });

            inputs = JSON.stringify(inputs);
            let formData = new FormData();
            formData.append('inputs', inputs);
            axios.post('/agents/doc_management/transactions/edit_files/save_edit_system_inputs', formData, axios_options)
            .then(function (response) {
                // add user fields and inputs
                let Listing_ID = $('#Listing_ID').val();
                let Contract_ID = $('#Contract_ID').val();
                let transaction_type = $('#transaction_type').val();
                let Agent_ID = $('#Agent_ID').val();
                let file_id = $('#file_id').val();

                let user_fields = [];
                $('.user-field-div').each(function() {

                    let field_div = $(this);
                    let field_type = field_div.data('type');
                    let file_id = $('#file_id').val();

                    let user_field = {
                        file_id: file_id,
                        create_field_id: field_div.data('field-id'),
                        field_type: field_type,
                        hp: field_div.data('hp'),
                        wp: field_div.data('wp'),
                        xp: field_div.data('xp'),
                        yp: field_div.data('yp'),
                        page: field_div.data('page'),
                        input_data: ''
                    }
                    // add input if user_text
                    if(field_type == 'user_text') {
                        let input = field_div.closest('.field-div-container').find('.field-input');
                        let input_data = {
                            value: input.val()
                        }
                        user_field.input_data = input_data;
                    }

                    user_fields.push(user_field);
                });

                user_fields = JSON.stringify(user_fields);

                let formData = new FormData();
                formData.append('Agent_ID', Agent_ID);
                formData.append('Listing_ID', Listing_ID);
                formData.append('Contract_ID', Contract_ID);
                formData.append('transaction_type', transaction_type);
                formData.append('file_id', file_id);
                formData.append('user_fields', user_fields);
                axios.post('/agents/doc_management/transactions/edit_files/save_edit_user_fields', formData, axios_options)
                .then(function (response) {

                    to_pdf();

                    $('.field-input').each(function() {
                        $(this).data('original-value', $(this).val());
                    });

                })
                .catch(function (error) {

                });

            })
            .catch(function (error) {

            });

        }

        function set_field_text(field_div) {

            let field_div_container = field_div.closest('.field-div-container');
            let inputs_container = field_div_container.find('.inputs-container');
            let field_name = field_div.data('field-name'); // SellerOrOwnerOneName, BuyerOrRenterBothAddress
            let group_id = field_div.data('group-id');
            let data_div = field_div_container.find('.data-div');

            if(field_div.data('category') == 'number') {

                let number = '';
                let number_value = inputs_container.find('.field-input').val();
                if(number_value != '') {
                    number = parseInt(number_value);
                }

                // add values to data-div for each field in group
                $('.group_' + group_id).each(function () {

                    let group_field_div_container = $(this).closest('.field-div-container');

                    group_field_div_container.find('.field-input').val(number_value);

                    // only the written fields will be split.
                    let number_type = $(this).data('number-type');
                    let group_data_div = group_field_div_container.find('.data-div');
                    if (number == '') {
                        group_data_div.html('');
                    } else {
                        if (number_type == 'numeric') {
                            group_data_div.html(global_format_number(number));
                        } else {
                            split_lines(group_id, writtenNumber(number));
                        }
                    }
                });

            } else if(field_div.data('type') == 'name') {

                if(field_div_container.find('.field-input').eq(0).length == 1) {

                    let name1 = field_div_container.find('.field-input').eq(0).val();
                    let name2 = field_div_container.find('.field-input').eq(1).val();
                    let name_value = '';

                    name_value = name1;

                    if(field_name.match(/Both/)) {
                        name_value = name1;
                        if(name2 != '') {
                            name_value += ', '+name2;
                        }
                    } else if(field_name.match(/One/)) {
                        name_value = name1;
                    } else if(field_name.match(/Two/)) {
                        name_value = name2;
                    }

                    if($('.group_' + group_id).length > 0) {
                        split_lines(group_id, name_value);
                    } else {
                        data_div.html(name_value);
                    }

                }

            } else if(field_div.data('type') == 'address') {

                let street = field_div_container.find('.field-input').eq(0).val();
                let city = field_div_container.find('.field-input').eq(1).val();
                let state = field_div_container.find('.field-input').eq(2).val();
                let zip = field_div_container.find('.field-input').eq(3).val();
                let county = field_div_container.find('.field-input').eq(4).val();

                let address_value = '';

                if(street != '') {

                    if(field_name.match(/Full/)) {
                        address_value = street+' '+city+', '+state+' '+zip;
                    } else if(field_name.match(/Street/)) {
                        address_value = street;
                    } else if(field_name.match(/City/)) {
                        address_value = city;
                    } else if(field_name.match(/State/)) {
                        address_value = state;
                    } else if(field_name.match(/Zip/)) {
                        address_value = zip;
                    } else if(field_name.match(/County/)) {
                        address_value = county;
                    }

                }


                if($('.group_' + group_id).length > 0) {
                    split_lines(group_id, address_value);
                } else {
                    data_div.html(address_value);
                }

            }

        }

        function field_div_clicked(field_div) {

            hide_active_field();

            let field_div_container = field_div.closest('.field-div-container');
            field_div_container.addClass('show');

            let group_id = field_div.data('group-id');


            if (!field_div.data('category').match(/(checkbox|radio|date|strikeout|highlight)/)) {

                // inline editor for fields with only one input - numbers excluded too
                if(field_div.hasClass('inline') && field_div.data('category') != 'number') {

                    field_div_container.find('.inline-editor').focus();


                    tinymce.activeEditor.on('focus', function(e) {
                        // set z-index so inline editor gets focus
                        field_div_container.find('.inline-editor').css({ 'z-index': 5 });
                        // selects all and puts cursor at end
                        tinymce.activeEditor.selection.select(tinyMCE.activeEditor.getBody(), true);
                        tinymce.activeEditor.selection.collapse(false);
                    });
                    tinymce.activeEditor.on('blur', function(e) {
                        field_div_container.find('.inline-editor').css({ 'z-index': 1 });
                        field_div_container.find('.field-input').val(field_div_container.find('.inline-editor').text());
                    });

                } else {

                    if(field_div.data('category') == 'number') {

                        // number fields are split sometimes - numeric and written
                        field_div_container.find('.field-input').focus();
                        // keep tab from firing, jumps down page
                        $(document).on('keydown', function(e) {
                            if(e.key == 'Tab') {
                                return false;
                            }
                        });

                    }

                }

            } else {

                if (field_div.data('category') == 'radio') {

                    // clear x's and values for all radios in group
                    $('.group_' + group_id).closest('.field-div-container').find('.data-div').html('');
                    $('.group_' + group_id).closest('.field-div-container').find('.field-input').val('');
                    // check clicked radio
                    field_div_container.find('.data-div').html('x');
                    // update input value
                    field_div_container.find('.field-input').val('checked');

                } else if (field_div.data('category') == 'checkbox') {

                    // if checked, uncheck
                    if(field_div_container.find('.data-div').text().match(/x/)) {
                        field_div_container.find('.data-div').text('');
                        // update input value
                        field_div_container.find('.field-input').val('');
                    } else {
                        // check
                        field_div_container.find('.data-div').text('x');
                        // update input value
                        field_div_container.find('.field-input').val('checked');
                    }

                } else if (field_div.data('category') == 'date') {

                    field_div_container.find('.field-datepicker').trigger('click');

                }

            }
        }

        function hide_active_field() {

            let field_div_container = $('.field-div-container.show');
            if(field_div_container.length > 0) {
                set_field_text(field_div_container.find('.field-div'));
                update_common_fields(field_div_container.find('.field-div'));
                field_div_container.removeClass('show');
            }

        }

        function update_common_fields(field_div) {

            let field_div_container = field_div.closest('.field-div-container');
            let field_name = field_div.data('field-name');

            if(field_div.data('type') == 'name') {

                let name1 = field_div_container.find('.field-input').eq(0).val();
                let name2 = field_div_container.find('.field-input').eq(1).val();

                let name_types = ['BuyerOrRenterOne', 'BuyerOrRenterTwo', 'BuyerOrRenterBoth', 'SellerOrOwnerOne', 'SellerOrOwnerTwo', 'SellerOrOwnerBoth'];

                let name_type = '';
                name_types.forEach(function(type) {
                    if(field_name.match(type)) {
                        name_type = type.replace(/(One|Two|Both)/, '');
                    }
                });

                let name_fields = ['One', 'Two', 'Both'];

                name_fields.forEach(function(name_field) {
                    $('[data-field-name="'+name_type+name_field+'Name"]').each(function() {
                        let input = $(this).closest('.field-div-container').find('.field-input');
                        input.eq(0).val(name1);
                        input.eq(1).val(name2);
                        set_field_text($('[data-field-name="'+name_type+name_field+'Name"]'));
                    });
                });


            } else if(field_div.data('type') == 'address') {

                let street = field_div_container.find('.field-input').eq(0).val();
                let city = field_div_container.find('.field-input').eq(1).val();
                let state = field_div_container.find('.field-input').eq(2).val();
                let zip = field_div_container.find('.field-input').eq(3).val();
                let county = field_div_container.find('.field-input').eq(4).val();

                let address_types = ['BuyerOrRenterOne', 'BuyerOrRenterTwo', 'BuyerOrRenterBoth', 'SellerOrOwnerOne', 'SellerOrOwnerTwo', 'SellerOrOwnerBoth', 'BuyerAgent', 'ListAgent', 'Property'];
                let address_fields = ['Street', 'City', 'State', 'Zip', 'County'];

                let address_type = '';
                address_types.forEach(function(type) {
                    if(field_name.match(type)) {
                        address_type = type;
                    }
                });

                address_fields.forEach(function(field) {
                    // loop though field divs with address type and field
                    $('[data-field-name="'+address_type+field+'"]').each(function() {
                        let input = $(this).closest('.field-div-container').find('.field-input');
                        input.eq(0).val(street);
                        input.eq(1).val(city);
                        input.eq(2).val(state);
                        input.eq(3).val(zip);
                        input.eq(4).val(county);
                        set_field_text($('[data-field-name="'+address_type+field+'"]'));
                    });

                });

            } else if(field_div.data('type') == 'number') {

                let number = field_div_container.find('.field-input').eq(0).val();
                $('[data-field-name="'+field_name+'"]').closest('.field-div-container').find('.field-input').val(number);
                set_field_text($('[data-field-name="'+field_name+'"]'));

            }

        }

        function rotate_form(degrees) {
            $('.fa-sync-alt').addClass('fa-spin');
            global_loading_on('', '<div class="text-white">Rotating Document</div>');
            $('.file-view-page-container, .file-view-thumb-container').addClass('fadeOut');
            let file_id = $('#file_id').val();
            let file_type = $('#file_type').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            let formData = new FormData();
            formData.append('file_id', file_id);
            formData.append('file_type', file_type);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('degrees', degrees);
            axios.post('/agents/doc_management/transactions/edit_files/rotate_document', formData, axios_options)
            .then(function (response) {
                global_loading_off();
                $('.fa-sync-alt').removeClass('fa-spin');
                get_edit_file_docs();
            })
            .catch(function (error) {

            });

        }

        function to_pdf() {

            //global_loading_on('', '<div class="h3 text-white">Merging Fields, Creating and Saving PDF.</div> <div class="h3 mt-5 text-yellow">Please be patient, this process can take <br>5 - 10 seconds for each page.</div>');

            toastr['success']('Changes Successfully Saved');

            let els = '.system-html, .data-div-radio-check, .highlight-html, .user_textinline';
            let styles;
            $(els).each(function () {
                let data_div = $(this);
                styles = [
                    /*
                    'color',
                    'display',
                    'font-size',
                    'font-family',
                    'font-weight',
                    'left',
                    'letter-spacing',


                    'opacity',
                    'overflow',
                    'padding-bottom',
                    'position',
                    'text-align',
                    'top',
                    'white-space' */
                    'background',
                    'line-height',
                    'margin-left',
                    'margin-top',
                    'padding-left',
                    'padding-top',
                    'top'
                ];
                $.each(styles, function (index, style) {
                    data_div.data(style, data_div.css(style));
                });
            });

            // set inline styles for PDF
            // system fields
            let font_size = '13px';
            let top = '3px';
            if($('#page_size').val() == 'a4') {
                font_size = '12px';
                top = '3px';
            }
            let font_family = "'Roboto Condensed', sans-serif";
            $('.data-div.system-html, .textline-html').css({
                'position': 'absolute',
                'top': top,
                'left': '0px',
                'width': '100%',
                'overflow': 'visible',
                'white-space': 'nowrap',
                'font-size': font_size,
                'color': 'black',
                'line-height': '1',
                'padding-top': '0px',
                'padding-left': '0px',
                'font-family': font_family
            });
            $('.data-div.system-html').not('.inline-editor').css({
                'text-align': 'center'
            });
            $('.inline-editor').css({
                'font-size': font_size
            });
            $('.data-div-checkbox').css({
                'display': 'block',
                'height': '100%',
                'width': '100%',
                'margin-left': '2px',
                'margin-top': '1px',
                'color': '#000',
                'font-size': '1.4em',
                'line-height': '35%',
                'font-weight': 'bold',
                'font-family': font_family
            });
            $('.data-div-radio').css({
                'margin-left': '2px',
                'margin-top': '2px',
                'color': '#000',
                'font-size': '1.3em',
                'line-height': '40%',
                'font-weight': 'bold',
                'font-family': font_family
            });
            // remove background
            //$('.file-image-bg').css({ opacity: '0.0' });

            // user fields
            $('.data-div.highlight-html').css({
                'position': 'relative',
                'display': 'block',
                'background': 'rgba(255, 237, 74,.3)',
                'height': '100%',
                'width': '100% !important'
            });
            $('.data-div.strikeout-html').css({
                'display': 'block',
                'position': 'absolute',
                'top': '4px',
                'left': '0px',
                'width': '100%',
                'height': '4px',
                'background': '#000000',
                'line-height': '1'
            });
            //$('.data-div.strikeout-html').css({ display: 'none' });


            let file_id = $('#file_id').val();
            let document_id = $('#document_id').val();
            let file_name = $('#file_name').val();
            let file_type = $('#file_type').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            // remove datepicker html, datepicker input, background img, modals, left over input fields
            let elements_remove = '.file-image-bg, .field-div, .field-options-holder, .field-handle, .qs-datepicker-container, .field-datepicker, .inputs-container, .field-input';

            let formData = new FormData();

            // get html from all pages to add to pdf layer
            let c = 0;
            $('.file-view-page-container').each(function () {

                /* let container = $(this);
                let page_html = container.clone();

                page_html.find(elements_remove).remove();
                page_html = page_html.wrap('<div>').parent().html();
                //console.log(page_html);

                formData.append('page_' + c, page_html); */

                c += 1;
                let container = $(this);
                let page_html_top_clone = container.clone();
                let page_html_bottom_clone = container.clone();
                let page_html_top = '';
                let page_html_bottom = '';

                page_html_top_clone.find(elements_remove).remove();
                page_html_bottom_clone.find(elements_remove).remove();

                if(page_html_top_clone.find('.field-div-container').not('.highlight').length > 0) {
                    page_html_top = $(page_html_top_clone.wrap('<div>').parent().html().replace(/\>\s+\</g, '><'));
                    page_html_top.find('.highlight').remove();
                    page_html_top = page_html_top.wrap('<div>').parent().html();
                    formData.append('page_html_top_' + c, page_html_top);
                }

                if(page_html_bottom_clone.find('.highlight').length > 0) {
                    page_html_bottom = $(page_html_bottom_clone.wrap('<div>').parent().html().replace(/\>\s+\</g, '><'));
                    page_html_bottom.find('.field-div-container').not('.highlight').remove();
                    page_html_bottom = page_html_bottom.wrap('<div>').parent().html();
                    formData.append('page_html_bottom_' + c, page_html_bottom);
                }

            });

            formData.append('page_count', c);
            formData.append('file_id', file_id);
            formData.append('document_id', document_id);
            formData.append('file_type', file_type);
            formData.append('file_name', file_name);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);

            // reset all styles
            setTimeout(function () {
                $(els).each(function () {
                    let data_div = $(this);
                    $.each(styles, function (index, style) {
                        data_div.css(style, data_div.data(style));
                    });
                });

            }, 1000);

            $('#in_process_div').show();
            setTimeout(function() {
                in_process([document_id]);
            }, 3000);


            $('#save_file_button').html('<i class="fad fa-save fa-lg"></i><br>Save');

            axios_options['header'] = { 'content-type': 'multipart/form-data' };
            axios.post('/agents/doc_management/transactions/edit_files/convert_to_pdf', formData, axios_options)
                .then(function (response) {

                    //global_loading_off();
                    /* toastr['success']('Changes Successfully Saved');
                    $('#save_file_button').html('<i class="fad fa-save fa-lg"></i><br>Save'); */
                })
                .catch(function (error) {

                    });

        }

        function in_process(document_ids) {

            let formData = new FormData();
            formData.append('document_ids', document_ids);

            check_in_process = setInterval(function () {

                axios.post('/agents/doc_management/transactions/in_process', formData, axios_options)
                .then(function (response) {
                    if(response.data.in_process.length > 0) {
                        $('#in_process_div').show();
                    } else {
                        clearInterval(check_in_process);
                        $('#save_file_button').prop('disabled', false);
                        $('#in_process_div').hide();
                    }
                })
                .catch(function (error) {

                });

            }, 1000);


        }


        function inline_editor(/* ele */) {

            let options = {
                selector: '.inline-editor',
                inline: true,
                menubar: false,
                statusbar: false,
                toolbar: false,
                /* setup: function (ed) {
                    // limit chars by width / 6.5
                    let ele_width, field_div_container;
                    ed.on('keyup', function (e) {
                        field_div_container = $(e.target).closest('.field-div-container');
                        ele_width = field_div_container.find('.field-div').width();
                        console.log(ele_width);
                        if(!ele.find('.data-div').hasClass('textline-html')) {
                            let max_chars = Math.round(ele_width / 6.5);
                            let count = get_editor_text_count(ed).chars;
                            //console.log(ele, max_chars, count);
                            if(count > max_chars) {
                                toastr['error']('Max Characters of '+max_chars+' reached');
                                ed.on('keydown', function (e) {
                                    e.preventDefault();
                                    return false;
                                });
                            }
                        }
                    });
                } */
            }
            //tinymce.EditorManager.execCommand('mceRemoveEditor',true, '.inline-editor');
            text_editor(options);
        }

        // Returns text statistics for the specified editor
        /* function get_editor_text_count(editor) {
            let body = editor.getBody(), text = tinymce.trim(body.innerText || body.textContent);

            return {
                chars: text.length,
                words: text.split(/[\w\u2019\'-]+/).length
            };
        } */



        function split_lines(group_id, text) {

            text = text.trim();
            //let str_len = text.length;
            let field_type = $('.group_' + group_id).data('type');

            // split value between lines
            if ($('.group_' + group_id).not('[data-number-type="numeric"]').length == 1) {
                if (field_type == 'number') {
                    $('.group_' + group_id + '[data-number-type="written"]').first().closest('.field-div-container').find('.data-div').html(text);
                } else {
                    $('.group_' + group_id).first().closest('.field-div-container').find('.data-div').html(text);
                }

            } else {

                $('.group_' + group_id).not('[data-number-type="numeric"]').closest('.field-div-container').find('.data-div').html('');
                $('.group_' + group_id).not('[data-number-type="numeric"]').each(function () {
                    // if there is still text left over
                    if (text != '') {

                        let width = String(Math.ceil($(this).width()));
                        let text_len = text.length;
                        let max_chars = width * .15;
                        if (text_len > max_chars) {
                            let section = text.substring(0, max_chars);
                            let end = section.lastIndexOf(' ');
                            let field_text = text.substring(0, end);
                            $(this).closest('.field-div-container').find('.data-div').html(field_text);
                            let start = end + 1;
                            text = text.substring(start);
                        } else {
                            $(this).closest('.field-div-container').find('.data-div').html(text);
                            text = '';
                        }
                    }
                });

            }
        }

        function clear_datepicker(ele) {
            ele.closest('.field-div-container').find('.field-input').val('');
            ele.closest('.field-div-container').find('.data-div').html('');
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
