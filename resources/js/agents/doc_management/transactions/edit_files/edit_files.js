const writtenNumber = require('written-number');
const datepicker = require('js-datepicker');


if (document.URL.match(/edit_files/)) {

    $(function () {

        get_edit_file_docs();

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
                                field_div_container.find('.qs-datepicker').append('<div class="my-2 text-center w-100"><a href="javascript:void(0)" class="clear-datepicker text-danger"><i class="fal fa-times-circle mr-2"></i> Clear</a></div>');
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

            form_elements();

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
                field_class = 'user-field-div textline-div standard';
                field_html = '<div class="data-div textline-html inline-editor"></div> \
                <input type="hidden" class="field-input user-field-input" data-id="" data-field-id="'+field_id+'" data-group-id="'+group_id+'" data-field-type="'+field_type+'">';
                inline = 'inline';
            } else if (field_type == 'strikeout') {
                field_class = 'user-field-div strikeout-div standard';
                field_html = '<div class="data-div strikeout-html"></div>';
            } else if (field_type == 'highlight') {
                field_class = 'user-field-div highlight-div standard';
                field_html = '<div class="data-div highlight-html"></div>';
            }

            return ' \
            <div class="field-div-container show" style="position: absolute; top: '+y_perc+'%; left: '+x_perc+'%; height: '+h_perc+'%; width: '+w_perc+'%;"> \
                <div class="field-div new '+field_class+' group_'+group_id+' '+inline+'" style="position: absolute; top: 0%; left: 0%; height: 100%; width: 100%;" id="field_'+field_id+'" data-field-id="'+field_id+'" data-group-id="'+group_id+'" data-type="'+field_type+'" data-category="'+field_type+'" data-page="'+page+'"></div> \
                <div class="field-options-holder w-100"> \
                    <div class="d-flex justify-content-around"> \
                        <div class="btn-group" role="group" aria-label="Field Options"> \
                            <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                            <a type="button" class="btn btn-danger remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
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

            let els = '.data-div, .file-image-bg, .field-div, .data-div-radio-check';
            let styles;
            $(els).each(function () {
                let data_div = $(this);
                styles = ['color', 'font-size', 'line-height', 'text-align', 'font-weight', 'opacity', 'background', 'margin-left', 'margin-top', 'padding-left', 'padding-top', 'padding-bottom', 'display', 'position', 'font-family', 'letter-spacing', 'margin-top'];
                $.each(styles, function (index, style) {
                    data_div.data(style, data_div.css(style));
                });
            });

            // set inline styles for PDF
            // system fields
            $('.data-div').not('.data-div-radio-check, .highlight-html, .strikeout-html').css({ 'font-size': '.9rem', 'color': 'black', 'padding-top': '4px', 'font-family': '\'Roboto\', sans-serif' });
            $('.data-div').not('.inline-editor').css({ 'text-align': 'center' });
            $('.data-div-checkbox').css({ 'margin-left': '3px', 'margin-top': '2px', 'color': '#000', 'font-size': '1.5em', 'line-height': '35%', 'font-weight': 'bold', 'font-family': '\'Roboto\', sans-serif' });
            $('.data-div-radio').css({ 'margin-left': '2px', 'color': '#000', 'font-size': '1.5em', 'line-height': '40%', 'font-weight': 'bold', 'font-family': '\'Roboto\', sans-serif' });
            // remove background
            //$('.file-image-bg').css({ opacity: '0.0' });

            // user fields
            $('.data-div.highlight-html').css({ background: 'yellow', opacity: '0.5', width: '100%', height: '100%' });
            $('.data-div.strikeout-html').css({ width: '100%', height: '4px', background: 'black', display: 'block', position: 'relative', 'margin-top': '8px' });


            let file_id = $('#file_id').val();
            let file_name = $('#file_name').val();
            let file_type = $('#file_type').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            // remove datepicker html, datepicker input, background img, modals, left over input fields
            let elements_remove = '.file-image-bg, .field-div, .field-options-holder, .field-handle, .qs-datepicker-container, .field-datepicker, .inputs-container';

            let formData = new FormData();

            // get html from all pages to add to pdf layer
            let c = 0;
            $('.file-view-page-container').each(function () {
                c += 1;
                let container = $(this);
                let page_html = container.clone();

                page_html.find(elements_remove).remove();
                page_html = page_html.wrap('<div>').parent().html();
                console.log(page_html);

                formData.append('page_' + c, page_html);
            });

            formData.append('page_count', c);
            formData.append('file_id', file_id);
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

            in_process([file_id]);
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

        // highlight active thumb when clicked and scroll into view
        $(document).on('click', '.file-view-thumb-container', function () {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            window.location = '#page_' + id;
            //document.getElementById('page_' + id).scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        });

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

/* if (document.URL.match(/edit_files/)) {

    $(function() {

        form_elements();

        $('[data-address-type="state"]').addClass('uppercase').attr('maxlength', 2);
        $('[data-address-type="zip"]').addClass('numbers-only').attr('maxlength', 5);

        if ($('.field-datepicker').length > 0) {

            $('.field-datepicker').each(function() {
                let id = $(this).prop('id');
                window.picker = datepicker('#'+id, {
                    onSelect: (instance, date) => {
                        const value = date.toLocaleDateString();
                        $('#' + instance.el.id).prev('div.data-div').html(value);
                        let element = $('#' + instance.el.id);
                        let wrapper = element.closest('.form-ele');
                        show_cancel_date(wrapper, element);
                    },
                    onHide: instance => {
                        $('.field-div').removeClass('active');
                    },
                    formatter: (input, date, instance) => {
                        const value = date.toLocaleDateString();
                        input.value = value;
                    },
                    showAllDates: true,
                });
            });

        }

        let field_div_count = $('.field-div').length;
        let field_count = 0;
        $('.field-div').each(function () {
            let group_id = '';
            group_id = $(this).data('group-id');
            // add grouped class
            if ($('.group_' + group_id).length > 1) {
                $('.group_' + group_id).removeClass('standard').addClass('group');
            }
            // date field has no form-div so using field-div instead
            let type = $(this).data('type');
            let form_div;
            if (type == 'date' || type == 'radio' || type == 'checkbox') {
                form_div = $(this);
            } else {
                form_div = $(this).find('.form-div');
            }
            fill_fields(type, group_id, form_div, 'load');
            field_count += 1;
            if(field_count == field_div_count) {
                setTimeout(function() {
                    // TODO: undo this comment
                    //save_field_input_values('yes');
                }, 1000);
            }
        });

        field_list();

        $('#save_field_input_values_button').on('click', function() {
            save_field_input_values('no');
        });



        // on page click hide all focused els
        $(document).on('click', '.field-container', function (e) {
            if (!$(e.target).is('.field-div *')) {
                $('.focused').hide();
                field_list();
            }
        });

        $('.modal').on('hide.bs.modal', function () {
            reset_field_properties();
        });

        $('.field-div').not('.disabled').off('click').on('click', function () {

            let group_id = $(this).data('group-id');
            // checkboxes and radios never get highlighted
            if ($(this).data('type') != 'checkbox' && $(this).data('type') != 'radio') {

                $('.field-div').removeClass('active');
                $(this).addClass('active');

                if(!$(this).hasClass('date')) {
                    $(this).find('.modal').modal('show');
                    $('.modal-backdrop').appendTo($(this).closest('.field-div'));
                    // hide sidebar. not sure why it shows
                    $('.edit-file-sidebar').css({ 'z-index': '-1' });
                    $('.modal').on('hidden.bs.modal', function (e) {
                        $('.edit-file-sidebar').css({ 'z-index': '1' });
                    });
                }

            } else {

                if ($(this).data('type') == 'radio') {

                    $('.group_' + group_id).find('.data-div').html('');
                    // $('.group_' + group_id).find('input[type="radio"]').attr('checked', false);
                    // $(this).find('.data-div').next('input[type="radio"]').attr('checked', true);
                    $(this).find('.data-div').html('x');

                } else {

                    // FIXME: need to show/hide checkboxes on click
                    // have to add input value to checkboxes like radios
                    let check = $(this).find('input[type="checkbox"]');
                    let checked = check.attr('checked');
                    if (checked == false || checked == undefined) {
                        check.attr('checked', true);
                        $(this).find('.data-div').html('x');
                    } else {
                        check.attr('checked', false);
                        $(this).find('.data-div').html('');
                    }

                }

            }

        });


        $('.save-fillable-fields').on('click', function () {
            let type = $(this).data('type');
            let group_id = $(this).data('group-id');
            let form_div = $(this).parent('div.modal-footer').prev('div.modal-body').find('.form-div');
            fill_fields(type, group_id, form_div, 'save');
        });




        // if($(window).width() < 768) {
        //     $('.form-options-container').draggable({ axis: 'x' });
        // }

        // action buttons
        $(document).on('click', '#rotate_form_button:not(.disabled)', rotate_form);
        //$(document).on('click', '#to_pdf_button:not(.disabled)', to_pdf);
        $(document).on('click', '#show_edit_options_button:not(.disabled)', show_edit_options);
        $(document).on('click', '#save_edit_options_button', save_edit_options);
        $(document).on('click', '#cancel_edit_options_button', close_edit_options);

        $('.edit-form-action').on('click', function() {
            $('.edit-form-action').removeClass('active text-white').addClass('text-primary-dark');
            $(this).removeClass('text-primary-dark').addClass('active text-white');
        });

        // setTimeout(function() {
        //     if(window.location.href.match(/topdf/)) {
        //         to_pdf();
        //         window.history.pushState('data', 'Title', window.location.href.replace(/\/topdf/, ''));
        //         change_url();
        //     }
        // }, 1000);

    });

    function show_edit_options() {
        // show edit menu options and hide others
        $('.form-options-container').find('.form-options-div').not('.edit-options').hide();
        $('.edit-options').show();
        // disable filling for all fields
        $('.field-div').addClass('disabled');
        // remove user fields
        $('.user-field-div').remove();

        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        let Agent_ID = $('#Agent_ID').val();
        let file_id = $('#file_id').val();
        let file_type = $('#file_type').val();

        let formData = new FormData();
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('Agent_ID', Agent_ID);
        formData.append('file_id', file_id);
        formData.append('file_type', file_type);
        axios.post('/agents/doc_management/transactions/edit_files/get_user_fields', formData, axios_options)
        .then(function (response) {
            response.data.forEach(function(field) {

                let type = field['field_type'];
                let field_class = '';
                let field_html = '';
                let handles = ' \
                <div class="ui-resizable-handle ui-resizable-e focused"></div> \
                <div class="ui-resizable-handle ui-resizable-w focused"></div> \
                ';

                if(type == 'highlight') {
                    handles = ' \
                    <div class="ui-resizable-handle ui-resizable-nw focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-ne focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-se focused"></div> \
                    <div class="ui-resizable-handle ui-resizable-sw focused"></div> \
                    ';
                }

                if(type == 'user_text') {
                    field_class = 'user-field-div textline-div';
                    field_html = '<div class="textline-html"></div>';
                } else if (type == 'strikeout') {
                    field_class = 'user-field-div strikeout-div';
                    field_html = '<div class="strikeout-html"></div>';
                } else if (type == 'highlight') {
                    field_class = 'user-field-div highlight-div';
                    field_html = '<div class="highlight-html"></div>';
                }

                let field_div = ' \
                    <div class="field-div '+field_class+' group_'+field['group_id']+' ui-resizable ui-draggable" style="position: absolute; top: '+field['top_perc']+'%; left: '+field['left_perc']+'%; height: '+field['height_perc']+'%; width: '+field['width_perc']+'%;" id="field_'+field['group_id']+'" data-field-id="'+field['group_id']+'" data-group-id="'+field['group_id']+'" data-type="'+type+'" data-page="'+field['page']+'"> \
                        <div class="field-options-holder focused" style="right: 0px; display: none;"> \
                            <div class="btn-group" role="group" aria-label="Field Options"> <a type="button" class="btn btn-primary field-handle ui-draggable-handle"><i class="fal fa-arrows fa-lg"></i></a> <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> </div> \
                        </div> \
                        '+handles+' \
                        '+field_html+' \
                    </div> \
                ';

                let field_container = $('.file-view-page-container[data-id="'+field['page']+'"]').find('.field-container');
                field_container.append(field_div);
                setTimeout(function() {
                    $('.focused').hide();
                }, 500);
                set_hwxy($('#field_' + field['group_id']), field['group_id'], type);

                set_field_options(type, $('#field_' + field['group_id']), '', '', field_container);

            });
        })
        .catch(function (error) {

        });


        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image-bg', function (e) {
            add_field(e);
        });
    }

    function add_field(e) {

        let field_type = $('.edit-form-action.active').data('field-type');
        if(field_type) {

            let container = $(e.target.parentNode);

            // get bounding box coordinates
            let rect = e.target.getBoundingClientRect();
            // get target coordinates
            let x = parseInt(Math.round(e.clientX - rect.left));
            let y = parseInt(Math.round(e.clientY - rect.top));

            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y, container);

            let ele_h_perc = 1.3;

            // remove element height from top position
            y_perc = y_perc - ele_h_perc;

            // set w and h for new field
            let h_perc = 1.3;
            let w_perc = 15;
            h_perc = parseFloat(h_perc);
            w_perc = parseFloat(w_perc);

            // create unique id for field
            let id = Date.now();

            //create field and attach to container
            let field = field_html(h_perc, w_perc, x_perc, y_perc, id, id, $('#active_page').val(), field_type);

            // hide all handles and buttons
            //$('.focused').hide();

            // append new field
            $(container).append(field);

            set_hwxy($('#field_' + id), id, field_type);

            keep_in_view($('#field_' + id), w_perc, x_perc, y_perc, field_type);

            set_field_options(field_type, $('#field_' + id), id, rect, container);

        }
    }

    function set_field_options(field_type, ele, id, rect, container) {

        ele.on('click', function (e) {

            if (e.target === this) {
                e.stopPropagation();
            }
            $('.focused').hide();
            ele.find('.focused').show();
            let group_id = ele.data('group-id');
            let x_pos = ele.position().left;
            let doc_width = $('#file_viewer').width();
            let ele_pos = {
                left: '0px'
            }
            if(x_pos > doc_width / 2) {
                ele_pos = {
                    right: '0px'
                }
            }
            ele.find('.field-options-holder').css(ele_pos);
            set_hwxy(ele, group_id, field_type);

        });

        let handles = {
            'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
        };
        if(field_type == 'highlight') {
            handles = {
                'nw': '.ui-resizable-nw', 'ne': '.ui-resizable-ne', 'se': '.ui-resizable-se', 'sw': '.ui-resizable-sw'
            }
        }
        ele.resizable({
            containment: container,
            handles: handles,
            stop: function (e, ui) {
                let resized_ele = $(e.target);
                setTimeout(function() {
                    set_hwxy(resized_ele, '', field_type);
                }, 500);

            }
        });

        ele.draggable({
            containment: container,
            handle: '.field-handle',
            cursor: 'grab',
            stop: function (e, ui) {
                let dragged_ele = $(e.target);
                let dragged_x = ui.position.left;
                let dragged_y = ui.position.top;
                let dragged_x_perc = pix_2_perc_xy('x', dragged_x, container);
                let dragged_y_perc = pix_2_perc_xy('y', dragged_y,container);
                let dragged_h = dragged_ele.height();
                let dragged_w = dragged_ele.width();
                //let dragged_h_perc = pix_2_perc_hw('height', dragged_h, container);
                let dragged_w_perc = pix_2_perc_hw('width', dragged_w, container);
                //let dragged_group_id = dragged_ele.data('group-id');


                setTimeout(function() {
                    set_hwxy(dragged_ele, '', field_type);
                    keep_in_view(dragged_ele, dragged_w_perc, dragged_x_perc, dragged_y_perc, field_type);
                }, 500);

            }
        });


        // hide all handles and buttons when another container is selected
        $('.field-select-container').on('click', function (e) {
            $('.focused').hide();
        });

        // remove field
        $('.remove-field').off('click').on('click', function () {
            $(this).closest('.field-div').remove();

            setTimeout(function () {
                field_list();
            }, 500);
        });



    }

    function field_html(h_perc, w_perc, x_perc, y_perc, id, group_id, page, type) {

        let field_class = '';
        let field_html = '';
        let handles = ' \
        <div class="field-handle ui-resizable-handle ui-resizable-e focused"></div> \
        <div class="field-handle ui-resizable-handle ui-resizable-w focused"></div> \
        ';

        if(type == 'highlight') {
            handles = ' \
            <div class="field-handle ui-resizable-handle ui-resizable-nw focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-ne focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-se focused"></div> \
            <div class="field-handle ui-resizable-handle ui-resizable-sw focused"></div> \
            ';
        }

        if(type == 'user_text') {
            field_class = 'user-field-div textline-div standard';
            field_html = '<div class="textline-html"></div>';
        } else if (type == 'strikeout') {
            field_class = 'user-field-div strikeout-div standard';
            field_html = '<div class="strikeout-html"></div>';
        } else if (type == 'highlight') {
            field_class = 'user-field-div highlight-div standard';
            field_html = '<div class="highlight-html"></div>';
        }

        return ' \
        <div class="field-div new '+ field_class + ' active group_' + group_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;" id="field_' + id + '" data-field-id="' + id + '" data-group-id="' + group_id + '" data-type="' + type + '" data-page="' + page + '"> \
            <div class="field-options-holder focused"> \
                <div class="btn-group" role="group" aria-label="Field Options"> \
                    <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                    <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
                </div> \
            </div> \
            '+ handles + ' \
            '+ field_html + ' \
        </div> \
        ';
    }

    function set_hwxy(ele, group_id, type) {

        let container = ele.closest('.field-container');

        let h = ele.height();
        let w = ele.width();
        let h_perc = pix_2_perc_hw('height', h, container);
        let w_perc = pix_2_perc_hw('width', w, container);

        let x = ele.position().left;
        let y = ele.position().top;
        let x_perc = pix_2_perc_xy('x', x, container);
        let y_perc = pix_2_perc_xy('y', y,container);

        if (h_perc) {
            ele.data('hp', h_perc);
            ele.data('wp', w_perc);
            $('#field_' + type + '_heightp').val(h_perc);
            $('#field_' + type + '_widthp').val(w_perc);
        }
        if (x) {
            ele.data('x', x);
            ele.data('y', y);
            ele.data('xp', x_perc);
            ele.data('yp', y_perc);
            $('#field_' + type + '_x').val(x);
            $('#field_' + type + '_y').val(y);
            $('#field_' + type + '_xp').val(x_perc);
            $('#field_' + type + '_yp').val(y_perc);
        }
        if (group_id) {
            $('#field_' + type + '_group_id').val(group_id);
        }
        ele.data('page', ele.data('page'));

    }

    function keep_in_view(ele, w_perc, x_perc, y_perc, type) {
        // adjust fields if placed out of bounds
        let dist = '';
        let cw = 100;
        dist = 3;
        let cd_adjusted = cw;

        let x_pos = ele.position().left;
        let doc_width = $('#file_viewer').width();

        if(x_pos > (doc_width / 2)) {
            ele.find('.field-options-holder').css({ left: '' }).animate({ right: '0px' });
        } else {
            ele.find('.field-options-holder').css({ right: '' }).animate({ left: '0px' });
        }

        if (x_perc < dist) {
            ele.animate({ left: dist + '%' });
        }
        if ((x_perc + w_perc) > cd_adjusted) {
            let pos = cw - w_perc - dist;
            ele.animate({ left: pos + '%' });
        }

        if (y_perc < 2) {
            ele.animate({ top: '2%' });
        }

        setTimeout(function () {
            let group_id = ele.data('group-id');
            set_hwxy(ele, group_id, type);
        }, 1500);

    }

    function pix_2_perc_hw(type, px, container) {
        if (type == 'width') {
            return (100 * parseFloat(px / parseFloat(container.width())));
        } else {
            return (100 * parseFloat(px / parseFloat(container.height())));
        }
    }

    function pix_2_perc_xy(type, px, container) {
        if (type == 'x') {
            return (100 * parseFloat(px / parseFloat(container.width())));
        } else {
            return (100 * parseFloat(px / parseFloat(container.height())));
        }
    }

    function save_edit_options() {

        let data = [];

        if ($('.user-field-div').length > 0) {

            $('.user-field-div').each(function () {

                let field_data = {};
                field_data['Listing_ID'] = $('#Listing_ID').val();
                field_data['Contract_ID'] = $('#Contract_ID').val();
                field_data['Referral_ID'] = $('#Referral_ID').val();
                field_data['transaction_type'] = $('#transaction_type').val();
                field_data['Agent_ID'] = $('#Agent_ID').val();

                let type = $(this).data('type');
                field_data['file_type'] = $('#file_type').val();
                field_data['file_id'] = $('#file_id').val();
                field_data['field_id'] = $(this).data('field-id');

                let user_field_type = 'Text';
                if(type == 'strikeout') {
                    user_field_type = 'Strikeout';
                } else if(type == 'highlight') {
                    user_field_type = 'Highlight';
                }

                field_data['field_name'] = 'User '+user_field_type;
                field_data['field_name_display'] = 'User '+user_field_type;
                field_data['group_id'] = $(this).data('group-id');
                field_data['page'] = $(this).data('page');
                field_data['field_type'] = type;
                field_data['left'] = $(this).data('x');
                field_data['top'] = $(this).data('y');
                field_data['height'] = $(this).data('h');
                field_data['width'] = $(this).data('w');
                field_data['left_perc'] = $(this).data('xp');
                field_data['top_perc'] = $(this).data('yp');
                field_data['height_perc'] = $(this).data('hp');
                field_data['width_perc'] = $(this).data('wp');

                data.push(field_data);

            });

        } else {

            let field_data = {};
            field_data['Listing_ID'] = $('#Listing_ID').val();
            field_data['Contract_ID'] = $('#Contract_ID').val();
            field_data['Referral_ID'] = $('#Referral_ID').val();
            field_data['transaction_type'] = $('#transaction_type').val();
            field_data['Agent_ID'] = $('#Agent_ID').val();
            field_data['file_type'] = $('#file_type').val();
            field_data['file_id'] = $('#file_id').val();

            data.push(field_data);

        }

        data = JSON.stringify(data);

        let formData = new FormData();
        formData.append('data', data);
        axios.post('/agents/doc_management/transactions/edit_files/save_edit_options', formData, axios_options)
        .then(function (response) {
            location.reload();
        })
        .catch(function (error) {

        });
    }

    function close_edit_options() {
        $('#confirm_modal').modal().find('.modal-body').html('This will delete all changes made. Continue?');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Changes?');
        $('#confirm_button').on('click', function() {
            location.reload();
        });
    }

    function to_pdf() {

        global_loading_on('', '<div class="h3 text-white">Merging Fields, Creating and Saving PDF.</div> <div class="h3 mt-5 text-yellow">Please be patient, this process can take <br>5 - 10 seconds for each page.</div>');
        // fields that css will be changed during export to pdf. They will be reset after
        let els = '.data-div, .file-image-bg, .field-div, .data-div-radio-check';
        let styles;
        $(els).each(function () {
            let data_div = $(this);
            styles = ['color', 'font-size', 'line-height', 'font-weight', 'opacity', 'background', 'margin-left', 'padding-left', 'padding-top', 'display', 'position', 'font-family', 'letter-spacing', 'margin-top'];
            $.each(styles, function (index, style) {
                data_div.data(style, data_div.css(style));
            });
        });

        // set inline styles for PDF
        // system fields
        $('.data-div').not('.data-div-radio-check, .highlight, .strikeout').css({ 'font-size': '.9rem', 'color': 'blue', 'padding-left': '5px', 'padding-top': '3px', 'font-family': 'Arial', 'letter-spacing': '0.03rem' });
        $('.data-div-checkbox').css({ 'margin-left': '1px', 'color': '#000', 'font-size': '1.4em', 'line-height': '80%', 'font-weight': 'bold' });
        $('.data-div-radio').css({ 'margin-left': '2px', 'color': '#000', 'font-size': '1.4em', 'line-height': '90%', 'font-weight': 'bold' });
        // remove background
        $('.file-image-bg').css({ opacity: '0.0' });
        $('.field-div').css({ background: 'none' });

        // user fields
        $('.data-div.highlight').css({ background: 'yellow', opacity: '0.5', width: '100%', height: '100%' });
        $('.data-div.strikeout').css({ width: '100%', height: '2px', background: 'black', display: 'block', position: 'relative', 'margin-top': '7px' });


        let file_id = $('#file_id').val();
        let file_name = $('#file_name').val();
        let file_type = $('#file_type').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        // remove datepicker html, datepicker input, background img, modals, left over input fields
        let elements_remove = '.qs-datepicker-container, .field-datepicker, .file-image-bg, .fillable-field-input, .modal';

        let formData = new FormData();

        // get html from all pages to add to pdf layer
        let c = 0;
        $('.file-view-page-container').each(function () {
            c += 1;
            let container = $(this);
            let page_html = container.clone();
            page_html.find(elements_remove).remove();
            page_html = page_html.wrap('<div>').parent().html();

            formData.append('page_' + c, page_html);
        });

        formData.append('page_count', c);
        formData.append('file_id', file_id);
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

        }, 300);

        axios_options['header'] = { 'content-type': 'multipart/form-data' };
        axios.post('/agents/doc_management/transactions/edit_files/convert_to_pdf', formData, axios_options)
            .then(function (response) {

                global_loading_off();
                toastr['success']('Document Saved Successfully');
            })
            .catch(function (error) {

                });



    }

    function rotate_form() {
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
        axios.post('/agents/doc_management/transactions/edit_files/rotate_document', formData, axios_options)
        .then(function (response) {
            setTimeout(function() {
                location.reload();
            }, 500);
        })
        .catch(function (error) {

        });

    }

    function save_field_input_values(on_load) {

        let field_data = [];

        if($('.fillable-field-input').not('div.fillable-field-input').length > 0) {

            $('.fillable-field-input').not('div.fillable-field-input').each(function () {
                let input_value = '';
                let input_id = $(this).attr('id');
                let file_id = $('#file_id').val();
                let file_type = $('#file_type').val();
                let common_name = $(this).data('common-name');
                let Listing_ID = $('#Listing_ID').val();
                let Contract_ID = $('#Contract_ID').val();
                let Referral_ID = $('#Referral_ID').val();
                let transaction_type = $('#transaction_type').val();
                let Agent_ID = $('#Agent_ID').val();
                if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {
                    if ($(this).is(':checked')) {
                        input_value = 'checked';
                    }
                } else {
                    input_value = $(this).val();
                }

                field_data.push({
                    input_id: input_id,
                    input_value: input_value,
                    file_id: file_id,
                    file_type: file_type,
                    common_name: common_name,
                    Listing_ID: Listing_ID,
                    Contract_ID: Contract_ID,
                    Referral_ID: Referral_ID,
                    transaction_type: transaction_type,
                    Agent_ID: Agent_ID
                });
            });
            axios.post('/agents/doc_management/transactions/edit_files/save_field_input_values', field_data, axios_options)
                .then(function (response) {
                    if(on_load == 'no') {
                        to_pdf();
                    }
                })
                .catch(function (error) {

                });

        } else {

            toastr['warning']('Nothing to save');

        }

    }

    function fill_fields(type, group_id, form_div, fill_type) {

        if (type == 'number') {

            let input = form_div.find('.fillable-field-input');

            let num = '';
            if (input.val() != '') {
                num = parseInt(input.val());
            }
            // add values to data-div for each field in group
            $('.group_' + group_id).each(function () {
                // add values to all inputs in group
                $(this).find('.fillable-field-input').val(num).data('default-value', num)
                // only the written fields will be split.
                let subtype = $(this).data('number-type');
                let data_div = $(this).find('.data-div');
                if (input.val() == '') {
                    let data_div = $(this).find('.data-div');
                    data_div.html('');
                } else {
                    if (subtype == 'numeric') {
                        data_div.html(global_format_number(num));
                    } else {
                        split_lines(group_id, writtenNumber(num));
                    }
                }
            });



        } else if (type == 'address') {
            // get inputs array
            let inputs = form_div.find('.fillable-field-input');
            // create labels and names from array
            let address_labels = [];
            let address_names = [];
            inputs.each(function () {
                address_labels.push($(this).data('type'));
                address_names.push($(this).data('address-type'));
            });
            // add values for inputs and match with name/label
            let address_values = [];
            $.each(address_labels, function (index, address_label) {
                inputs.each(function () {
                    if ($(this).data('type') == address_label) {
                        address_values.push($(this).val());
                    }
                });
            });

            $('.group_' + group_id).each(function () {

                let group = $(this);
                let address_type = $(this).data('address-type');

                group.find('.fillable-field-input').each(function () {
                    let input = $(this);
                    $.each(address_labels, function (index, address_label) {
                        if (input.data('type') == address_label) {
                            input.val(address_values[index]).data('default-value', address_values[index]);
                        }
                    });
                });

                $.each(address_names, function (index, address_name) {
                    if (address_type != 'full') {
                        if (group.data('address-type') == address_name) {
                            group.find('.data-div').html(address_values[index]);
                        }
                    } else {
                        let full_address = address_values[0] + ' ' + address_values[1] + ' ' + address_values[2] + ' ' + address_values[3];
                        group.find('.data-div').html(full_address);
                        split_lines(group_id, full_address);
                    }
                });

            });

        } else if (type == 'name') {

            let inputs = form_div.find('.fillable-field-input');

            // get all input labels from data-type
            let name_labels = [];
            let name_names = [];
            inputs.each(function () {
                name_labels.push($(this).data('type'));
                name_names.push($(this).data('name-type'));
            });

            let name_values = [];
            // get values for each label
            $.each(name_labels, function (index, name_label) {
                inputs.each(function () {
                    if ($(this).data('type') == name_label) {
                        name_values.push($(this).val());
                    }
                });
            });

            $('.group_' + group_id).each(function () {

                let group = $(this);
                let name_type = $(this).data('name-type');

                let name1 = $(this).find('.fillable-field-input').eq(0).val();
                let name2 = $(this).find('.fillable-field-input').eq(1).val();
                let names = name1;

                if (name2 != undefined && name2 != '') {
                    names = names + ', ' + name2;
                }

                if (names && typeof names !== 'undefined') {

                    // group.find('.data-div').html(names);
                    // split_lines(group_id, names);

                    group.find('.fillable-field-input').each(function () {
                        let inputs = $(this);
                        $.each(name_labels, function (index, name_label) {
                            if (inputs.data('type') == name_label) {
                                inputs.val(name_values[index]).data('default-value', name_values[index]);
                            }
                        });
                    });

                    $.each(name_names, function (index, name_name) {
                        if (name_type != 'both') {
                            if (group.data('name-type') == name_name) {
                                group.find('.data-div').html(name_values[index]);
                            }
                        } else {
                            //let full_names = name_values[0]+' '+name_values[1];
                            group.find('.data-div').html(names);
                            split_lines(group_id, names);
                        }
                    });

                }
            });


        } else if (type == 'textline') {

            let textarea = form_div.find('.fillable-field-input');
            let text = textarea.val();
            textarea.data('default-value', text);
            split_lines(group_id, text);

        } else if (type == 'date') {

            let input = form_div.find('.fillable-field-input');
            input.data('default-value', input.val());
            $('.group_' + group_id).find('.data-div').html(input.val());

        } else if (type == 'radio') {

            let input = form_div.find('.fillable-field-input');
            if (input.is(':checked')) {
                input.data('default-value', 'checked');
                input.prev('.data-div').html('x');
            }

        } else if (type == 'checkbox') {

            let input = form_div.find('.fillable-field-input');
            if (input.is(':checked')) {
                input.data('default-value', 'checked');
                input.prev('.data-div').html('x');
            }

        } else if (type == 'user_text') {

            let textarea = form_div.find('.fillable-field-input');
            let text = textarea.val();

            textarea.data('default-value', text);
            form_div.closest('.field-div').find('.data-div').html(text);

        }

        if (fill_type == 'save') {
            $('.modal').modal('hide');
        }
    }

    function split_lines(group_id, text) {

        text = text.trim();
        //let str_len = text.length;
        let field_type = $('.group_' + group_id).data('type');

        // split value between lines
        if ($('.group_' + group_id).not('[data-number-type="numeric"]').length == 1) {
            if (field_type == 'number') {
                $('.group_' + group_id + '[data-number-type="written"]').first().find('.data-div').html(text);
            } else {
                $('.group_' + group_id).first().find('.data-div').html(text);
            }

        } else {

            $('.group_' + group_id).not('[data-number-type="numeric"]').find('.data-div').html('');
            $('.group_' + group_id).not('[data-number-type="numeric"]').each(function () {
                // if there is still text left over
                if (text != '') {

                    let width = String(Math.ceil($(this).width()));
                    let text_len = text.length;
                    let max_chars = width * .12;
                    if (text_len > max_chars) {
                        let section = text.substring(0, max_chars);
                        let end = section.lastIndexOf(' ');
                        let field_text = text.substring(0, end);
                        $(this).find('.data-div').html(field_text);
                        let start = end + 1;
                        text = text.substring(start);
                    } else {
                        $(this).find('.data-div').html(text);
                        text = '';
                    }
                }
            });

        }
    }

    function field_list() {
        $('.field-list-container').html('');
        $('.file-view-page-container').each(function () {

            let page_number = $(this).data('id');

            $('.field-list-container').append('<div class="page-container" id="page_container_' + page_number + '"></div>');

            let page_container = $('#page_container_' + page_number);

            page_container.append('<div class="font-weight-bold text-white bg-primary p-1 pl-2 mb-2">Page ' + page_number + '</div>');

            // get unique group ids
            let group_ids = [];

            $(this).find('.field-div').each(function () {
                group_ids.push($(this).data('group-id'));
            });
            group_ids = group_ids.filter(global_filter_array);
            // get all field names and add to field list
            $.each(group_ids, function (index, group_id) {
                let group = $('.group_' + group_id);
                let type = group.data('type');
                let order = Math.ceil(group.data('y'));
                let name = '';
                if (group.data('type') == 'radio') {
                    group.each(function () {
                        name = $(this).data('customname');
                        page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">' + name + '</a></div>');
                    });
                } else if (group.data('type') == 'checkbox') {
                    group.each(function () {
                        name = 'Checkbox';
                        page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">' + name + '</a></div>');
                    });
                } else {

                    name = group.data('customname');
                    if (group.data('commonname') != undefined && group.data('commonname') != '') {
                        name = group.data('commonname');
                    }

                    if (name == undefined || name == 'undefined' || name == '' || name == 'User Field') {
                        if(type == 'user_text') {
                            page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">User Text Field</a></div>');
                        }
                    } else {
                        page_container.append('<div class="mb-1 border-bottom border-primary field-list-div" data-order="' + order + '"><a href="javascript: void(0)" class="field-list-link ml-3" data-group-id="' + group_id + '" data-type="' + type + '">' + name + '</a></div>');
                    }

                }

            });

            let fields = page_container.find('.field-list-div');
            fields.sort(function(a, b){
                return $(a).data('order')-$(b).data('order')
            });


            page_container.append(fields);

            $('.field-list-link').off('click').on('click', function (e) {
                //e.stopPropagation();
                let group_id = $(this).data('group-id');
                let type = $(this).data('type');
                let ele = $('.field-div[data-group-id="' + group_id + '"]').first();

                if (type == 'date') {
                    setTimeout(function() {
                        ele.find('.field-datepicker').focus().trigger('click').next('.qs-datepicker-container').removeClass('qs-hidden');
                    }, 500);
                } else {
                    if (type != 'checkbox' && type != 'radio') {
                        ele.find('.modal').modal('show');
                    }
                }
                $('.field-div').removeClass('active');
                ele.addClass('active');

                let container = $('#file_viewer');
                let scrollTo = $('#field_' + group_id).first();
                container.animate({
                    scrollTop: (scrollTo.offset().top - container.offset().top + container.scrollTop()) - 200
                });
                setTimeout(function() {
                    if (type != 'checkbox' && type != 'radio') {
                        ele.trigger('click');
                    }
                    //$('.modal.show').find('input').eq(0).trigger('click').focus().next('label').addClass('active');
                }, 200);

            });

        });

    }

    function reset_field_properties() {
        // reset name fields
        $('.form-div').each(function () {
            $(this).find('input, textarea').each(function () {
                $(this).val($(this).data('default-value'));
            });
        });
        $('.field-div').removeClass('active');
    }
} */
