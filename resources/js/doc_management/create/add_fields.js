if (document.URL.match(/create\/add_fields/)) {

    /* TODO:
    set all default values
    restore default values when not saved
    on add field - get common/custom name and other details from group

    delete docs_create_field_types table
    remove FieldTypes model
    delete docs_create_field_inputs table
    remove FieldInputs model
    delete docs_transactions_field_inputs_values table
    remove UserFieldsValues model
    */
    $(function() {

        // set field options on load
        if ($('.field-div').length > 0) {

            $('.field-div').each(function () {
                set_field_options($(this).closest('.field-div-container'));
            });
            $('.field-div').each(function () {
                let group_id = $(this).data('group-id');
                if($('.group_' + group_id).length > 1) {
                    $('.group_' + group_id).removeClass('standard').addClass('group');
                }
            });

        }

        // Show active field
        $(document).on('click', '.field-wrapper', function () {
            $('.field-wrapper').removeClass('active');
            $(this).addClass('active');
            $('#active_field').val($(this).data('type'));
        });

        // open field when clicked
        $(document).on('click', '.field-div', function() {
            hide_active_field();
            $(this).closest('.field-div-container').addClass('show').find('.field-div-options').show();
        });

        // remove field
        $(document).on('click', '.remove-field', function() {
            $(this).closest('.field-div-container').remove();
        });

        // hide all active fields when page clicked
        $('.field-select-container div, .file-image').on('click', function() {
            hide_active_field();
        });

        $('#save_add_fields').off('click').on('click', save_add_fields);

        $('.delete-page-button').on('click', delete_page);

        // highlight active thumb when clicked and scroll into view
        $('.file-view-thumb-container').on('click', function (e) {
            $('.file-view-thumb-container').removeClass('active');
            $(this).addClass('active');
            let id = $(this).data('id');
            $('#active_page').val(id);
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

        // add new field on dblclick
        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image', function (e) {

            let field_category = $('#active_field').val();
            let active_page = $('#active_page').val();

            // only if a field has been selected
            if (field_category != '') {

                hide_active_field();

                let container = $(e.target.parentNode);

                let coords = set_and_get_field_coordinates(e, null, field_category, 'no');
                let x_perc = coords.x;
                let y_perc = coords.y;
                let h_perc = coords.h;
                let w_perc = coords.w;

                // create unique id for field
                let field_id = Date.now();

                //create field and attach to container
                let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, field_id, active_page, field_category);

                // append new field
                container.append(field);

                let ele = $('.field-div-container.show');

                // run this again in case it was placed out of bounds
                set_and_get_field_coordinates(null, ele, field_category, 'no');

                set_field_options(ele);

                ele.find('.field-div-options').show();

            }
        });

        function save_add_fields() {

            //console.log('running save_add_fields');

            if($('.field-div.error').length > 0) {

                let field_div = $('.field-div.error:first');
                let field_div_id = field_div.prop('id');

                document.getElementById(field_div_id).scrollIntoView();
                $('.file-view').scrollTop($('.file-view').scrollTop() - 125);
                field_div.trigger('click').parent().find('.save-field-properties-button').trigger('click');

                toastr['error']('All Fields Must Be Complete');

                return false;
            }

            $('#save_add_fields').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving');

            let data = [];

            if ($('.field-div').length > 0) {

                $('.field-div').each(function () {

                    let field_div = $(this);
                    let field_div_container = field_div.closest('.field-div-container');
                    let field_id = field_div.data('field-id');
                    let field_category = field_div.data('field-category');

                    let common_field_name = field_div_container.find('.common-field-name').val() ?? null;
                    let common_field_id = field_div_container.find('.common-field-id').val() ?? null;
                    let common_field_type = field_div_container.find('.common-field-type').val() ?? null;
                    let common_field_sub_group_id = field_div_container.find('.common-field-sub-group-id').val() ?? null;
                    let custom_field_name = field_div_container.find('.custom-field-name').val();
                    let number_type = field_div_container.find('.number-type:checked').val() ?? null;

                    field_data = {
                        'file_id': $('#file_id').val(),
                        'field_id': field_id,
                        'group_id': field_div.data('group-id'),
                        'field_category': field_category,
                        'common_field_type': common_field_type,
                        'common_field_id': common_field_id,
                        'common_field_sub_group_id': common_field_sub_group_id,
                        'common_field_name': common_field_name,
                        'custom_field_name': custom_field_name,
                        'number_type': number_type,
                        'page': field_div.data('page'),
                        'left_perc': field_div.data('xp'),
                        'top_perc': field_div.data('yp'),
                        'height_perc': field_div.data('hp'),
                        'width_perc': field_div.data('wp')
                    }

                    data.push(field_data);

                });

            } else {

                let field_data = {};
                field_data['file_id'] = $('#file_id').val();

                data.push(field_data);

            }

            $.ajax({
                type: 'POST',
                url: '/doc_management/save_add_fields',
                data: { data: JSON.stringify(data) },
                success: function (response) {
                    if(response.error == 'published') {
                        $('#modal_danger').modal('show').find('.modal-body').html('This form has already been published. It can no longer be edited');
                    } else {
                        toastr['success']('Fields Successfully Saved');
                    }

                    $('#save_add_fields').html('<i class="fad fa-save mr-2"></i> Save');
                }
            });

        }

        function set_field_options(ele) {

            let field_category = ele.find('.field-div').data('field-category');
            let container = ele.closest('.field-container');

            if(field_category != 'checkbox') {
                get_edit_properties_html(ele.find('.field-div'));
            }

            let handles = {
                'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
            };
            let aspect_ratio = '';
            // not resizable
            if (field_category == 'checkbox' || field_category == 'radio') {
                aspect_ratio = '4 / 4';
            }

            // make field resizable
            if (field_category != 'checkbox' && field_category != 'radio') {
                ele.resizable({
                    containment: container,
                    handles: handles,
                    aspectRatio: aspect_ratio,
                    stop: function (e, ui) {
                        let resized_ele = $(e.target);
                        set_and_get_field_coordinates(null, resized_ele, field_category, 'yes');
                    }
                });

                ele.find('.field-div-options').hide();
            }

            // make field draggable
            ele.draggable({
                containment: container,
                handle: '.field-handle',
                cursor: 'grab',
                stop: function (e, ui) {
                    let dragged_ele = $(e.target);
                    set_and_get_field_coordinates(null, dragged_ele, field_category, 'yes');
                }
            });

            // add additional fields to group
            if (field_category != 'date') {
                // add items to group
                ele.find('.add-field-button').off('click').on('click', function () {
                    add_item(ele, field_category);
                });
            }

            // adjust y_perc with slider
            $('.mini-slider-option').off('click').on('click', function () {
                mini_slider($(this));
            });

        }

        function field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, page, field_category) {

            //console.log('running field_html');

            let handles = ' \
            <div class="ui-resizable-handle ui-resizable-e focused field-div-options"></div> \
            <div class="ui-resizable-handle ui-resizable-w focused field-div-options"></div> \
            ';
            let field_class = '';
            let field_data = '';
            let hide_add_option = '';

            let position = x_perc > 50 ? 'right' : '';
            let field_div_properties = ' \
            <div class="field-div-properties bg-white border shadow pt-0 pb-2 px-2  '+position+'"> \
                <div class="field-div-properties-html"></div> \
            </div> \
            ';

            if (field_category == 'textline' || field_category == 'number') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
            } else if (field_category == 'radio') {
                handles = '';
                field_class = 'radio-div standard';
                field_data = '<div class="radio-html"></div>';
            } else if (field_category == 'checkbox') {
                handles = '';
                field_class = 'checkbox-div standard';
                field_data = '<div class="checkbox-html"></div>';
                field_div_properties = '';
            } else if (field_category == 'date') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
                hide_add_option = 'hidden';
            }

            let field_div_html = ' \
            <div class="field-div-container show" id="field_container_' + field_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;"> \
                <div class="field-div '+ field_class + ' group_' + group_id + '" id="field_' + field_id + '" data-field-id="' + field_id + '" data-group-id="' + group_id + '" data-field-category="' + field_category + '" data-page="' + page + '"> \
                    <div class="field-name-display-div"></div> \
                </div> \
                <div class="field-div-options"> \
                    <a type="button" href="javascript: void(0)" class="btn btn-danger mx-0 remove-field '+position+'"><i class="fal fa-ban fa-lg mr-1"></i> Delete</a> \
                    <div class="d-flex justify-content-start field-div-controls '+position+'"> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="up"><i class="fal fa-arrow-up fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="down"><i class="fal fa-arrow-down fa-lg"></i></a> \
                        <a type="button" href="javascript: void(0)" class="btn btn-primary mx-0 add-field-button '+hide_add_option+'"><i class="fal fa-plus fa-lg"></i></a> \
                    </div> \
                    '+field_div_properties+' \
                </div> \
                '+handles+' \
                '+field_data+' \
            </div> \
            ';

            return field_div_html;
        }

        function get_edit_properties_html(ele) {

            //console.log('running get_edit_properties_html');

            let field_id = ele.data('field-id');
            let group_id = ele.data('group-id');
            let field_category = ele.data('field-category');

            //let common_name = $('.group_' + group_id).data('commonname');
            //let custom_name = $('.group_' + group_id).data('customname');

            axios.get('/doc_management/get_edit_properties_html', {
                params: {
                    file_id: $('#file_id').val(),
                    field_id: field_id,
                    field_category: field_category,
                    group_id: group_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                let edit_properties_html = response.data;
                let field_div_container = $('#field_container_'+field_id);
                field_div_container.find('.field-div-properties-html').html(edit_properties_html);

                // clear field-error from field-div
                field_div_container.find('.field-div').removeClass('error');

                // dropdowns for common fields
                field_div_container.find('.dropdown').not('.dropdown-input').on('mouseenter', function() {
                    $(this).addClass('show').find('.dropdown-menu').first().addClass('show');
                })
                .on('mouseleave', function() {
                    $(this).removeClass('show').find('.dropdown-menu').first().removeClass('show');
                });
                field_div_container.find('.dropdown-input').on('click', function(e) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    $(this).addClass('show').find('.dropdown-menu').first().addClass('show');
                }).on('mouseleave', function() {
                    $(this).removeClass('show').find('.dropdown-menu').first().removeClass('show');
                });

                $('.save-field-properties-button').on('click', function() {
                    save_edit_properties($(this));
                });

                // when the common field name is clicked add to input and add ID to hidden input
                field_div_container.find('.field-name').on('click', function() {

                    let field_name = $(this).text();
                    let field_id = $(this).data('field-id');
                    let field_type = $(this).data('field-type');
                    let field_sub_group_id = $(this).data('field-sub-group-id');

                    // clear custom name value
                    field_div_container.find('.custom-field-name').val('');
                    // add common field values
                    field_div_container.find('.common-field-name-input, .common-field-name').val(field_name);
                    field_div_container.find('.common-field-id').val(field_id);
                    field_div_container.find('.common-field-type').val(field_type);
                    field_div_container.find('.common-field-sub-group-id').val(field_sub_group_id);
                    // hide dropdown
                    setTimeout(function() {
                        $('.dropdown-input, .dropdown-menu').removeClass('show');
                    }, 100);

                });

                // clear common name if custom name entered
                field_div_container.find('.custom-field-name').on('change', function() {
                    if($(this).val() != '') {
                        field_div_container.find('.common-field-name-input, .common-field-name').val('');
                        field_div_container.find('.common-field-id').val('');
                        field_div_container.find('.common-field-type').val('');
                        field_div_container.find('.common-field-sub-group-id').val('');
                    }
                });

                // show list of custom names already added
                field_div_container.find('.field-data-name').on('keyup', show_custom_names);

                // clear button on dropdown to remove value from input
                $('.clear-common-field-name').on('click', function() {
                    $(this).closest('.edit-properties-div').find('.common-field-name-input, .common-field-name').val('');
                    setTimeout(function() {
                        $('.dropdown-input, .dropdown-menu').removeClass('show');
                    }, 200);
                });

                // close field options button
                $('.close-field-options').on('click', function() {
                    hide_active_field();
                });

                // capitalize all first letters
                $('.custom-field-name').on('keyup', function() {
                    $(this).val(ucwords($(this).val()));
                });

                //ele.find('.form-select.field-data-name').val(common_name).data('default-value', common_name);
                //ele.find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name);

                //form_elements();

            })
            .catch(function (error) {

            });
        }

        function save_edit_properties(button) {

            //console.log('running save_edit_properties');

            let field_div_container = button.closest('.field-div-container');
            let group_id = button.data('group-id');
            let field_id = button.data('field-id');
            let field_category = button.data('field-category');

            let common_name = field_div_container.find('.common-field-name').val();
            let custom_name = field_div_container.find('.custom-field-name').val();

            if(common_name == '' && custom_name == '') {
                field_div_container.find('.alert.alert-danger').removeClass('hide').addClass('show').find('.error-message').html('You must enter either the<br>Shared Field Name or Custom Name');
                field_div_container.find('input').on('change', function() {
                    field_div_container.find('.alert.alert-danger').removeClass('show').addClass('hide');
                });
                return false;
            }

            if(field_category == 'number') {
                if(field_div_container.find('.number-type:checked').length == 0) {
                    field_div_container.find('.alert.alert-danger').removeClass('hide').addClass('show').find('.error-message').html('You must select either<br>Written or Numeric');
                    field_div_container.find('input').on('change', function() {
                        field_div_container.find('.alert.alert-danger').removeClass('show').addClass('hide');
                    });
                    return false;
                }
            }

            let common_field_id = field_div_container.find('.common-field-id').val();
            let common_field_type = field_div_container.find('.common-field-type').val();
            let common_field_sub_group_id = field_div_container.find('.common-field-sub-group-id').val();


            if (field_category != 'checkbox') {

                // set default values of field
                field_div_container.find('input').not('[type=radio]').each(function () {
                    $(this).data('default-value', $(this).val());
                });

                // set values and default values of any other fields in group.
                $('.group_' + group_id).each(function () {

                    $(this).data('commonname', common_name).data('customname', custom_name).removeClass('error');

                    let group_div_container = $(this).closest('.field-div-container');
                    // radio will always be a custom name
                    if (field_category != 'radio') {
                        // add field name to display
                        group_div_container.find('.field-name-display-div').text(custom_name+common_name);
                        group_div_container.find('.common-field-name-input, .common-field-name').val(common_name).data('default-value', common_name);
                        group_div_container.find('.common-field-id').val(common_field_id).data('default-value', common_field_id);
                        group_div_container.find('.common-field-type').val(common_field_type).data('default-value', common_field_type);
                        group_div_container.find('.common-field-sub-group-id').val(common_field_sub_group_id).data('default-value', common_field_sub_group_id);
                    }
                    group_div_container.find('.custom-field-name').val(custom_name).data('default-value', custom_name);

                });

                if(field_category == 'number') {
                    // set default values for number inputs
                    let number_type_radio = field_div_container.find('.number-type');
                    number_type_radio.data('default-value', '');
                    number_type_radio.filter(':checked').data('default-value', 'checked');
                    let number_type = number_type_radio.filter(':checked').val();

                    // update other number types to written if this is numeric. There will be one numeric and possibly multiple written
                    if (number_type == 'numeric') {
                        $('.group_' + group_id).each(function() {
                            if($(this).prop('id') != 'field_'+field_id) {
                                // uncheck all and clear default value
                                $(this).closest('.field-div-container').find('.number-type').prop('checked', false).data('default-value', '');
                                // set written field as checked and update default value
                                $(this).closest('.field-div-container').find('[value=written]').prop('checked', true).data('default-value', 'checked');
                            }
                        });
                    }

                }

            }

            field_div_container.find('.alert.alert-success').removeClass('hide').addClass('show');
            field_div_container.find('input').on('change', function() {
                field_div_container.find('.alert.alert-success').removeClass('show').addClass('hide');
            });

        }

        function add_item(ele, field_category) {

            //console.log('running add_item');

            hide_active_field();

            // assign group id for original field
            let group_id = ele.find('.field-div').data('group-id');

            // get original field input values
            let common_name, common_field_id, common_field_type, common_field_sub_group_id, custom_name, number_type;

            if (field_category != 'radio') {
                common_name = ele.find('.common-field-name').val();
                common_field_id = ele.find('.common-field-id').val();
                common_field_type = ele.find('.common-field-type').val();
                common_field_sub_group_id = ele.find('.common-field-sub-group-id').val();
            }
            custom_name = ele.find('.custom-field-name').val();

            if(field_category == 'number') {
                number_type = ele.find('.number-type:checked').val();
            }

            let coords = set_and_get_field_coordinates(null, ele, field_category, 'yes');
            let x_perc = coords.x;
            let y_perc = coords.y;
            let h_perc = coords.h;
            let w_perc = coords.w;
            // add spacing
            y_perc = y_perc + (h_perc * 1.5);

            if ((parseFloat(y_perc) + parseFloat(h_perc)) > 99) {
                toastr['error']('Field must be on the page');
                return false;
            }

            // create new id for new field in group
            field_id = Date.now();
            let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, $('#active_page').val(), field_category);
            // append new field
            ele.closest('.field-container').append(field);

            let new_ele = $('.field-div-container.show');

            set_and_get_field_coordinates(null, new_ele, field_category, 'no');
            set_field_options(new_ele);

            if (field_category != 'checkbox') {

                setTimeout(function() {
                    // assign group field_id to new field
                    new_ele.find('.field-div').data('group-id', group_id).addClass('group_' + group_id);
                    $('.group_' + group_id).removeClass('standard').addClass('group');

                    // add field values and default data values from other fields in group
                    if (field_category != 'radio') {
                        // add field name to display
                        new_ele.find('.field-name-display-div').text(custom_name+common_name);
                        new_ele.find('.common-field-name-input, .common-field-name').val(common_name).data('default-value', common_name);
                        new_ele.find('.common-field-id').val(common_field_id).data('default-value', common_field_id);
                        new_ele.find('.common-field-type').val(common_field_type).data('default-value', common_field_type);
                        new_ele.find('.common-field-sub-group-id').val(common_field_sub_group_id).data('default-value', common_field_sub_group_id);
                    }
                    new_ele.find('.custom-field-name').val(custom_name).data('default-value', custom_name);

                    if(field_category == 'number') {
                        if(number_type == 'numeric') {
                            // if a number and previous field is numeric this has to be written
                            new_ele.find('.number-type[value=written]').prop('checked', true).data('default-value', 'checked');
                        } else {
                            // if one in the group is numeric this will be written
                            if($('.group_' + group_id).closest('.field-div-container').find('.number-type[value=numeric]:checked').length > 0) {
                                new_ele.find('.number-type[value=written]').prop('checked', true).data('default-value', 'checked');
                            }
                        }
                    }
                    new_ele.find('.field-div-options').show();

                }, 200);

            }

        }

        function mini_slider(ele) {

            let dir = ele.data('direction');
            let operator = (dir == 'up') ? '-' : '+';
            let field_div_container = ele.closest('.field-div-container');
            let field_div = ele.closest('.field-div');
            let field_category = field_div.data('field-category');
            field_div_container.css({ top: operator + '=.05%' });
            // set new h,w,x,y after moving up and down
            setTimeout(function() {
                set_and_get_field_coordinates(null, field_div_container, field_category, 'yes');
            }, 10);

        }

        function set_and_get_field_coordinates(e, ele, field_category, existing) {

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
            if (field_category == 'radio' || field_category == 'checkbox') {
                ele_h_perc = 1.1;
            }
            if(e) {
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;
            }

            // set w and h for new field
            let h_perc, w_perc;
            if (field_category == 'radio' || field_category == 'checkbox') {
                h_perc = 1.1;
                w_perc = 1.45;
            } else {
                h_perc = 1.3;
                w_perc = existing == 'no' ? 15 : (ele.width() / ele.parent().width()) * 100;
            }
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

            setTimeout(function() {
                ele.find('.remove-field, .dropdown, .field-div-properties, .field-div-controls').removeClass('right');
                if(x_perc > 50) {
                    ele.find('.remove-field, .dropdown, .field-div-properties, .field-div-controls').addClass('right');
                }
            }, 300);

            return {
                h: h_perc,
                w: w_perc,
                x: x_perc,
                y: y_perc
            }

        }

        function hide_active_field() {

            // reset all fields to default values
            let field_div_container = $('.field-div-container.show');
            let field_div = field_div_container.find('.field-div');
            let field_category = field_div_container.find('.field-div').data('field-category');

            field_div_container.find('input[type="text"], input[type="hidden"]').each(function() {
                $(this).val($(this).data('default-value'));
            });

            if(field_category == 'number') {
                field_div_container.find('input[type="radio"]').each(function() {
                    if($(this).data('default-value') == 'checked') {
                        $(this).prop('checked', true);
                    }
                });
            }

            field_div_container.removeClass('show').find('.field-div-options').hide();

            // add field-error if fields not complete
            let errors = 'no';

            let common_name = field_div_container.find('.common-field-name').val();
            let custom_name = field_div_container.find('.custom-field-name').val();

            if(common_name == '' && custom_name == '') {
                errors = 'yes';
            }

            if(field_category == 'number') {
                if(field_div_container.find('.number-type:checked').length == 0) {
                    errors = 'yes';
                }
            }

            field_div.removeClass('error');
            if(errors == 'yes') {
                field_div.addClass('error');
            }

        }

        function pix_2_perc_xy(type, px, container) {
            if (type == 'x') {
                return (100 * parseFloat(px / parseFloat(container.width())));
            } else {
                return (100 * parseFloat(px / parseFloat(container.height())));
            }
        }

        function show_custom_names() {
            let input = $(this);
            let field_div = input.closest('.field-div-container');

            if(input.val() != '') {

                axios.get('/doc_management/get_custom_names', {
                    params: {
                        val: input.val()
                    }
                })
                .then(function (response) {

                    field_div.find('.dropdown-results-div').html('');
                    response.data.custom_names.forEach(function (result) {
                        field_div.find('.dropdown-results-div').append('<a href="javascript: void(0)" class="list-group-item field-name-result">'+result['field_name_display']+'</a>');
                    });
                    field_div.find('.custom-name-results').show();

                    $('.field-name-result').on('click', function() {
                        let edit_div = $(this).closest('.field-div-container').find('.edit-properties-div');
                        edit_div.find('.form-input.field-data-name').val($(this).text());
                        edit_div.find('.custom-name-results').hide();
                    });

                })
                .catch(function (error) {

                });

            } else {
                field_div.find('.custom-name-results').hide();

            }

            $(document).on('mouseup', function (e) {
                if ($(e.target).closest('.custom-name-results').length  === 0) {
                    $('.custom-name-results').hide();
                }
            });
        }

        function delete_page() {

            //console.log('running delete_page');

            let page = $(this).data('page-number');
            let file_id = $(this).data('file-id');

            $('#page_div_'+page).next('.file-view-page-info').fadeOut('slow').remove();
            $('#page_'+page+', #page_div_'+page+', #thumb_'+page).fadeOut('slow').remove();

            let formData = new FormData();
            formData.append('page', page);
            formData.append('file_id', file_id);
            axios.post('/doc_management/delete_page', formData, axios_options)
            .then(function (response) {
                toastr['success']('Page Successfully Removed')
            })
            .catch(function (error) {

            });
        }


    });

}


