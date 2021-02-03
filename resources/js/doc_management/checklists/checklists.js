if (document.URL.match(/checklists/)) {

    $(function() {

        load_checklists();

        let show_checklist_id = global_get_url_parameters('checklist_id');

        if (show_checklist_id) {
            add_checklist_items(show_checklist_id);
            let checklist_location_id = global_get_url_parameters('checklist_location_id');
            let type = global_get_url_parameters('checklist_type');
            $('#list_' + checklist_location_id).trigger('click');
            $('#list_div_' + checklist_location_id).find('.checklist-type-option').val(type);
            //change_checklist_type();
        }

        $('.referral-tab').on('shown.bs.tab', function (e) {
            $('.referral-tab-data').find('.non-referral-checklist-div').hide();
            $('.referral-tab-data').find('.referral-checklist-div').show();
            $('.referral-tab-data').find('.property-type-div-header').text('Referral Checklist');
        });
        /* $('.referral-tab').on('hidden.bs.tab', function (e) {
            $('.referral-tab-data').find('.non-referral-checklist-div').show();
            $('.referral-tab-data').find('.referral-checklist-div').hide();
        }); */


        // moved this from init
        $('.checklist-location').not('.loaded').off('click').on('click',function() {
            $(this).addClass('loaded');
            let location_id = $(this).data('id');
            get_checklists(location_id, 'listing');
        });



    });

    function load_checklists() {
        // get first location to load. others will be loaded when location is selected from menu
        let location_id = $('.checklist-data').eq(0).data('location-id');
        get_checklists(location_id, 'listing');
    }

    // functions to run on load and after adding elements
    function init() {

        // show add/edit modal to edit checklist details
        $('.add-checklist-button, .edit-checklist-button').off('click').on('click', show_add_edit_checklist);
        // add referral checklist
        $('.add-referral-checklist-button').off('click').on('click', add_referral_checklist);
        // delete checklist
        $('.delete-checklist-button').off('click').on('click', confirm_delete_checklist);
        // add items to checklist
        $('.add-items-button').off('click').on('click', function () {
            add_checklist_items($(this).data('checklist-id'));
        });
        // duplicate checklist
        $('.duplicate-checklist-button').off('click').on('click', function () {
            duplicate_checklist($(this));
        });
        // copy checklists to another location
        $('.copy-checklist-button').off('click').on('click', function() {
            copy_checklist($(this).data('location-id'));
        });

        $('.select-form-group').off('change').on('change', show_form_group);

        $('.checklist-type-option').off('change').on('change', change_checklist_type);


        sortable_checklists();



        /* setTimeout(function() {
            select_refresh();
        }, 200); */

    }

    function add_referral_checklist() {
        let location_id = $(this).data('location-id');
        let state = $(this).data('state');

        let formData = new FormData();
        formData.append('checklist_location_id', location_id);
        formData.append('checklist_type', 'referral');
        formData.append('checklist_state', state);

        url = '/doc_management/add_checklist_referral';

        axios.post(url, formData, axios_options)
            .then(function (response) {
                get_checklists(location_id, 'referral');
            })
            .catch(function (error) {

            });
    }

    function duplicate_checklist(ele) {
        let checklist_id = ele.data('checklist-id');
        let checklist_location_id = ele.data('checklist-location-id');
        let checklist_type = ele.data('checklist-type');
        let formData = new FormData();
        formData.append('checklist_id', checklist_id);
        axios.post('/doc_management/duplicate_checklist', formData, axios_options)
        .then(function (response) {
            get_checklists(checklist_location_id, checklist_type);
        })
        .catch(function (error) {

        });
    }

    function copy_checklist(location_id) {

        let checklist_type = $('#list_div_' + location_id).find('.checklist-type-option').val();

        axios.get('/doc_management/get_copy_checklists', {
            params: {
                location_id: location_id,
                checklist_type: checklist_type
            },
            headers: axios_headers_html
        })
        .then(function (response) {
            $('#copy_checklists_modal').modal();
            $('#copy_checklists_div').html(response.data);
            // set values to access later
            $('#copy_checklists_location_id').val(location_id);
            $('#copy_checklists_checklist_type').val(checklist_type);

            //form_elements();
            global_tooltip();
            // highlight selected check rows
            $('.export-to-form-group').on('change', function() {
                if($(this).is(':checked')) {
                    $(this).closest('.list-group-item').addClass('bg-green-light');
                } else {
                    $(this).closest('.list-group-item').removeClass('bg-green-light');
                }
            });

            $('#save_copy_checklists_button').off('click').on('click', function() {
                if($('.export-to-form-group:checked').length == 0) {
                    $('#modal_danger').modal().find('.modal-body').html('You must select at least one region to copy the checklists to');
                    return false;
                }
                $('#confirm_copy_modal').modal();
                $('#confirm_copy_button').off('click').on('click', function() {
                    $('#save_copy_checklists_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span> Copying Checklists');
                    save_copy_checklists(location_id, checklist_type);
                });
            });

        })
        .catch(function (error) {

        });
    }

    function save_copy_checklists(location_id, checklist_type) {

        $('#confirm_copy_modal').modal('hide');

        let checklists_to_copy_ids = [];
        $('.checklists-to-export-id:checked').each(function() {
            checklists_to_copy_ids.push($(this).val());
        });

        let checklist_location_ids = [];
        $('.export-to-form-group:checked').each(function () {
            checklist_location_ids.push($(this).val());
        });

        let formData = new FormData();
        formData.append('location_id', location_id);
        formData.append('checklist_location_ids', checklist_location_ids);
        formData.append('checklists_to_copy_ids', checklists_to_copy_ids);
        formData.append('checklist_type', checklist_type);

        axios.post('/doc_management/save_copy_checklists', formData, axios_options)
        .then(function (response) {
            $('#copy_checklists_modal').modal('hide');
            toastr['success']('Checklists Copied Successfully');
            load_checklists();
            get_checklists(location_id, checklist_type);
            $('#save_copy_checklists_button').prop('disabled', false).html('<i class="fal fa-check mr-2"></i> Copy Checklists');
        })
        .catch(function (error) {

        });

    }

    function change_checklist_type() {

        $('.checklist-items-container').hide();
        $('.checklist-type-option').each(function () {

            let list_div = $(this).closest('.list-div');

            list_div.find('.checklist-items-contract').hide();
            list_div.find('.checklist-items-listing').hide();

            if(!$('.referral-tab').hasClass('active')) {

                let checklist_type = $(this).val().charAt(0).toUpperCase() + $(this).val().slice(1);
                list_div.find('.property-type-div-header').text(checklist_type + ' Checklists');

                if ($(this).val() == 'listing') {
                    list_div.find('.checklist-items-listing').show();
                } else if ($(this).val() == 'contract') {
                    list_div.find('.checklist-items-contract').show();
                }

            }

        });

    }

    function sortable_checklists() {
        // checklists are sortable
        $('.sortable-checklist').sortable({
            placeholder: 'bg-orange-sortable',
            handle: '.checklist-handle',
            stop: function (event, ui) {
                // get details and update new order
                let els = $(ui.item).parent('.sortable-checklist').children('.checklist-items-container');
                reorder_checklists(els);
            }

        });
        $('.sortable-checklist').disableSelection();
    }

    function reorder_checklists(els) {
        let checklists = {
            checklist: []
        }

        els.each(function () {
            let el, checklist_id, checklist_index;
            el = $(this);
            checklist_id = el.data('checklist-id');
            checklist_index = el.index();
            checklists.checklist.push(
                {
                    'checklist_id': checklist_id,
                    'checklist_index': checklist_index
                }
            );
        });
        let formData = new FormData();
        checklists = JSON.stringify(checklists);
        formData.append('data', checklists);
        axios.post('/doc_management/reorder_checklists', formData, axios_options)
            .then(function (response) {
                toastr['success']('Checklist Reordered');
            })
            .catch(function (error) {

            });
    }

    function sortable_checklist_items() {
        // checklist items are sortable and saved after sort
        $('.sortable-checklist-items').sortable({
            handle: '.checklist-item-handle',
            items: '.list-group-item',
            placeholder: 'bg-orange-sortable-big',
            revert: true,
            start: function(event, ui) {
                setTimeout(function() {
                    fill_empty_groups();
                }, 500);
            },
            stop: function (event, ui) {
                // remove place holders for empty form groups
                $('.list-group-holder').each(function() {
                    if($(this).prev('li').hasClass('checklist-item') || $(this).next('li').hasClass('checklist-item')) {
                        $(this).remove();
                    }
                });
                forms_status();
                $('.bg-orange-light').removeClass('bg-orange-light');
                // assign new form-group-id
                $('.checklist-item').each(function () {
                    let form_group_id = $(this).prevAll('.list-group-header:first').data('form-group-id');
                    $(this).data('form-group-id', form_group_id);
                });
            }

        });
        $('.sortable-checklist-items').disableSelection();
    }

    function fill_empty_groups() {
        $('.list-group-header').each(function() {
            if($(this).next('li').hasClass('ui-sortable-helper') && $(this).next('li').next('li').hasClass('list-group-header')) {
                $('<li class="list-group-item list-group-holder w-100 text-danger p-4">No Forms Yet For This Group</li>').insertAfter($(this));
            }
            $('.sortable-checklist-items').sortable('refresh');
        });
    }

    // search forms in add items modal
    function form_search() {
        let v = $('.form-search').val();
        if (v.length == 0) {
            // hide all containers with header and name inside
            $('.form-group-div').hide();
            // make sure all headers and names are visible if searched for
            $('.list-group-header, .form-name').show();
            // get value of selected form group to reset list
            let form_group = $('.select-form-group').val();
            if (form_group == 'all') {
                $('.form-group-div, .list-group-header, .form-name').show();
            } else {
                $('[data-form-group-id="' + form_group + '"]').show().find('.form-name').show();
            }
        } else {
            // show all containers with header and name inside
            $('.form-group-div').show();
            // hide all headers
            $('.list-group-header').hide();
            // hide all names
            $('.form-name').hide().each(function () {
                if ($(this).data('text').match(new RegExp(v, 'i'))) {
                    // show name
                    $(this).show();
                    // show header
                    $(this).closest('.form-group-div').find('.list-group-header').show();
                }
            });
        }
    }

    // add/edit checklist items
    function add_checklist_items(checklist_id) {

        // get checklist details html to add to modal
        axios.get('/doc_management/get_checklist_items', {
            params: {
                checklist_id: checklist_id
            },
            headers: axios_headers_html
        })
        .then(function (response) {
            // show modal
            $('#checklist_items_modal').modal();
            // add html form response
            $('#checklist_items_div').html(response.data);

            // add title to modal
            $('#checklist_items_modal_title').html('Checklist Items | ' + $('#checklist_header_val').val());

            // search forms
            $('.form-search').on('keyup', form_search);

            // delete checklist items
            $('.delete-checklist-item-button').on('click', delete_checklist_item);

            $('.add-to-checklist-button').not('disabled').on('click', add_to_checklist);

            $('#save_checklist_items_button').off('click').on('click', save_checklist_items);

            sortable_checklist_items();
            forms_status();
            //form_elements();

            show_form_group();


        })
        .catch(function (error) {

        });
    }

    function show_form_group() {

        let parent = $('.forms-container');
        let select = parent.find('.select-form-group');
        // clear search input
        parent.find('.form-search').val('');

        // if all show everything or just the selected group
        if (select.val() == 'all') {
            parent.find('.form-group-div, .list-group-header, .form-name').show();
        } else {
            parent.find('.list-group-header, .form-name').show();
            parent.find('.form-group-div').hide();
            parent.find('[data-form-group-id="' + select.val() + '"]').show();
        }

    }

    function save_checklist_items() {

        let cont = 'yes';
        $('.checklist-item').each(function() {
            if($(this).data('form-group-id') == undefined) {
                $('#modal_danger').modal().find('.modal-body').html('You must add all checklist items to a group');
                cont = 'no';
            }
        });

        let form = $('#checklist_items_form');

        let validate = validate_form(form);

        if (validate == 'yes' && cont == 'yes') {

            //if ($('.checklist-item').length > 0) {

                let checklist_items = [];
                let checklist_id = $('#checklist_id').val();

                $('.checklist-item').each(function (index) {

                    let checklist_item = {};

                    let checklist_form_id = $(this).data('form-id');
                    let required = $(this).find('.checklist-item-required').prop('name');
                    let checklist_item_required = $('[name="'+required+'"]:checked').val();
                    let checklist_item_group_id = $(this).data('form-group-id');
                    let checklist_item_order = index;

                    checklist_item['checklist_id'] = checklist_id;
                    checklist_item['checklist_form_id'] = checklist_form_id;
                    checklist_item['checklist_item_required'] = checklist_item_required;
                    checklist_item['checklist_item_group_id'] = checklist_item_group_id;
                    checklist_item['checklist_item_order'] = checklist_item_order;

                    checklist_items.push(checklist_item);

                });

                let formData = new FormData();
                formData.append('checklist_id', checklist_id);
                formData.append('checklist_items', JSON.stringify(checklist_items));
                axios.post('/doc_management/add_checklist_items', formData, axios_options)
                .then(function (response) {
                    $('#checklist_items_modal').modal('hide');
                    toastr['success']('Checklist Items Saved');
                    get_checklists($('#add_item_checklist_location_id').val(), $('#add_item_checklist_type').val());
                })
                .catch(function (error) {

                });

            //}
        }

    }

    function add_to_checklist() {
        let form_id = $(this).data('form-id');
        let text_orig = $(this).data('text');
        let checklist_group_id = $(this).data('checklist-group-id');
        let form_loc = 'javascript:void(0)';
        if($(this).data('form-loc') != '') {
            form_loc = '/'+$(this).data('form-loc');
        }
        let text = text_orig;
        if (text_orig.length > 85) {
            text = text_orig.slice(0, 85) + '...';
        }
        // this is the helper dragged and inserted in checklist items container
        let checklist_item = ' \
            <li class="list-group-item checklist-item w-100 pt-1 pb-0 bg-orange-light" data-form-id="' + form_id + '" data-form-group-id="'+checklist_group_id+'"> \
                <div class="row"> \
                    <div class="col-8"> \
                        <div class="d-flex justify-content-start align-items-center"> \
                            <div> \
                                <i class="fas fa-sort fa-lg mx-3 text-primary checklist-item-handle ui-sortable-handle"></i> \
                            </div> \
                            <div class="h5 text-primary" title="' + text_orig + '"><a href="javascript: void(0)"> ' + text + '</a></div> \
                        </div> \
                    </div> \
                    <div class="col-3"> \
                        <div class="d-flex justify-content-start align-items-center"> \
                            <span class="mr-1">Required:</span> \
                            <input type="radio" class="custom-form-element form-radio checklist-item-required required" name="checklist_item_required_' + form_id + '" value="yes" data-label="Yes"> \
                            <input type="radio" class="custom-form-element form-radio checklist-item-required required" name="checklist_item_required_' + form_id + '" value="no" data-label="No"> \
                        </div> \
                    </div> \
                    <div class="col-1 d-flex align-items-center"> \
                        <a class="btn btn-sm btn-danger delete-checklist-item-button"><i class="fad fa-trash"></i></a> \
                    </div> \
                </div> \
            </li> \
        ';
        $(checklist_item).insertAfter($('[data-form-group-id="' + checklist_group_id + '"]').last());
        $('.delete-checklist-item-button').on('click', delete_checklist_item);


        // if the form is already included in another checklist get the details and add it to this one
        axios.get('/doc_management/get_checklist_item_details', {
            params: {
                form_id: form_id
            },
        })
        .then(function (response) {
            //form_elements();
            forms_status();
        })
        .catch(function (error) {

        });



    }

    function delete_checklist_item() {
        let button = $(this);
        $('#confirm_remove_file_modal').modal();
        $('#confirm_remove_file').on('click', function () {
            button.closest('li').remove();
            forms_status();
            fill_empty_groups();
            $('#confirm_remove_file_modal').modal('hide');
        });
    }

    function forms_status() {

        $('.form-name').removeClass('form-selected');
        $('.add-to-checklist-button').removeClass('disabled');

        let form_ids = [];
        $('.checklist-item').each(function () {
            form_ids.push($(this).data('form-id'));
        });

        $.map(form_ids, function (value, index) {
            $('.form-name[data-form-id="' + value + '"]').addClass('form-selected').find('.add-to-checklist-button').addClass('disabled');
        });

    }

    function show_add_edit_checklist() {

        $('#checklist_modal').modal();

        let checklist_type = $(this).closest('.list-div').find('.checklist-type-option').val();

        let location_id = $(this).data('location-id');
        let property_type = $(this).data('property-type');
        let property_sub_type = $(this).data('property-sub-type');
        let state = $(this).data('state');
        let form_type = $(this).data('form-type');
        let sale_rent = $(this).data('sale-rent');
        let represent = $(this).data('represent');

        // assign form input values
        $('#checklist_location_id').val(location_id);
        $('#checklist_type').val(checklist_type);
        $('#checklist_property_type_id').val(property_type);
        $('#checklist_property_sub_type_id').val(property_sub_type);
        $('#checklist_sale_rent').val(sale_rent);
        $('#checklist_represent').val(represent);

        if (checklist_type== 'listing') {
            $('#checklist_represent').val('seller');
        }


        // assign hidden input values
        $('#checklist_id').val($(this).data('checklist-id'));
        $('#checklist_state').val(state);
        $('#form_type').val(form_type);

        //select_refresh();

        show_hide_options();

        $('#checklist_modal').on('change', '.form-select', function(e) {

            let ele = $(this);
            let id = ele.attr('id');

            let ids = ['checklist_type', 'checklist_property_type_id', 'checklist_sale_rent', 'checklist_property_sub_type_id', 'checklist_represent'];

            if(ids.includes(id)) {
                show_hide_options();
            }

        });


        $('#save_checklist_button').off('click').on('click', save_checklist);

    }

    function show_hide_options() {
console.log('show_hide_options');
        let select_checklist_type = $('#checklist_type');
        let select_checklist_property_type = $('#checklist_property_type_id');
        let select_checklist_sale_rent = $('#checklist_sale_rent');
        let select_checklist_property_sub_type_id = $('#checklist_property_sub_type_id');
        let select_checklist_represent = $('#checklist_represent');

        // ######### these are based on resource values and could possibly be changed by user

        if (select_checklist_type.val() == 'listing') {
            select_checklist_represent.val('seller').prop('disabled', true);
        } else {
            select_checklist_represent.prop('disabled', false);
        }


        // if residential show property_sub_types | but not if rental
        if (select_checklist_property_type.find('option:selected').text() == 'Residential') {
            if (select_checklist_sale_rent.val() == 'rental') {
                select_checklist_property_sub_type_id.val('').addClass('hidden').removeClass('required');
            } else {
                select_checklist_property_sub_type_id.removeClass('hidden').addClass('required');
            }
        } else {
            select_checklist_property_sub_type_id.val('').addClass('hidden').removeClass('required');
        }
        // if fsbo you must be representing the buyer, must be a contract and for sale
        if (select_checklist_property_sub_type_id.find('option:selected').text() == 'For Sale By Owner') {
            select_checklist_represent.val('buyer');
            select_checklist_sale_rent.val('sale');
        }

        if (select_checklist_represent.val() == 'buyer') {
            select_checklist_type.val('contract');
        }

        //select_refresh();

    }

    function save_checklist() {
        let form = $('#checklist_form');
        let validate = validate_form(form);

        if (validate == 'yes') {
            let formData = new FormData();
            formData.append('checklist_id', $('#checklist_id').val());
            formData.append('checklist_location_id', $('#checklist_location_id').val());
            formData.append('checklist_represent', $('#checklist_represent').val());
            formData.append('checklist_type', $('#checklist_type').val());
            formData.append('checklist_sale_rent', $('#checklist_sale_rent').val());
            formData.append('checklist_property_type_id', $('#checklist_property_type_id').val());
            formData.append('checklist_property_sub_type_id', $('#checklist_property_sub_type_id').val());
            formData.append('checklist_state', $('#checklist_state').val());

            let form_type = $('#form_type').val();

            let url = '/doc_management/edit_checklist';
            if (form_type == 'add') {
                url = '/doc_management/add_checklist';
            }

            axios.post(url, formData, axios_options)
                .then(function (response) {
                    get_checklists($('#checklist_location_id').val(), $('#checklist_type').val());
                    setTimeout(function() {
                        let els = $('#list_div_' + $('#checklist_location_id').val() + '_files').find('.sortable-checklist').children('.checklist-items-container');
                        reorder_checklists(els);
                    }, 500);
                })
                .catch(function (error) {

                });
        }
    }

    function get_checklists(checklist_location_id, checklist_type) {
        let options = {
            params: {
                checklist_location_id: checklist_location_id,
                checklist_type: checklist_type
            },
            headers: axios_headers_html
        }

        axios.get('/doc_management/get_checklists', options)
            .then(function (response) {
                $('#list_div_' + checklist_location_id + '_files').html($(response.data));
                // $('#list_div_' + checklist_location_id + '_file_count').text($('#files_count').val());
                $('.checklist-items-container').hide();
                $('.checklist-items-' + checklist_type).show();
                $('#checklist_modal').modal('hide');

                if(checklist_type == 'referral') {
                    $('.referral-tab-data').find('.non-referral-checklist-div').hide();
                    $('.referral-tab-data').find('.referral-checklist-div').show();
                } else {
                    $('.referral-tab-data').find('.non-referral-checklist-div').show();
                    $('.referral-tab-data').find('.referral-checklist-div').hide();
                }
                // make sure correct checklist type is shown
                //$('.checklist-type-option').val(checklist_type);

                //select_refresh();
                init();
            })
            .catch(function (error) {

            });
    }

    function confirm_delete_checklist() {
        let ele = $(this);
        $('#confirm_delete_checklist_modal').modal();
        $('#confirm_delete_checklist').off('click').on('click', function () {
            delete_checklist(ele);
        });

    }

    function delete_checklist(ele) {
        let checklist_id = ele.data('checklist-id');
        let checklist_location_id = ele.data('checklist-location-id');
        let checklist_type = ele.data('checklist-type');
        let formData = new FormData();
        formData.append('checklist_id', checklist_id);
        axios.post('/doc_management/delete_checklist', formData, axios_options)
        .then(function (response) {
            get_checklists(checklist_location_id, checklist_type);
            $('#confirm_delete_checklist_modal').modal('hide');
        })
        .catch(function (error) {

        });
    }

}

