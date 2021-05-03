if (document.URL.match(/create\/upload\/files/)) {

    $(function() {

        init();

        $('#file_name_display').on('keyup change', function() {
            $(this).val(ucwords($(this).val()));
        });

        $('.form-search').on('keyup change', function() {
            form_search($(this));
        });

        $(document).on('click', '.show-checklist-items-collapsible', function() {
            let input = $(this).closest('td').prev('td').find('input');
            show_options(input);
        });

    });

    function init() {
        // get forms for each form group
        //let data_count = $('.forms-data').length;
        //global_loading_on('', '<div class="h3 text-white">Loading...</div>');

        // load first form group
        let form_group = $('.forms-data').eq(0);
        let form_group_id = form_group.data('form-group-id');
        let state = form_group.data('state');
        let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();

        get_forms(form_group_id, state, order);


    }

    function form_search(ele) {

        let container = ele.closest('.list-div');
        let val = ele.val();
        if(val.length > 0) {
            let regex = new RegExp(val, 'i');
            container.find('.uploads-list').each(function() {
                if($(this).data('form-name').match(regex)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('.uploads-list').show();
        }
    }

    function get_forms(form_group_id, state, order=null) {

        //global_loading_on('', '<div class="h3 text-white">Loading...</div>');

        let form_group = $('#list_div_'+form_group_id+'_files');
        let options = {
            params: {
                form_group_id: form_group_id,
                state: state,
                order: order
            },
            headers: axios_headers_html
        }

        axios.get('/doc_management/get_form_group_files', options)
            .then(function (response) {

                form_group.html($(response.data));

                // Add file modal
                $('.upload-file-button').off('click').on('click', function () {
                    show_upload(form_group);
                });
                $('.add-non-form-item-button').off('click').on('click', function() {
                    show_add_non_form_item(form_group);
                });
                // init functions
                upload_options();

                $('#upload_file_button').off('click').on('click', upload_file);

                //$('.add-non-form-item-button').hide();

                //global_loading_off();

                //form_elements();

                let ele = form_group.find('.activate-upload');

                filter_uploads(ele);

                $('.add-edit-button').on('click', function () {
                    $('.uploads-list').removeClass('bg-orange-light');
                    $(this).closest('.uploads-list').addClass('bg-orange-light');
                });

            })
            .catch(function (error) {

            });
    }

    function upload_options() {

        $('.edit-upload').off('click').on('click', function () {
            edit_upload($(this));
        });

        $('.duplicate-upload').off('click').on('click', function () {
            duplicate_upload($(this));
        });

        $('.publish-upload').off('click').on('click', function () {
            confirm_publish_upload($(this));
        });

        $('.activate-upload').off('click').on('click', function () {
            activate_upload($(this));
        });

        $('.delete-upload').off('click').on('click', function () {
            confirm_delete_upload($(this));
        });

        $('.manage-upload').off('click').on('click', function () {
            show_manage_upload($(this).data('id'), $(this).data('form-group-id'));
        });


        $('.uploads-filter-published, .uploads-filter-active').off('change').on('change', function () {
            filter_uploads($(this));
        });

        $('.uploads-filter-sort').off('change').on('change', function () {
            sort_uploads($(this));
        });
        global_tooltip();

        $('.form-group-item').off('click').on('click', function() {
            if($(this).hasClass('last')) {
                $('.add-non-form-item-button').show();
                $('.upload-file-button').hide();
            } else {
                $('.add-non-form-item-button').hide();
                $('.upload-file-button').show();
            }

            $('.form-search').val('');
            $('.uploads-list').show();

            let id = $(this).data('id');
            let form_group = $('#list_div_'+id+'_files');
            if(!form_group.html().match(/\</)) {
                let form_group_id = form_group.data('form-group-id');
                let state = form_group.data('state');
                let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
                get_forms(form_group_id, state, order);
            }

        });
    }

    function show_manage_upload(form_id, form_group_id) {
        $('#form_manage_modal').modal();
        axios.get('/doc_management/get_manage_upload_details', {
            params: {
                form_id: form_id,
                form_group_id: form_group_id
            },
            headers: axios_headers_html
        })
        .then(function (response) {

            $('#form_manage_div').html(response.data);
            //form_elements();

            let state = $('#manage_form_state').val();
            let form_name = $('#manage_form_name').val();

            $('.select-form-button').off('click').on('click', function () {
                show_confirm_replace(form_group_id, state, form_id, form_name, $(this).data('form-id'), $(this).data('form-name'));
            });
            $('#remove_from_checklist_button').off('click').on('click', function () {
                show_confirm_remove(form_id, form_name, form_group_id, state);
            });
            $('#add_to_checklists_button').off('click').on('click', function () {
                show_checklist_type(form_id, form_name, form_group_id, state);
            });

            global_tooltip();
            //select_refresh();

        })
        .catch(function (error) {

        });
    }

    function show_checklist_type(form_id, form_name, form_group_id, state) {
        $('#checklist_type_modal').modal();
        $('#checklist_type_button').on('click', function() {
            let checklist_type = $('#add_form_checklist_type').val();
            let form = $('#checklist_type_form');
            let form_check = validate_form(form);
            if (form_check == 'yes') {
                $('#checklist_type_modal').modal('hide');
                setTimeout(function() {
                    show_add_to_checklists(form_id, form_name, form_group_id, state, checklist_type);
                }, 200);
            }
        });
    }

    function show_add_to_checklists(form_id, form_name, form_group_id, state, checklist_type) {
        $('#add_to_checklists_modal').modal();
        $('#add_form_to_checklists_div').html('<div class="loader-gif"></div>');
        axios.get('/doc_management/get_add_to_checklists_details', {
            params: {
                form_id: form_id,
                /* form_group_id: form_group_id, */
                checklist_type: checklist_type
            },
            headers: axios_headers_html
        })
        .then(function (response) {

            setTimeout(function() {
                $('#add_form_to_checklists_div').html(response.data);
                $('#add_to_checklists_form_name').text(form_name);
                //form_elements();
                $('#add_to_checklists_table').DataTable({
                    'paging': false,
                    "aaSorting": [],
                    "searching": false,
                    "info": false,
                    columnDefs: [{
                        orderable: false,
                        targets: [0,1,9]
                    }]
                });

                order_checklists();


                $('.checklist-item-checkbox').on('change', function() {
                    show_options($(this));
                });

                $('#save_add_to_checklists_button').off('click').on('click', function() {
                    save_add_to_checklists(state, form_id, form_group_id);
                });

                $('.checklist-filter').on('change', checklist_filter);
                $('#filter_selected').on('change', filter_selected);

                $('#select_all_checklists').on('click', check_all);
            }, 2000);

        })
        .catch(function (error) {

        });

    }

    function filter_selected() {
        if($(this).val() == 'selected') {
            $('.checklist-items-tr').hide();
            $('.checklist-item-checkbox:checked').closest('.checklist-items-tr').show();
        } else {
            $('.checklist-items-tr').show();
            $('.checklist-item-checkbox:checked').closest('.checklist-items-tr').hide();
        }
    }

    function show_options(ele) {
        // highlight background of selected rows
        if(ele.is(':checked')) {
            let bg_class = 'bg-blue-light';
            ele.closest('tr').addClass(bg_class).find('.show-checklist-items-collapsible-div').show();
        } else {
            ele.closest('tr').removeClass('bg-blue-light').find('.show-checklist-items-collapsible-div').hide();
        }
        $('.collapse').collapse('hide');
        $('.show-checklist-items-collapsible').text('Show Checklist Items').addClass('btn-primary').removeClass('btn-success');

        let append_to = ele.closest('td').next('td').find('.checklist-items-collapsible');
        let button = ele.closest('td').next('td').find('.show-checklist-items-collapsible');
        console.log(append_to.length, button.length, append_to.find('li').length);
        if(append_to.find('li').length == 0) {
            let checklist_id = button.data('checklist-id');
            let file_id = button.data('file-id');
            axios.get('/doc_management/add_form_get_checklist_items', {
                params: {
                    checklist_id: checklist_id,
                    file_id: file_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {
                append_to.html(response.data);
                sortable();
            })
            .catch(function (error) {

            });

        }
    }

    function check_all() {
        if($('#select_all_checklists').is(':checked')) {
            $('.filter-active').find('.checklist-item-checkbox').each(function() {
                $(this).prop('checked', true);
                show_options($(this));
            });
        }  else {
            $('.filter-active').find('.checklist-item-checkbox').each(function() {
                $(this).prop('checked', false);
                show_options($(this));
            });
        }

    }

    function checklist_filter() {
        $('#select_all_checklists').prop('checked', false);
        $('.checklist-items-tr').show().addClass('filter-active');
        $('.checklist-filter').each(function() {
            let filter_val = $(this).val();
            let filter_type = $(this).data('type');
            if(filter_val != '') {
                $('.checklist-items-tr').each(function() {
                    if($(this).data(filter_type) != filter_val) {
                        $(this).hide().removeClass('filter-active');
                    }
                });
            }

        });
    }

    function save_add_to_checklists(state, checklist_form_id, checklist_item_group_id) {

        let form = $('#add_to_checklists_form');
        let form_check = validate_form(form);

        if (form_check == 'yes') {

            $('#save_add_to_checklists_button').html('<span class="spinner-border spinner-border-sm"></span> Saving');

            let els = $('.checklist-items-tr');
            let checklists = {
                checklist: []
            }

            let checklist_ids_keep = [];
            els.each(function () {
                let el, checklist_id_checkbox, checklist_id;
                el = $(this);
                checklist_id_checkbox = el.find('.checklist-item-checkbox');
                checklist_id = '';
                // get all checked checklists
                if(checklist_id_checkbox.is(':checked')) {
                    checklist_id = checklist_id_checkbox.val();
                    if(el.find('.order-checklist-item-sortable').length == 0) {
                        checklist_ids_keep.push(checklist_id);
                    } else {
                        let checklist_group_id = el.find('.order-checklist-item-sortable').prevAll('.list-group-header:first').data('form-group-id');
                        let checklist_order = el.find('.order-checklist-item').index(el.find('.order-checklist-item-sortable'));
                        checklists.checklist.push(
                            {
                                'checklist_id': checklist_id,
                                'checklist_order': checklist_order,
                                'checklist_group_id': checklist_group_id
                            }
                        );
                    }

                }

            });

            checklists = JSON.stringify(checklists);
            let file_id = $('#add_to_checklist_file_id').val();
            let required = $('.checklist-item-required').val();


            let formData = new FormData();
            formData.append('checklists', checklists);
            formData.append('file_id', file_id);
            formData.append('required', required);
            formData.append('checklist_ids_keep', checklist_ids_keep);

            axios.post('/doc_management/save_add_to_checklists', formData, axios_options)
            .then(function (response) {
                $('#add_to_checklists_modal').modal('hide');
                $('#add_form_to_checklists_div').html('');
                let order = $('#list_div_' + checklist_item_group_id).find('.uploads-filter-sort').val();
                get_forms(checklist_item_group_id, state, order);
                setTimeout(function() {
                    show_manage_upload(checklist_form_id, checklist_item_group_id);
                }, 100);
                toastr['success']('Form Successfully Added To Checklists');
                $('#save_add_to_checklists_button').html('<i class="fal fa-check mr-2"></i> Save');
            })
            .catch(function (error) {

            });

        }
    }

    function sortable() {
        $('.checklist-items-sortable').sortable({
            handle: '.order-checklist-item-sortable-handle',
            placeholder: 'bg-orange-sortable',
            stop: function( event, ui ) {
                reorder_checklists();
                $(ui.item).closest('tr').addClass('bg-blue-light');
            }
        });
    }

    function order_checklists() {
        $('.show-checklist-items-collapsible').off('click').on('click', function() {
            $('.checklist-items-collapsible').collapse('hide');
            if($(this).text() == 'Show Checklist Items') {
                $('.show-checklist-items-collapsible').text('Show Checklist Items').addClass('btn-primary').removeClass('btn-success');
                $(this).text('Hide Checklist Items').removeClass('btn-primary').addClass('btn-success');
            } else {
                $(this).text('Show Checklist Items').addClass('btn-primary').removeClass('btn-success');
            }
        });
        $('.checklist-items-sortable').disableSelection();

    }

    function reorder_checklists() {
        $('.order-checklist-item').each(function() {
            $(this).find('.checklist-item-order').text($(this).index() + 1);
        });
    }

    function show_confirm_remove(form_id, form_name, form_group_id, manage_form_state) {
        $('#remove_form_modal').modal();
        $('#remove_form_name').text(form_name);
        $('#confirm_remove_from_checklist_button').off('click').on('click', function () {
            let formData = new FormData();
            formData.append('form_id', form_id);
            axios.post('/doc_management/remove_upload', formData, axios_options)
            .then(function (response) {
                $('#remove_form_modal').modal('hide');
                let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
                get_forms(form_group_id, manage_form_state, order);
                $('#form_manage_modal').modal('hide');
                toastr['success']('Form Successfully Removed');
            })
            .catch(function (error) {

            });
        });
    }

    function show_confirm_replace(form_group_id, manage_form_state, old_form_id, manage_form_name, new_form_id, new_form_name) {
        $('#replace_form_modal').modal();
        $('#replace_old_form').text(manage_form_name);
        $('#replace_new_form').text(new_form_name);
        $('#confirm_replace_form_button').off('click').on('click', function () {
            let formData = new FormData();
            formData.append('old_form_id', old_form_id);
            formData.append('new_form_id', new_form_id);
            axios.post('/doc_management/replace_upload', formData, axios_options)
            .then(function (response) {
                $('#replace_form_modal').modal('hide');
                let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
                get_forms(form_group_id, manage_form_state, order);
                $('#form_manage_modal').modal('hide');
                toastr['success']('Form Successfully Replaced');
            })
            .catch(function (error) {

            });
        });
    }

    function sort_uploads(ele) {
        let order = ele.val();
        let list_div = ele.closest('.list-div');
        let form_group_id = list_div.find('.form-group-id').val();
        let state = list_div.find('.form-group-state').val();
        get_forms(form_group_id, state, order);

    }

    function activate_upload(ele) {
        let upload_id = ele.data('id');
        let form_group_id = ele.data('form-group-id');
        let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
        let state = ele.data('state');
        let active = ele.data('active');
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        formData.append('active', active);
        axios.post('/doc_management/activate_upload', formData)
            .then(function (response) {
                get_forms(form_group_id, state, order);
                let msg;
                if (active == 'yes') {
                    msg = 'Form Activated Successfully';
                } else {
                    msg = 'Form Deactivated Successfully';
                }
                toastr['success'](msg);
            })
            .catch(function (error) {

            });
    }

    function filter_uploads(ele) {

        let list_div = ele.closest('.list-div');
        list_div.find('.uploads-list').show();

        let filter_active = list_div.find('.uploads-filter-active').val();
        let filter_publish = list_div.find('.uploads-filter-published').val();
        list_div.find('.uploads-filter-active').prop('disabled', false);

        // filter published first since a form cannot be notactive unless it's published first
        if (filter_publish == 'published') {
            list_div.find('.notpublished').hide();
            if (filter_active == 'active') {
                list_div.find('.published.notactive').hide();
            } else if (filter_active == 'notactive') {
                list_div.find('.published.active').hide();
            }
        } else if (filter_publish == 'notpublished') {
            list_div.find('.published').hide();
            list_div.find('.uploads-filter-active').val('all').prop('disabled', true);
        } else {
            if (filter_active == 'active') {
                list_div.find('.uploads-list').show();
                list_div.find('.notactive').hide();
            } else if (filter_active == 'notactive') {
                list_div.find('.uploads-list').hide();
                list_div.find('.published.notactive').show();
            }
        }
        //select_refresh();

    }

    function confirm_publish_upload(ele) {

        let upload_id = ele.data('id');
        let form_group_id = ele.data('form-group-id');
        let state = ele.data('state');

        $('#confirm_publish_modal').modal();

        $('#confirm_publish').off('click').on('click', function () {
            publish_upload(upload_id, form_group_id, state);
        });
    }

    function publish_upload(upload_id, form_group_id, state) {
        let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        axios.post('/doc_management/publish_upload', formData)
            .then(function (response) {
                $('#confirm_publish_modal').modal('hide');
                get_forms(form_group_id, state, order);
                toastr['success']('Form Published Successfully');
            })
            .catch(function (error) {

            });
    }

    function duplicate_upload(ele) {
        let upload_id = ele.data('id');
        let form_group_id = ele.data('form-group-id');
        let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
        let state = ele.data('state');
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        axios.post('/doc_management/duplicate_upload', formData)
            .then(function (response) {
                get_forms(form_group_id, state, order);
                toastr['success']('Form Duplicated Successfully');
            })
            .catch(function (error) {

            });
    }

    function confirm_delete_upload(ele) {

        let upload_id = ele.data('id');
        let form_group_id = ele.data('form-group-id');
        let state = ele.data('state');

        $('#confirm_delete_modal').modal();

        $('#confirm_delete').off('click').on('click', function () {
            delete_upload(upload_id, form_group_id, state);
        });
    }

    function delete_upload(upload_id, form_group_id, state) {
        let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
        let formData = new FormData();
        formData.append('upload_id', upload_id);
        axios.post('/doc_management/delete_upload', formData)
            .then(function (response) {
                $('#confirm_delete_modal').modal('hide');
                get_forms(form_group_id, state, order);
                toastr['success']('Form Deleted Successfully');
            })
            .catch(function (error) {

            });
    }

    function edit_upload(ele) {

        let upload_id = ele.data('id');

        axios.get('/doc_management/get_upload_details', {
            params: {
                upload_id: upload_id
            },
        })
            .then(function (response) {

                let disabled = false;
                if(response.data.published == 'yes') {
                    disabled = true;
                }

                let file_name_orig = response.data.file_name_orig;
                let file_name = response.data.file_name_display;
                let form_group_id = response.data.form_group_id;
                let state = response.data.state;
                let helper_text = response.data.helper_text;
                let form_categories = response.data.form_categories;
                let form_tags = response.data.form_tags;
                let checklist_group_id = response.data.checklist_group_id;
                //let form = $('#edit_file_form');
                //form.find('select').val('');
                $('#edit_form_name').text(file_name_orig);
                $('#edit_file_name_display').val(file_name);
                $('#edit_form_group_id').val(form_group_id).prop('disabled', disabled).data('form-group-id', form_group_id);
                $('#edit_state').val(state).prop('disabled', disabled);
                $('#edit_helper_text').val(helper_text);
                $('#edit_form_tags').val(form_tags);
                $('#edit_checklist_group_id').val(checklist_group_id).prop('disabled', disabled);
                form_categories = form_categories.split(',');

                $('#edit_form_categories').find('option').prop('selected', false);
                $.each(form_categories, function (i, e) {
                    $('#edit_form_categories').find('option[value="' + e + '"]').prop('selected', true);
                });
                setTimeout(function() {
                    $('#edit_form_categories').trigger('change');
                }, 200);

                $('#edit_file_id').val(upload_id);

                $('#edit_file_modal').modal();

                $('#save_edit_file_button').off('click').on('click', save_edit_file);

            })
            .catch(function (error) {

            });
    }

    function show_add_non_form_item(ele) {
        // show modal
        $('#add_item_no_form_modal').modal();

        // set values for from group and state
        let state = ele.data('state');
        let form_group_id = ele.data('form-group-id');
        $('#no_form_form_group_id').val(form_group_id);
        $('#no_form_state').val(state);
        $('#no_form_form_categories').val('');
        $('#no_form_form_tags').val('');
        $('#no_form_checklist_group_id').val('');

        $('#save_add_item_no_form_button').off('click').on('click', save_non_form_item);

        //select_refresh();

        $('#no_form_form_group_id').on('change', function () {
            $('#no_form_state').val(ele.find('option:selected').data('state'));
            //select_refresh();
        });
    }

    function save_non_form_item() {

        let form = $('#add_item_no_form_form');
        let form_check = validate_form(form);

        if (form_check == 'yes') {

            let form_group_id = $('#no_form_form_group_id').val();
            let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
            let state = $('#no_form_state').val();

            $('#save_add_item_no_form_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving');

            let formData = new FormData(form[0]);

            axios.post('/doc_management/save_add_non_form', formData)
                .then(function (response) {
                    $('#add_item_no_form_modal').modal('hide');
                    get_forms(form_group_id, state, order);
                    $('#save_add_item_no_form_button').prop('disabled', false).html('<i class="fad fa-upload mr-2"></i> Save Details');
                    toastr['success']('Item Added Successfully');
                    $('#list_' + form_group_id).trigger('click');
                })
                .catch(function (error) {
                    //
                });

        }

    }

    function save_edit_file() {

        let form = $('#edit_file_form');
        let form_check = validate_form(form);

        if (form_check == 'yes') {

            let form_group_id = $('#edit_form_group_id').val();
            let prev_form_group_id = $('#edit_form_group_id').data('form-group-id');
            let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();
            let state = $('#edit_state').val();

            $('#save_edit_file_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving');

            let formData = new FormData(form[0]);

            axios.post('/doc_management/save_file_edit', formData)
                .then(function (response) {
                    $('#edit_file_modal').modal('hide');
                    get_forms(form_group_id, state, order);
                    if(prev_form_group_id != '') {
                        get_forms(prev_form_group_id, state, order);
                    }
                    $('#save_edit_file_button').prop('disabled', false).html('<i class="fad fa-upload mr-2"></i> Save Details');
                    toastr['success']('Upload Edited Successfully');
                })
                .catch(function (error) {
                    //
                });

        }

    }

    function show_upload(ele) {

        $('#add_upload_modal').modal();

        $('#form_name').html('');

        let state = ele.data('state');
        let form_group_id = ele.data('form-group-id');
        $('#form_group_id').val(form_group_id);
        $('#state').val(state);
        //console.log($('#form_categories').val());
        $('#form_categories').val('');
        $('#form_tags').val('');
        $('#checklist_group_id').val('');

        $('.show-forms-button').hide();

        //select_refresh();

        setTimeout(function () {

            $('#file_upload').off('change').on('change', function () {

                if($(this).val() != '') {

                    global_loading_on('', '<div class="h5 text-white">Scanning Upload</div>');

                    let form = $('#upload_file_form');
                    let formData = new FormData(form[0]);

                    axios.post('/doc_management/get_upload_text', formData, axios_options)
                    .then(function (response) {

                        /* $('#upload_preview').html('<img src="'+response.data.upload_location+'" width="100%">'); */
                        $('#upload_preview').html('<embed src="'+response.data.upload_location+'#view=FitW" width="100%" height="100%">');

                        $('.form-names').fadeIn('slow');
                        $('#form_names').html('');
                        $('#form_names_div').collapse('show');
                        $('#form_name').html('<h5 class="text-orange">Select and/or Edit Form Name</h5>');

                        response.data.titles.forEach(function(title) {
                            let row = ' \
                            <div class="d-flex justify-content-start align-items-center title-option w-100"> \
                                <div><a href="javascript: void(0)" class="btn btn-primary add-title">Select</a></div> \
                                <div class="w-100"><input type="text" class="custom-form-element form-input" value="' + title + '" data-label=""></div> \
                            </div> \
                            ';
                            $('#form_names').append(row);
                        });

                        $('.add-title').on('click', function() {
                            $('.show-forms-button').show();
                            let title = $(this).closest('.title-option').find('input').val();
                            $('#file_name_display, #helper_text').val(title);
                            $('#file_name_display').on('change', function() {
                                if($('#helper_text').val() == title) {
                                    $('#helper_text').val($('#file_name_display').val());
                                }
                            });
                            $('#form_names_div').collapse('hide');
                        });

                        global_loading_off();

                    })
                    .catch(function (error) {

                    });
                }


                /* let form_name = $('.file-path').val().replace(/\.pdf/, '');
                form_name = form_name.replace(/^[0-9\.\s-]+/, '');
                form_name = form_name.replace(/^\([a-zA-Z\s]+\)/, '');
                form_name = form_name.replace(/^[-\s]{1}/, '');
                form_name = form_name.replace(/[0-9\.\s_-]+$/, '');
                $('#file_name_display, #helper_text').val(form_name).trigger('change'); */
            });
            $('#form_group_id').on('change', function () {
                $('#state').val(ele.find('option:selected').data('state'));
                //select_refresh();
            });
        }, 500);

    }

    function upload_file() {

        let form_check = validate_form($('#upload_file_form'));

        if (form_check == 'yes') {

            let form_group_id = $('#form_group_id').val();
            let state = $('#state').val();
            let order = $('#list_div_' + form_group_id).find('.uploads-filter-sort').val();

            $('#upload_file_button').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading');

            let formData = new FormData($('#upload_file_form')[0]);

            axios_options['header'] = { 'content-type': 'multipart/form-data' };
            axios.post('/doc_management/upload_file', formData, axios_options)
                .then(function (response) {
                    $('#add_upload_modal').modal('hide');
                    $('#file_name_display, #file_upload, #form_categories, #form_tags, #checklist_group_id').val('');

                    get_forms(form_group_id, state, order);
                    $('#upload_file_button').prop('disabled', false).html('<i class="fad fa-upload mr-2"></i> Upload Form');
                    $('#upload_preview').html('');
                    $('#helper_text').val('');
                })
                .catch(function (error) {
                    //
                });
        }

    }




}
