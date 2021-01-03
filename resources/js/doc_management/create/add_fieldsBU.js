if (document.URL.match(/create\/add_fields/)) {

    $(function() {

        // TODO run field_status() after any add or remove

        set_common_fields();
        /* setTimeout(function () {
            field_list();
        }, 200); */

        // Show active field
        $('.field-wrapper').on('click', function () {
            $('.field-wrapper').removeClass('active');
            $(this).addClass('active');
            $('#active_field').val($(this).data('type'));
        });

        // set field options on load
        if ($('.field-div').length > 0) {

            $('.field-div').each(function () {

                let group_id = $(this).data('group-id');
                let field_type = $(this).data('type');

                $(this).find('.field-properties').data('field-type', field_type);

                // add grouped class
                if ($('.group_' + group_id).length > 1) {
                    $('.group_' + group_id).removeClass('standard').addClass('group');
                };
                // hide add item on all fields of group but last
                $('.group_' + group_id).find('.add-item-container').hide();
                $('.group_' + group_id).find('.add-item-container').last().show();

                set_field_options(field_type, $(this));
                set_hwxy($(this), $(this).data('group-id'), field_type);

            });

            $('.focused').hide();

            field_status();
            setTimeout(function () {
                field_list();
                global_loading_off();
            }, 10);


        }

        $('#save_add_fields').off('click').on('click', save_add_fields);


        // on page double click add field
        // changed from just .file-view-page-container.active - adding .file-image prevents new field being created when double clicking in edit properties div
        //$('.file-view-page-container.active .file-image').on('dblclick', function (e) {
        $('#file_viewer').off('dblclick').on('dblclick', '.file-view-page-container.active .file-image', function (e) {

            // get container so easier to find elements
            let container = $(e.target.parentNode);

            let field_type = $('#active_field').val();

            // only if a field has been selected
            if (field_type != '') {

                // get bounding box coordinates
                let rect = e.target.getBoundingClientRect();
                // get target coordinates
                let x = parseInt(Math.round(e.clientX - rect.left));
                let y = parseInt(Math.round(e.clientY - rect.top));

                let x_perc = pix_2_perc_xy('x', x, container);
                let y_perc = pix_2_perc_xy('y', y, container);

                let ele_h_perc = 1.3;
                if (field_type == 'radio' || field_type == 'checkbox') {
                    ele_h_perc = 1.1;
                }
                // remove element height from top position
                y_perc = y_perc - ele_h_perc;

                // set w and h for new field
                let h_perc, w_perc;
                if (field_type == 'radio' || field_type == 'checkbox') {
                    h_perc = 1.1;
                    w_perc = 1.45;
                } else {
                    h_perc = $('#field_' + field_type + '_heightp').val();
                    //w_perc = $('#field_' + field_type + '_widthp').val();
                    w_perc = 10;
                }
                h_perc = parseFloat(h_perc);
                w_perc = parseFloat(w_perc);
                // let h = perc_2_pix('height', h_perc, container);
                // let w = perc_2_pix('width', w_perc, container);

                // hide all handles and buttons
                $('.focused').hide();

                // create unique id for field
                let field_id = Date.now();

                //create field and attach to container
                let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, field_id, $('#active_page').val(), field_type);

                // append new field
                $(container).append(field);

                $('.draggable').draggable({
                    handle: '.draggable-handle'
                });

                $('.field-div').removeClass('active');
                $('#field_' + field_id).addClass('active');

                set_hwxy($('#field_' + field_id), field_id, field_type);

                keep_in_view($('#field_' + field_id), w_perc, x_perc, y_perc, field_type);
                /* let field_div_x = $('#field_'+field_id).position().left;
                position_edit_properties_div(field_div_x); */

                $('#field_'+field_id).find('.focused').show();

                if(field_type != 'checkbox') {
                    get_edit_properties_html(field_id, field_id, field_type, rect, container, $('#field_'+field_id));
                }

            }
        });

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

        let show_field_name_running = 'no';
        // remove common name when custom name is type in
        $(document).on('change', '.field-data-name', function(e) {

            if(e.target.classList.contains('field-data-name')) {

                let select = $(this);
                if(show_field_name_running == 'no') {
                    show_field_name(select);
                }

                let field_div = select.closest('.field-div');
                let field_id = field_div.data('field-id');
                let edit_div = field_div.find('.edit-properties-div');
                let inputs_container = edit_div.find('.field-inputs-div');

                if(field_div.data('type') == 'address') {

                    add_address_inputs(field_id, inputs_container, select);
                    name_required(select.closest('.form-div'));

                } else if (field_div.data('type')  == 'name') {
                    add_name_inputs(field_id, inputs_container, select);
                }

            }
            return;

        });



        // on page click hide all focused els
        $(document).on('mousedown', '.file-view-container *', function (e) {

            // save edit properties for each field
            if(e.target.classList.contains('field-save-properties')) {

                let button = $(e.target);
                save_field_properties(button);

            // closed edit fields for all when close button clicked, clicked outside of edit field area or options at top clicked
            } else if(e.target.classList.contains('close-field-options') || e.target.classList.contains('field-wrapper') || e.target.closest('.field-wrapper')) {

                if($('.field-div.active').length > 0) {
                    $('.field-div.active').find('.focused').hide();
                    $('.field-div.active').find('.collapse').removeClass('show');
                    $('.field-div.active').removeClass('active');
                }

            } else if(e.target.classList.contains('remove-field') || e.target.closest('.remove-field')) {

                remove_field();

            } else if(e.target.classList.contains('field-name-result')) {


                let edit_div = $(this).closest('.field-div').find('.edit-properties-div');
                edit_div.find('.form-input.field-data-name').val($(this).text());
                edit_div.find('.custom-name-results').hide();
                e.stopPropagation();


            } else {

                if (!$(e.target).is('.field-div *')) {
                    $('.collapse').removeClass('show');
                }
                if (!$(e.target).is('.custom-name-results *')) {
                    $('.custom-name-results').hide();
                }
                /* if (!$(e.target).is('.edit-properties-container *')) {
                    $('.edit-properties-container').collapse('hide');
                } */
                if(!$(e.target).is('.mini-slider-div.active *')) {
                    $('.mini-slider-div.active').hide().removeClass('active');
                }

            }

        });





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

        function reset_field_properties(field_id) {

            //console.log('running reset_field_properties');

            // reset name fields
            $('#field_'+field_id).find('select, input').not('input.form-select-search-input, input.form-select-value-input').each(function () {
                $(this).val($(this).data('default-value'));
            });
            select_refresh();

        }

        function set_field_options(field_type, ele) {

            //console.log('running set_field_options');

            let field_id = ele.data('field-id');

            // get bounding box coordinates
            let rect = ele[0].getBoundingClientRect();
            let container = ele.closest('.field-container');

            let handles = {
                'e': '.ui-resizable-e', 'w': '.ui-resizable-w'
            };
            let aspect_ratio = '';
            if (field_type == 'checkbox' || field_type == 'radio') {
                aspect_ratio = '4 / 4';
            }

            ele.off('click').on('click', function (e) {

                if (e.target === e.currentTarget) {
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }


                //$('.edit-properties-container').removeClass('show');

                $('.focused').hide();
                ele.find('.focused').show();
                $('.field-div').removeClass('active');
                ele.addClass('active');
                //let group_id = ele.data('group-id');

                //set_hwxy(ele, group_id, field_type);

                /* let x = $('#field_'+field_id).position().left;
                position_edit_properties_div(x); */



            });

            if (field_type != 'checkbox' && field_type != 'radio') {
                ele.resizable({
                    containment: container,
                    handles: handles,
                    aspectRatio: aspect_ratio,
                    stop: function (e, ui) {
                        let resized_ele = $(e.target);
                        setTimeout(function() {
                            set_hwxy(resized_ele, '', field_type);
                        }, 500);

                    }
                });


            }

            ele.draggable({
                containment: container,
                handle: '.field-handle',
                cursor: 'grab',
                stop: function (e, ui) {
                    let dragged_ele = $(e.target);
                    let dragged_x = ui.position.left;
                    let dragged_y = ui.position.top;
                    /* let dragged_x_perc = pix_2_perc_xy('x', dragged_x, container);
                    let dragged_y_perc = pix_2_perc_xy('y', dragged_y,container); */
                    let dragged_h = dragged_ele.height();
                    let dragged_w = dragged_ele.width();
                    /* let dragged_h_perc = pix_2_perc_hw('height', dragged_h, container);
                    let dragged_w_perc = pix_2_perc_hw('width', dragged_w, container);
                    let dragged_group_id = dragged_ele.data('group-id'); */

                    /* let x = dragged_x;
                    position_edit_properties_div(x); */

                    setTimeout(function() {
                        set_hwxy(dragged_ele, '', field_type);
                        //keep_in_view(dragged_ele, dragged_w_perc, dragged_x_perc, dragged_y_perc, field_type);
                    }, 200);

                }
            });


            // add additional fields to group
            if (field_type != 'date') {
                // add items to group
                ele.find('.field-add-item').off('click').on('click', function () {

                    add_item(ele, container, field_type, rect);

                });
            }



            // mini-slider
            $('.mini-slider-button').off('click').on('click', function () {
                let minislider = $(this).closest('.field-options-holder').find('.mini-slider-div');
                minislider.show().addClass('active');
                $('.mini-slider-option').off('click').on('click', function () {
                    let dir = $(this).data('direction');
                    let operator = (dir == 'up') ? '-' : '+';
                    let field_div = $(this).closest('.field-div');
                    let field_type = field_div.data('type');
                    field_div.css({ top: operator + '=.05%' });
                    // set new h,w,x,y after moving up and down
                    set_hwxy(field_div, '', field_type);
                });

            });

            // add properties
            $('.field-properties').off('click').on('click', function () {
                show_edit_properties($(this));
            });

            form_elements();

        }

        function show_edit_properties(ele) {

            //console.log('running show_edit_properties');

            let field_type = ele.data('field-type');
            let field_id = ele.data('field-id');
            let edit_div = ele.closest('.field-div').find('.edit-properties-div');
            let inputs_container = edit_div.find('.field-inputs-div');

            /* if(edit_div.css('display') === 'block') {
                edit_div.hide();
            } else {
                edit_div.show();
            } */


            //store inputs html in input to be restored on cancel
            $('#inputs_html').val(inputs_container.html());

            let x = $('#field_'+field_id).position().left;
            position_edit_properties_div(x);

            // prevent new field being created
            $('.edit-properties-div *').off('dblclick').on('dblclick', function (event) {
                event.stopPropagation();
            });

            let select = edit_div.find('.form-select.field-data-name');

            edit_div.find('.form-input.field-data-name').on('keyup', function () {

                if($(this).val() != '') {

                    axios.get('/doc_management/get_custom_names', {
                        params: {
                            val: $(this).val()
                        }
                    })
                    .then(function (response) {

                        edit_div.find('.dropdown-results-div').html('');
                        response.data.custom_names.forEach(function (result) {
                            edit_div.find('.dropdown-results-div').append('<a href="javascript: void(0)" class="list-group-item list-group-item-action field-name-result">'+result['field_name_display']+'</a>');
                        });
                        edit_div.find('.custom-name-results').show();

                    })
                    .catch(function (error) {

                    });

                } else {
                    edit_div.find('.custom-name-results').hide();

                }

            });


            // clear fields on cancel
            $('.edit-properties-container').off().on('hidden.bs.collapse', function (e) {

                reset_field_properties(field_id);

                let form = $(this).find('.form-div');
                form.find('.field-inputs-div').html($('#inputs_html').val());

                //edit_div.hide();

            });
        }

        function add_item(ele, container, field_type, rect) {

            //console.log('running add_item');

            $('.collapse').removeClass('show');

            // assign group id for original field
            let group_id = $(this).data('group-id');

            /* let common_name = $('.group_' + group_id).data('commonname');
            let custom_name = $('.group_' + group_id).data('customname'); */
            $('.group_' + group_id).removeClass('standard').addClass('group');

            // get h,w, x, y etc. incase it was changed during a drag or resize
            let field_div = ele.closest('.field-div');
            let h = field_div.height();
            let w = field_div.width();
            let h_perc = pix_2_perc_hw('height', h, container);
            let w_perc = pix_2_perc_hw('width', w, container);
            let x = field_div.position().left;
            let y = field_div.position().top;
            let x_perc = pix_2_perc_xy('x', x, container);
            let y_perc = pix_2_perc_xy('y', y,container);

            // drop the new line height of ele below the original
            let spacing = 1.5;
            if (field_type == 'radio' || field_type == 'checkbox') {
                spacing = 1.5;
            }
            y_perc = y_perc + (h_perc * spacing);


            // create new id for new field in group
            field_id = Date.now();
            let field = field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, $('#active_page').val(), field_type);
            // append new field
            ele.closest('.field-container').append(field);

            let new_ele = $('#field_' + field_id);

            $('.focused').fadeOut();

            setTimeout(function() {

                $('.field-div').removeClass('active');
                new_ele.addClass('active').find('.focused').fadeIn();

                let new_h = new_ele.height();
                let new_w = new_ele.width();
                let new_h_perc = pix_2_perc_hw('height', new_h, container);
                let new_w_perc = pix_2_perc_hw('width', new_w, container);
                let new_x = new_ele.position().left;
                let new_y = new_ele.position().top;
                let new_x_perc = pix_2_perc_xy('x', new_x, container);
                let new_y_perc = pix_2_perc_xy('y', new_y,container);

                if ((parseFloat(new_y_perc) + parseFloat(new_h_perc)) > 99) {
                    new_ele.css({ border: '3px dotted #900' });
                    setTimeout(function () {
                        new_ele.remove();
                    }, 1000);
                    return false;
                }

                set_hwxy(new_ele, '', field_type);

                // assign group field_id to new field
                new_ele.data('group-id', group_id).removeClass('standard').addClass('group').addClass('group_' + group_id);

                // move add line option to last line
                $('.group_' + group_id).find('.add-item-container').hide();
                $('.group_' + group_id).find('.add-item-container').last().show();

                get_edit_properties_html(field_id, group_id, field_type, rect, container, new_ele);

            }, 100);
        }

        function remove_field() {

            //console.log('running remove_field');

            let remove_group_id = $('.field-div.active').data('group-id');
            $('.field-div.active').remove();

            let group = $('.group_' + remove_group_id);
            if (group.length > 1) {
                // hide add line option for all but last
                group.find('.add-item-container').hide();
                group.find('.add-item-container').last().show();
            } else {
                group.find('.add-item-container').show();
            }
            // see if other divs in a group and if just one remove group class and group icon from field-status-group-div
            if (group.length == 1) {
                group.removeClass('group').addClass('standard').find('.field-status-group-div').html('');
            }

            field_status();
            setTimeout(function () {
                field_list();
            }, 200);
        }

        function show_field_name(ele) {

            //console.log('running show_field_name');

            if(show_field_name_running == 'no') {

                show_field_name_running = 'yes';
                let field = ele;
                let form_div = field.closest('.form-div');
                let inputs_container = form_div.find('.field-inputs-div');
                if(field.val() != '') {
                    if(field.hasClass('form-input')) {
                        form_div.find('.form-select.field-data-name').val('');
                        select_refresh();
                        inputs_container.html('');
                    } else if(field.hasClass('form-select')) {
                        form_div.find('.form-input.field-data-name').val('');
                    }
                }
                show_field_name_running = 'no';

            }

        }

        function position_edit_properties_div(x) {

            //console.log('running position_edit_properties_div');

            let width = $('#file_viewer').width();
            if(x < (width / 4)) {
                $('.edit-properties-container').css({ left: '0px', right: '' });
                $('.field-options-holder').css({ left: '-8px', right: '' });
            } else if(x > (width / 4) && x < (width / 2)) {
                $('.edit-properties-container').css({ left: '-150px', right: '' });
                $('.field-options-holder').css({ left: '-8px', right: '' });
            } else if(x > (width / 2) && x < (width - (width * .35))) {
                $('.edit-properties-container').css({ left: '', right: '-150px' });
                $('.field-options-holder').css({ left: '', right: '0px' });
            } else if(x > (width - (width * .35))) {
                $('.edit-properties-container').css({ left: '', right: '0px' });
                $('.field-options-holder').css({ left: '', right: '0px' });

            }
        }

        function name_required(form_div) {

            //console.log('running name_required');

            let has_value = 'no';
            form_div.find('.field-data-name').each(function() {
                if($(this).val() != '') {
                    has_value = 'yes';
                }
            });
            if(has_value == 'yes') {
                form_div.find('.field-data-name').removeClass('required');
            } else {
                form_div.find('.field-data-name').addClass('required');
            }
            select_refresh();
        }

        function save_field_properties(button) {

            //console.log('running save_field_properties');

            //$('#properties_container_'+field_id).collapse('hide');
            $('.edit-properties-container.show').removeClass('show');

            setTimeout(function() {

                let field_div = button.closest('.field-div');
                let inputs_container = field_div.find('.field-inputs-div');
                let group_id = button.data('group-id');
                let field_id = button.data('field-id');
                let type = button.data('type');
                let form = button.closest('.form-div');
                let common_name = form.find('.form-select.field-data-name').val();
                let custom_name = form.find('.form-input.field-data-name').val();
                let inputs_div = form.find('.field-inputs-div');

                $('#inputs_html').val(inputs_container.html());
                field_div.find('input, select').each(function () {
                    $(this).data('default-value', $(this).val());
                });


                /* let address_select = form.find('select.field-data-address-type');
                address_select.each(function () {
                    let address_type = $(this).val();
                    $(this).data('default-value', address_type);
                });

                let textline_select = form.find('select.field-data-textline-type');
                textline_select.each(function () {
                    let textline_type = $(this).val();
                    $(this).data('default-value', textline_type);
                });

                let name_select = form.find('select.field-data-name-type');
                name_select.each(function () {
                    let name_type = $(this).val();
                    $(this).data('default-value', name_type);
                }); */



                let number_select = form.find('select.field-data-number-type');
                let number_type = number_select.val();
                number_select.data('default-value', number_type);
                // update other number types to written if this is numeric. There will be one numeric and possibly multiple written
                if (number_type == 'numeric') {
                    if ($('.group_' + group_id).length > 1) {
                        $('.group_' + group_id).each(function() {
                            $(this).find('select.field-data-number-type').not(number_select).val('written').data('default-value', 'written');
                        });
                    }
                }

                if (type == 'radio') {

                    let radio_input = form.find('input.field-data-radio-value');
                    let radio_value = radio_input.val();
                    radio_input.data('default-value', radio_value);

                    $('.group_' + group_id).each(function () {

                        $(this).data('customname', custom_name);
                        $(this).find('.form-input.field-data-name').each(function () {
                            $(this).val(custom_name).data('default-value', custom_name);
                        });
                    });

                } else if (type != 'checkbox') {

                    $('.group_' + group_id).each(function () {

                        $(this).data('commonname', common_name).data('customname', custom_name);
                        $(this).find('.form-select.field-data-name').each(function () {
                            $(this).val(common_name).data('default-value', common_name);
                        });
                        $(this).find('.form-input.field-data-name').each(function () {
                            $(this).val(custom_name).data('default-value', custom_name);
                        });

                    });

                }

                field_status();
                field_list();
                select_refresh();

                $('.group_' + group_id).removeClass('field-error');

            }, 10);


        }

        function add_address_inputs(field_id, inputs_container, select) {

            console.log('running add_address_inputs');

            inputs_container.html('');

            //let standard_addresses = ['Property Address', 'Seller One Home Address', 'Seller Two Home Address', 'Buyer One Home Address', 'Buyer Two Home Address'];
            //if (standard_addresses.includes(select.val().trim())) {

                let values = ['Street', 'City', 'State', 'Zip Code', 'County'];
                let address_type = select.val().replace(/\sAddress/, '').trim();
                let input_id = new Date().getTime();
                let c = 0;
                values.forEach(function(value) {
                    c += 1;
                    input_id = input_id + c;
                    inputs_container.append('<input type="hidden" class="field-data-input" id="input_name_'+field_id+'_'+input_id+'" value="'+address_type+' '+value+'" data-default-value="'+address_type+' '+value+'" data-id="'+input_id+'">');
                });

            //}

        }

        function add_name_inputs(field_id, inputs_container, select) {

            //console.log('running add_name_inputs');

            let name_type = select.val().replace(/\sName/, '');
            let input_id = new Date().getTime();
            let input_id2 = input_id + 1;

            inputs_container.html('');

            if (name_type == 'Seller or Landlord' || name_type == 'Buyer or Renter') {

                inputs_container.append('<input type="hidden" class="field-data-input" id="input_name_'+field_id+'_'+input_id+'" value="' + name_type + ' One Name" data-default-value="' + name_type + ' One Name" data-id="'+input_id+'">');
                inputs_container.append('<input type="hidden" class="field-data-input" id="input_name_'+field_id+'_'+input_id2+'" value="' + name_type + ' Two Name" data-default-value="' + name_type + ' Two Name" data-id="'+input_id2+'">');

            } else {

                inputs_container.append('<input type="hidden" class="field-data-input" id="input_name_'+field_id+'_'+input_id+'" value="' + name_type + ' Name" data-default-value="' + name_type + ' Name" data-id="'+input_id+'">');

            }

        }


        function get_edit_properties_html(field_id, group_id, field_type, rect, container, ele) {

            //console.log('running get_edit_properties_html');

            let common_name = $('.group_' + group_id).data('commonname');
            let custom_name = $('.group_' + group_id).data('customname');

            axios.get('/doc_management/get_edit_properties_html', {
                params: {
                    file_id: $('#file_id').val(),
                    field_id: field_id,
                    field_type: field_type,
                    group_id: group_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                // set values for field name
                if (field_type != 'checkbox') {

                    let edit_properties_html = response.data;
                    $('#field_'+field_id).find('.edit-properties-container').html(edit_properties_html);

                    if(ele) {

                        ele.find('.form-select.field-data-name').val(common_name).data('default-value', common_name);
                        ele.find('.form-input.field-data-name').val(custom_name).data('default-value', custom_name);

                        let inputs_container = $('#field_' + field_id).find('.field-inputs-div');

                        if(field_type == 'name') {
                            add_name_inputs(field_id, inputs_container, ele.find('.form-select.field-data-name'));
                        } else if(field_type == 'address') {
                            add_address_inputs(field_id, inputs_container, ele.find('.form-select.field-data-name'));
                        }
                    }

                }

                set_field_options(field_type, $('#field_' + field_id), field_id);

                //setTimeout(function() {
                    select_refresh();
                //}, 500);

            })
            .catch(function (error) {

            });
        }

        function field_html(h_perc, w_perc, x_perc, y_perc, field_id, group_id, page, type) {

            //console.log('running field_html');


            let field_class = '';
            let field_data = '';
            let hide_add_option = '';
            let handles = ' \
            <div class="ui-resizable-handle ui-resizable-e focused"></div> \
            <div class="ui-resizable-handle ui-resizable-w focused"></div> \
            ';
            let width_class = 'w-600';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'number') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
                w_perc = 10;

            } else if (type == 'radio') {
                handles = '';
                field_class = type + '-div standard';
                field_data = '<div class="radio-html"></div>';
                width_class = 'w-400';
            } else if (type == 'checkbox') {
                handles = '';
                field_class = type + '-div standard';
                field_data = '<div class="checkbox-html"></div>';
            } else if (type == 'date') {
                field_class = 'textline-div standard';
                field_data = '<div class="textline-html"></div>';
                hide_add_option = 'hidden';
                width_class = 'w-400';
            }

            let field_div_html = ' \
            <div class="field-div '+ field_class + ' active group_' + group_id + '" style="position: absolute; top: ' + y_perc + '%; left: ' + x_perc + '%; height: ' + h_perc + '%; width: ' + w_perc + '%;" id="field_' + field_id + '" data-field-id="' + field_id + '" data-group-id="' + group_id + '" data-type="' + type + '" data-page="' + page + '"> \
                <div class="field-status-div d-flex justify-content-left"> \
                    <div class="field-name-display-div"></div> \
                    <div class="field-status-group-div float-right"></div> \
                </div> \
                <div class="field-options-holder focused"> \
                    <div> \
                        <a href="javascript: void(0)" class="btn btn-sm btn-danger m-0 ml-2 close-field-options"><i class="fa fa-times fa-lg mr-2"></i> Hide</a> \
                    </div> \
                    <div class="btn-group" role="group" aria-label="Field Options"> \
                        <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary mini-slider-button"><i class="fal fa-arrows-v fa-lg"></i></a> \
                        <a type="button" class="btn btn-primary field-add-item ' + hide_add_option + '" data-group-id="'+ group_id + '"><i class="fal fa-plus fa-lg"></i></a> \
                ';
                if(type != 'checkbox') {
                        field_div_html += ' \
                        <a type="button" class="btn btn-primary field-properties" data-group-id="'+ group_id + '" data-field-id="' + field_id +'" data-field-type="' + type +'" data-toggle="collapse" href="#properties_container_'+field_id+'" role="button" aria-expanded="false" aria-controls="properties_container_'+field_id+'"><i class="fal fa-info-circle fa-lg"></i></a> \
                        ';
                }
                        field_div_html += ' \
                        <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a> \
                    </div> \
                    <div class="mini-slider-div"> \
                        <ul class="mini-slider list-group list-group-flush border border-primary p-0"> \
                            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="up"><i class="fal fa-arrow-up text-primary"></i></a></li> \
                            <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="down"><i class="fal fa-arrow-down text-primary"></i></a></li> \
                        </ul> \
                    </div> \
                </div> \
                <div id="properties_container_'+field_id+'" class="collapse edit-properties-container bg-white border rounded shadow '+width_class+'" data-field-id="'+field_id+'" data-parent="#file_viewer"></div> \
                '+ handles + ' \
                '+ field_data + ' \
            </div> \
            ';

            return field_div_html;
        }


        function field_status() {

            //console.log('running field_status');

            let group_ids = [];
            $('.field-div').each(function () {
                group_ids.push($(this).data('group-id'));
            });
            group_ids = group_ids.filter(global_filter_array);

            for (let i = 0; i < group_ids.length; i++) {

                // find out if grouped and add icon
                let grouped = false;
                let field_divs = $('.field-div[data-group-id="' + group_ids[i] + '"]');
                if (field_divs.length > 1) {
                    grouped = true;
                }
                if (grouped == true) {
                    // remove all group icons
                    field_divs.find('.field-status-group-div').html('');
                    // add group icon to last of all
                    field_divs.last().find('.field-status-group-div').html('<i class="fal fa-layer-group"></i>');
                }


                // add field names
                let cont = 'yes';
                $('.field-div[data-group-id="' + group_ids[i] + '"]').each(function () {

                    let field_div = $(this);
                    let field_name = '';

                    // all but checkbox get names added only to the last
                    if ($(this).data('type') != 'checkbox') {

                        $(this).find('.field-name-display-div').html('');
                        field_div.find('.field-data-name').each(function () {

                            if ($(this).val() != '') {
                                field_div.find('.field-data-name').removeClass('required');
                                field_name = $(this).val();
                                // add field name to last of each group
                                if(field_div.data('type') == 'number') {
                                    if(cont == 'yes') {
                                        if(field_div.find('.field-data-number-type option:checked').val() == 'written') {
                                            field_div.find('.field-name-display-div').html(field_name);
                                            cont = 'no';
                                        } else {
                                            field_div.find('.field-name-display-div').first().html(field_name);
                                            cont = 'no';
                                        }
                                    }
                                } else {
                                    $('.field-div[data-group-id="' + group_ids[i] + '"]').find('.field-name-display-div').last().html(field_name);
                                }
                            }

                        });
                    } else {
                        // checkboxes get name for each since not really a group
                        field_name = field_div.find('.field-data-name').val();
                        $(this).find('.field-name-display-div').html(field_name);
                    }
                });


            }

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

        function perc_2_pix(type, perc, container) {
            if (type == 'width') {
                return parseFloat((perc / 100) * parseFloat(container.width()));
            } else {
                return parseFloat((perc / 100) * parseFloat(container.height()));
            }
        }

        function set_hwxy(ele, group_id, type) {

            //console.log('running set_hwxy');

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

            //console.log('running keep_in_view');

            // adjust fields if placed out of bounds
            let dist = '';
            let cw = 100;
            let cd_adjusted = '';
            if (type == 'textline' || type == 'name' || type == 'address' || type == 'date' || type == 'number') {
                dist = 0;
                cd_adjusted = cw;
            } else if (type == 'radio' || type == 'checkbox') {
                dist = 0;
                cd_adjusted = cw;
            }

            /* let x_pos = ele.position().left;
            let doc_width = $('#file_viewer').width();

            if(x_pos > (doc_width * .75)) {
                ele.find('.field-options-holder').css({ left: '' }).animate({ right: '0px' });
            } else {
                ele.find('.field-options-holder').css({ right: '' }).animate({ left: '0px' });
            } */

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
            }, 100);

        }

        function set_common_fields() {

            //console.log('running set_common_fields');

            $.ajax({
                type: 'get',
                url: '/doc_management/common_fields',
                dataType: "json",
                success: function (data) {
                    $.each(data, function (k) {
                        let type = k;
                        let select_options = '';
                        $.each(this, function (k, v) {
                            select_options = select_options + '<option value="' + v + '">' + v + '</option>';
                        });
                        $('#' + type + '_select_options').val(select_options);
                    });
                    setTimeout(select_refresh, 500);

                }
            });
        }

        function field_list() {

            //console.log('running field_list');

            $('.field-list-container').html('');

            $('.file-view-page-container').each(function () {

                let page_number = $(this).data('id');

                $('.field-list-container').append('<div class="font-weight-bold text-white bg-primary p-1 pl-2 mb-2">Page ' + page_number + '</div>');

                // get unique group ids
                let group_ids = [];

                $(this).find('.field-div').each(function () {
                    group_ids.push($(this).data('group-id'));
                });
                group_ids = group_ids.filter(global_filter_array);
                // get all field names and add to field list
                $.each(group_ids, function (index, group_id) {
                    let name = $('.group_' + group_id).data('customname');
                    if($('.group_' + group_id).data('type') == 'radio') {
                        name = 'Radio - ' + name;
                    }
                    if ($('.group_' + group_id).data('commonname') != undefined && $('.group_' + group_id).data('commonname') != '') {
                        name = $('.group_' + group_id).data('commonname');
                    }
                    if (name == undefined || name == '') {
                        if($('.group_' + group_id).data('type') == 'checkbox') {
                            name = 'Checkbox';
                        } else {
                            name = '<span class="text-danger">Not Named</span>';
                        }
                    }
                    $('.field-list-container').append('<div class="mb-1 border-bottom border-primary"><a href="javascript: void(0)" class="field-list-link pl-2" data-group-id="' + group_id + '">' + name + '</a></div>');
                });

            });

            $('.field-list-link').off('click').on('click', function () {
                let group_id = $(this).data('group-id');
                let ele = $('.field-div[data-group-id="' + group_id + '"]').first();
                $('.focused').hide();
                ele.find('.focused').show();
                $('.field-div').removeClass('active');
                ele.addClass('active');

                let container = ele.parent();
                // let h = ele.height();
                // let w = ele.width();
                // let h_perc = pix_2_perc_hw('height', h, container);
                // let w_perc = pix_2_perc_hw('width', w, container);
                // let y = ele.position().top;
                // let x = ele.position().left;
                let type = ele.data('type');
                set_hwxy(ele, group_id, type);

                let $container = $('#file_viewer'),
                    $scrollTo = $('#field_' + group_id).first();
                $container.animate({
                    scrollTop: ($scrollTo.offset().top - $container.offset().top + $container.scrollTop()) - 200
                });

            });

        }

        function check_fields() {

            //console.log('running check_fields');

            // remove all error divs
            $('.field-error-div').remove();
            $('.field-error').removeClass('field-error');
            $('.field-list-link').removeClass('text-danger');
            let errors = 'no';

            $('.field-div').each(function () {

                let field_div = $(this);
                let type = field_div.data('type');

                if(type != 'checkbox') {

                    // add error divs
                    $('<div class="list-group field-error-div"></div>').insertAfter(field_div.find('.form-div'));

                    let errors_found = 'no';

                    let field_name = null;
                    field_div.find('.field-data-name').each(function () {
                        if ($(this).val() != '') {
                            field_name = $(this).val();
                        }
                    });
                    if (field_name == null) {
                        field_div.addClass('field-error');
                        field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must name the field</div>');
                        errors = 'yes';
                        errors_found = 'yes';
                    }

                    if (type == 'number') {
                        if (field_div.find('select.field-data-number-type').val() == '') {
                            field_div.addClass('field-error');
                            field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the number type</div>');
                            errors = 'yes';
                            errors_found = 'yes';
                        }
                    }

                    if (type == 'address') {
                        if (field_div.find('select.field-data-address-type').val() == '') {
                            field_div.addClass('field-error');
                            field_div.find('.field-error-div').append('<div class="field-error-item list-group-item list-group-item-danger"><i class="fal fa-exclamation-triangle mr-2"></i> You must enter the address type</div>');
                            errors = 'yes';
                            errors_found = 'yes';
                        }
                    }

                    if (errors_found == 'yes') {
                        $('.field-list-link[data-group-id="' +field_div.data('group-id')+'"]').addClass('text-danger');
                    } else {
                        field_div.find('.field-error-div').remove();
                    }

                }

            });

            field_status();
            setTimeout(function () {
                field_list();
            }, 200);

            if (errors == 'yes') {
                return false;
            }
            return true;

        }


        function save_add_fields() {

            //console.log('running save_add_fields');

            let check = check_fields();

            if(check == true) {

                $('#save_add_fields').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving');

                let data = [];

                if ($('.field-div').length > 0) {

                    $('.field-div').each(function () {

                        let field_data = {};
                        let field_div = $(this);
                        let field_id = field_div.data('field-id');
                        let type = field_div.data('type');

                        field_data['file_id'] = $('#file_id').val();
                        field_data['field_id'] = field_id;
                        field_data['group_id'] = field_div.data('group-id');
                        field_data['page'] = field_div.data('page');
                        field_data['field_type'] = type;
                        field_data['left'] = field_div.data('x');
                        field_data['top'] = field_div.data('y');
                        field_data['height'] = field_div.data('h');
                        field_data['width'] = field_div.data('w');
                        field_data['left_perc'] = field_div.data('xp');
                        field_data['top_perc'] = field_div.data('yp');
                        field_data['height_perc'] = field_div.data('hp');
                        field_data['width_perc'] = field_div.data('wp');

                        // inputs are arrays and have their own table
                        field_data['field_data_input'] = [];
                        //field_data['field_data_input_helper_text'] = [];
                        field_data['field_data_input_id'] = [];

                        if (field_div.find('.field-data-input').length > 0) {
                            field_div.find('.field-data-input').each(function () {
                                if ($(this).val() != '') {
                                    field_data['field_data_input'].push($(this).val());
                                    field_data['field_data_input_id'].push($(this).data('id'));
                                }
                            });
                        }

                        field_div.find('.field-data-name').each(function () {
                            if ($(this).val() != '') {
                                field_data['field_name'] = $(this).val();
                                let field_type = $(this).data('field-type');
                                field_data['field_name_type'] = field_type;
                            }
                        });


                        //field_data['helper_text'] = field_div.find('input.field-data-helper-text').val();

                        field_data['number_type'] = field_div.find('select.field-data-number-type').val();

                        field_data['address_type'] = field_div.find('select.field-data-address-type').val();

                        field_data['textline_type'] = field_div.find('select.field-data-textline-type').val();

                        field_data['name_type'] = field_div.find('select.field-data-name-type').val();

                        field_data['radio_value'] = field_div.find('.field-data-radio-value').val();

                        field_data['checkbox_value'] = field_div.find('.field-data-checkbox-value').val();

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
            } else {
                $('#modal_danger').modal('show').find('.modal-body').html('All Fields Must Be Completed');
            }


        }


    });

}


