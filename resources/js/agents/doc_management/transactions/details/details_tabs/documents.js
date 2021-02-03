import { find } from 'lodash';
import Sortable from 'sortablejs';

if (document.URL.match(/transaction_details/)) {


    $(function() {

        $(document).on('click', 'a, .btn, .dropdown-item, .check-all', function(e) {

            /* let classes = e.target.classList;
            let id = e.target.id; */

            let ele = $(this);
            let id = ele.attr('id');
            let classes = ele[0].classList;

            if(classes.contains('add-folder-button')) {
                show_add_folder();
            } else if(classes.contains('check-all')) {
                check_all(ele);
            } else if(classes.contains('doc-get-signed-button')) {
                get_signed(ele); // has document-id
            } else if(classes.contains('doc-delete-button')) {
                show_delete_one_document(ele);
            }  else if(classes.contains('delete-folder-button')) {
                confirm_delete_folder(ele.data('folder-id'));
            }  else if(classes.contains('doc-rename-button')) {
                show_rename_document(ele);
            }  else if(classes.contains('doc-split-button')) {
                show_split_document(ele);
            }  else if(classes.contains('doc-duplicate-button')) {
                duplicate_document(ele);
            }  else if(classes.contains('doc-print-button')) {
                print_pdf(ele.data('link'));
            }  else if(classes.contains('docs-download-button')) {
                print_download_documents('download', ele.data('type'));
            }  else if(classes.contains('docs-print-button')) {
                print_download_documents('print', ele.data('type'));
            }  else if(classes.contains('doc-email-button')) {
                email_get_documents(ele);
            }  else if(classes.contains('docs-email-button')) {
                email_get_documents(ele);
            }  else if(classes.contains('delete-address-button')) {
                ele.closest('.row').remove();
            }  else if(classes.contains('remove-emailed-document')) {
                remove_emailed_document(ele);
            } else if(classes.contains('move-documents-button')) {
                show_move_documents();
            } else if(classes.contains('delete-documents-button')) {
                show_delete_documents();
            } else if(id == 'upload_documents_button') {
                show_upload_documents();
            } else if(id == 'add_checklist_template_button') {
                show_add_checklist_template();
            } else if(id == 'save_add_checklist_template_button') {
                $('#save_add_checklist_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
                save_add_template_documents('checklist');
            } else if(id == 'add_individual_template_button') {
                show_add_individual_template();
            } else if(id == 'save_add_individual_template_button') {
                if($('.individual-template-form:checked').length > 0) {
                    $('#save_add_individual_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
                    save_add_template_documents('individual');
                } else {
                    $('#modal_danger').modal().find('.modal-body').html('You must select at least one form to add');
                }
            } else if(id == 'send_email_button') {
                email_documents();
            }
        });


        $(document).on('change', '.check-document', show_bulk_options);



    });

    window.get_signed = function(ele) {

        let Listing_ID = $('#Listing_ID').val() > 0 ? $('#Listing_ID').val() : 0;
        let Contract_ID = $('#Contract_ID').val() > 0 ? $('#Contract_ID').val() : 0;
        let Referral_ID = $('#Referral_ID').val() > 0 ? $('#Referral_ID').val() : 0;
        let transaction_type = $('#transaction_type').val();
        let Agent_ID = $('#Agent_ID').val();
        let User_ID = $('#User_ID').val();
        let document_ids = [];
        if(ele.data('document-id')) {
            document_ids.push(ele.data('document-id'));
        } else {
            $('.check-document:checked').each(function () {
                document_ids.push($(this).data('document-id'));
            });
        }

        document_ids = document_ids.join(',');

        window.open('/esign/esign_add_documents/'+User_ID+'/'+document_ids+'/'+Agent_ID+'/'+Listing_ID+'/'+Contract_ID+'/'+Referral_ID+'/'+transaction_type, '_blank');

    }

    window.in_process = function(document_ids) {

        let formData = new FormData();
        formData.append('document_ids', document_ids);
        axios.post('/agents/doc_management/transactions/in_process', formData, axios_options)
        .then(function (response) {
            show_in_process(response.data.in_process);
            clear_in_process(response.data.not_in_process);
        })
        .catch(function (error) {

        });

    }

    function show_in_process(document_ids) {
        if(document_ids.length > 0) {
            document_ids.forEach(function (document_id) {
                let doc_div = $('.document-div[data-document-id="' + document_id + '"]');
                doc_div.addClass('in-process').find('.check-document').prop('disabled', true);
                doc_div.find('.in-process-icon').show();
                doc_div.find('.document-title a').css({ opacity: '0.4' });
                doc_div.find('.btn').prop('disabled', true);
                $('#in_process_div').show();
            });
        }
    }

    function clear_in_process(document_ids) {
        if($('.in-process').length > 0) {
            if(document_ids.length > 0) {
                document_ids.forEach(function (document_id) {
                    let doc_div = $('.document-div[data-document-id="' + document_id + '"]');
                    doc_div.removeClass('in-process').find('.check-document').prop('disabled', false);
                    doc_div.find('.in-process-icon').hide();
                    doc_div.find('.document-title a').css({ opacity: '1' });
                    doc_div.find('.btn').prop('disabled', false);
                });
            }
        } else {
            $('#in_process_div').hide();
        }
    }

    window.remove_emailed_document = function(ele) {

        let document_id = ele.data('document-id');

        let formData = new FormData();
        formData.append('document_id', document_id);
        axios.post('/agents/doc_management/transactions/delete_emailed_document', formData, axios_options)
        .then(function (response) {

            let list_group = ele.closest('.list-group');
            ele.closest('.list-group-item').addClass('animate__animated animate__fadeOut');
            setTimeout(function() {
                ele.closest('.list-group-item').remove();
                if(list_group.find('.list-group-item').length == 0) {
                    $('#emailed_documents_container').hide();
                    $('#emailed_documents_div').html('');
                }
            }, 700);

        })
        .catch(function (error) {

        });

    }

    window.get_emailed_documents = function() {

        if($('#emailed_documents_div').html() == '') {

            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();
            let Agent_ID = $('#Agent_ID').val();

            axios.get('/agents/doc_management/transactions/get_emailed_documents', {
                params: {
                    Listing_ID: Listing_ID,
                    Contract_ID: Contract_ID,
                    Referral_ID: Referral_ID,
                    transaction_type: transaction_type,
                    Agent_ID: Agent_ID
                }
            })
            .then(function (response) {

                $('#emailed_documents_div').html('');

                if(response.data.length > 0) {

                    response.data.forEach(function (doc) {
                        let doc_div = ' \
                        <div class="list-group-item list-group-item-action py-1 d-flex justify-content-between align-items-center emailed-document" data-document-id="'+doc.id+'" data-file-size="'+doc.file_size+'" data-file-name-display="'+doc.file_name_display+'"> \
                            <div class="d-flex justify-content-start align-items-center"> \
                                <div class="mr-4"> \
                                    <a href="javascript: void(0)" class="remove-emailed-document" data-document-id="'+doc.id+'"><i class="fal fa-times text-danger"></i></a> \
                                </div> \
                                <div> \
                                    '+doc.file_name_display+' \
                                </div> \
                            </div> \
                            <div> \
                                <a href="'+doc.file_location+'" target="_blank">View</a> \
                            </div> \
                        </div> \
                        ';
                        $('#emailed_documents_div').append(doc_div);
                        $('#emailed_documents_container').show();
                    });

                    //select_refresh($('#emailed_documents_container'));

                }

                $('#add_emailed_documents_button').off('click').on('click', add_emailed_documents);

            })
            .catch(function (error) {

            });

        }

    }

    window.add_emailed_documents = function() {

        //global_loading_on('', '<h4 class="text-white">Importing Emailed Documents...</h4>');

        let document_ids = [];
        let files = [];
        $('.emailed-document').each(function () {

            document_ids.push($(this).data('document-id'));

            let file_data = {};
            file_data['file_name_display'] = $(this).data('file-name-display');
            file_data['file_size'] = ($(this).data('file-size') / (1024*1024)).toFixed(2);
            files.push(file_data);

        });

        file_progress(files);

        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        let transaction_type = $('#transaction_type').val();
        let folder = $('#emailed_documents_folder').val();

        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('Agent_ID', Agent_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('folder', folder);

        axios.post('/agents/doc_management/transactions/add_emailed_documents', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
                global_loading_off();
            })
            .catch(function (error) {

            });

    }

    window.print_download_documents = function(task, type) {
        let document_ids = [];
        $('.check-document:checked').each(function () {
            document_ids.push($(this).data('document-id'));
        });

        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('type', type);

        axios.post('/agents/doc_management/transactions/merge_documents', formData, axios_options)
            .then(function (response) {
                let file_location = response.data.file_location;
                let filename = response.data.filename;
                if(task == 'print') {
                    print_pdf(file_location);
                } else if(task == 'download') {
                    let a = document.createElement('A');
                    a.href = file_location;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            })
            .catch(function (error) {

            });
    }

    window.email_documents = function() {

        $('#send_email_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Sending Email');
        let from = $('#email_from').val();
        let to_addresses = [];
        $('.to-addresses').each(function() {
            if($(this).find('.email-to-address').val() != '') {
                to_addresses.push({
                    type: $(this).find('.email-address-type').val(),
                    address: $(this).find('.email-to-address').val()
                });
            }
        });
        let subject = $('#email_subject').val();
        let message = $('#email_message').val();
        let attachments = [];
        $('#email_attachments').find('.attachment-row').each(function() {
            attachments.push({
                filename: $(this).data('file-name'),
                file_location: $(this).data('file-location')
            });
        });

        let formData = new FormData();
        formData.append('type', 'documents');
        formData.append('from', from);
        formData.append('to_addresses', JSON.stringify(to_addresses));
        formData.append('subject', subject);
        formData.append('message', message);
        formData.append('attachments', JSON.stringify(attachments));
        axios.post('/agents/doc_management/transactions/send_email', formData, axios_options)
        .then(function (response) {
            if(response.data.fail) {
                $('#modal_danger').modal().find('.modal-body').html('The attachments you are sending are too large. They must total less than 20MB and they are currently '+response.data.attachment_size+'MB');
            }
            $('#send_email_button').html('<i class="fad fa-share mr-2"></i> Send Email');
            $('#send_email_modal').modal('hide');
            toastr['success']('Documents Successfully Emailed');
        })
        .catch(function (error) {

        });
    }

    window.email_get_documents = function(ele) {

        let docs_type = ele.data('docs-type') ? ele.data('docs-type') : '';
        let document_ids = [];
        if(docs_type) {
            $('.check-document:checked').each(function () {
                document_ids.push(ele.data('document-id'));
            });
        } else {
            document_ids.push(ele.data('document-id'));
        }

        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('docs_type', docs_type);
        formData.append('type', 'filled');

        axios.post('/agents/doc_management/transactions/email_get_documents', formData, axios_options)
            .then(function (response) {

                let file_locations = response.data.file_locations;
                let filenames = response.data.filenames;

                file_locations.forEach(function (file_location, index) {
                    let file_name = filenames[index];
                    let attachment = ' \
                    <div class="d-flex justify-content-start attachment-row" data-file-name="' + file_name + '" data-file-location="' + file_location + '"> \
                        <div><a href="javascript: void(0)" class="delete-attachment-button"><i class="fal fa-times text-danger mr-2"></i></a></div> \
                        <div>' + file_name + '</div> \
                    </div>';
                    $('#email_attachments').append(attachment);
                });

                $('.delete-attachment-button').on('click', function() {
                    $(this).closest('.attachment-row').remove();
                });

                $('#send_email_modal').modal();
                $('#send_email_modal').on('hide.bs.modal', function () {
                    $('#email_attachments').html('');
                    $('.new-address').remove();
                });

                $('.add-address-button').off('click').on('click', function() {
                    let new_address_row = ' \
                    <div class="row to-addresses new-address"> \
                        <div class="col-2"> \
                            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search email-address-type"> \
                                <option value="to">To:</option> \
                                <option value="cc" selected>Cc:</option> \
                                <option value="bcc">Bcc:</option> \
                            </select> \
                        </div> \
                        <div class="col-9"> \
                            <input type="text" class="custom-form-element form-input email-to-address"> \
                        </div> \
                        <div class="col-1"> \
                            <div class="h-100 d-flex justify-content-end align-items-center"> \
                                <button class="btn btn-sm btn-danger delete-address-button"><i class="fal fa-times delete-address-button"></i></button> \
                            </div> \
                        </div> \
                    </div> \
                    ';
                    $(new_address_row).insertBefore($(this).closest('.row'));

                });


            })
            .catch(function (error) {

            });
    }

    window.duplicate_document = function(ele) {

        global_loading_on('', '<h4 class="text-white">Duplicating Form, Fields and Values...</h4>');
        let document_id = ele.data('document-id');
        let file_type = ele.data('file-type');
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        let formData = new FormData();
        formData.append('document_id', document_id);
        formData.append('file_type', file_type);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        axios.post('/agents/doc_management/transactions/duplicate_document', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
                global_loading_off();
                toastr['success']('Document successfully duplicated');
            })
            .catch(function (error) {

            });
    }

    window.show_split_document = function(ele) {

        $('#folder_id').val(ele.data('folder'));
        let document_id = ele.data('document-id');
        let checklist_id = ele.data('checklist-id');
        let file_type = ele.data('file-type');
        let file_name = ele.data('file-name');
        let transaction_type = $('#transaction_type').val();

        $('#split_document_modal').modal();

        axios.get('/agents/doc_management/transactions/get_split_document_html', {
            params: {
                document_id: document_id,
                checklist_id: checklist_id,
                file_type: file_type,
                file_name: file_name,
                transaction_type: transaction_type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
            .then(function (response) {
                $('#split_document_container').html(response.data);
                setTimeout(function () {

                    $('.image-slider').draggable({ axis: "x" });
                    $('.selected-images-slider').draggable({ axis: "x" });

                    $(document).on('click', '.image-zoom-button', function () {
                        $('#image_zoom_modal').modal();
                        $('#image_zoom_div').html('<img src="' + $(this).data('image-src') + '" class="image-preview">');
                    });

                    $(document).on('click', '.add-to-selected-button', function () {
                        let image_holder = $(this).closest('.image-holder');
                        image_holder.appendTo('.selected-images-slider').find('.image-order').text(image_holder.index() + 1);
                        $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', false);
                    });
                    $(document).on('click', '.remove-from-selected-button', function () {

                        let image_holder = $(this).closest('.image-holder');
                        image_holder.prependTo('.image-slider');

                        let image_divs = $('.image-slider').find('.image-holder');
                        image_divs.sort(function (a, b) {
                            return $(a).data('index') - $(b).data('index')
                        });
                        $('.image-slider').html(image_divs);

                        if ($('.selected-images-slider').find('.image-holder').length == 0) {
                            $('.add-docs-to-checklist-item-button, #save_document_name_button').prop('disabled', true);
                        } else {
                            $('.selected-images-slider').find('.image-holder').each(function () {
                                let index = $(this).index() + 1;
                                $(this).find('.image-order').text(index);
                            });
                        }

                    });

                    //form_elements();
                    $('[data-toggle="popover"]').popover();

                    $('#save_document_name_button').on('click', function() {
                        $(this).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Saving...');
                        save_document_name($(this));
                    });
                    $('.add-docs-to-checklist-item-button').on('click', function() {
                        $(this).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding...');
                        save_document_name($(this));
                    });

                }, 500);
            })
            .catch(function (error) {

            });

    }

    window.save_document_name = function(ele) {

        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        let Agent_ID = $('#Agent_ID').val();
        let folder_id = $('#folder_id').val();
        let file_id = ele.data('file-id') ?? null;

        let checklist_item_id = ele.data('checklist-item-id') ?? null;
        let checklist_id = ele.data('checklist-id') ?? null;
        let validate = 'yes';
        let document_name = ele.data('upload-id');
        if (!checklist_item_id) {
            let form = $('#document_name_form');
            validate = validate_form(form);
            document_name = $('#document_name').val();
        }
        if(document_name == '0') {
            document_name = ele.closest('.list-group-item').find('.document-name').text()
        }

        if (validate == 'yes') {

            let image_ids = [];
            let file_type = '';
            $('.selected-images-container').find('.image-holder').each(function () {
                file_type = $(this).data('file-type');
                image_ids.push($(this).data('document-image-id'));
            });

            let formData = new FormData();
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('Agent_ID', Agent_ID);
            formData.append('folder_id', folder_id);
            formData.append('document_name', document_name);
            formData.append('image_ids', image_ids);
            formData.append('file_type', file_type);
            formData.append('file_id', file_id);
            formData.append('checklist_item_id', checklist_item_id);
            formData.append('checklist_id', checklist_id);
            axios.post('/agents/doc_management/transactions/save_split_document', formData, axios_options)
                .then(function (response) {
                    toastr['success']('New Document Successfully Created');
                    $('.add-docs-to-checklist-item-button').html('<i class="fal fa-plus mr-1 mr-sm-2"></i> Add').prop('disabled', true);
                    $('#save_document_name_button').html('<i class="fad fa-save mr-2"></i> Save Document').prop('disabled', true);
                    $('.selected-images-slider').html('');
                    $('#split_document_modal').on('hide.bs.modal', function () {
                        load_tabs('documents');
                        //load_checklist_on_tab_click();
                    });
                    // change status and count of checklist items
                    ele.parent().next().html('<span class="badge checklist-item-badge bg-blue-light text-primary p-1" title="We have received your document for this item. It is in the review process"><span class="d-none d-sm-inline-block"><i class="fad fa-stopwatch fa-lg mr-1"></i> </span>Pending</span>');
                    let count = ele.closest('.list-group-item').find('.docs-count-badge').text();
                    count = parseInt(count);
                    ele.closest('.list-group-item').find('.docs-count-badge').text(count + 1);

                })
                .catch(function (error) {

                });
        }
    }

    window.show_rename_document = function(ele) {
        let document_id = ele.data('document-id');
        let document_name = ele.data('document-name');
        $('#rename_document_modal').modal();
        $('#new_document_name').val(document_name);
        $('#save_rename_document_button').on('click', function () {
            save_rename_document(document_id);
        });
    }

    window.save_rename_document = function(document_id) {
        let new_name = $('#new_document_name').val();

        let formData = new FormData();
        formData.append('document_id', document_id);
        formData.append('new_name', new_name);
        axios.post('/agents/doc_management/transactions/save_rename_document', formData, axios_options)
            .then(function (response) {
                toastr['success']('Document Successfully Renamed')
                load_tabs('documents', false);
                //load_checklist_on_tab_click();
            })
            .catch(function (error) {

            });
    }

    window.show_add_to_checklist = function() {

        let button = $(this);

        if($('#questions_confirmed').val() == 'yes') {

            add_to_checklist(button);

        } else {

            $('#required_fields_modal').modal();

            $('#save_required_fields_button').off('click').on('click', function() {

                let form = $('#required_fields_form');
                let validate = validate_form(form);

                if(validate == 'yes') {
                    let Contract_ID = $('#Contract_ID').val();
                    let formData = new FormData(form[0]);
                    formData.append('Contract_ID', Contract_ID);
                    axios.post('/agents/doc_management/transactions/save_required_fields', formData, axios_options)
                    .then(function (response) {

                        $('#required_fields_modal').modal('hide');
                        add_to_checklist(button);
                        $('#questions_confirmed').val('yes');

                    })
                    .catch(function (error) {

                    });

                }

            });

        }

    }

    window.add_to_checklist = function(button) {
        let checklist_id = button.data('checklist-id');
        let document_ids;
        let assigned = 'no';
        // single button add
        if (button.data('document-id')) {

            document_ids = [button.data('document-id')];
            if (button.hasClass('assigned')) {
                assigned = 'yes';
            }

        } else {
            // selecting multiple forms
            document_ids = [];
            if ($('.check-document:checked').not('.assigned').length == 0) {
                $('#modal_danger').modal().find('.modal-body').html('All documents you selected have already been assigned');
                return false;
            } else {
                $('.check-document:checked').not('.assigned').each(function () {
                    document_ids.push($(this).data('document-id'));
                });
                // if any of the docs are already assigned the user will be notified and they will be excluded
                if ($('.assigned:checked').length > 0) {
                    assigned = 'yes';
                }
            }

        }

        if (assigned == 'yes') {
            // notify user
            $('#modal_info').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-4"><i class="fad fa-exclamation-triangle fa-2x text-danger"></i></div><div class="text-gray text-center">Some documents you selected have already be assigned and will not be included</div></div>');
            let button = $('#modal_info').find('.modal-footer').find('.btn');
            button.text('Continue');

            button.on('click', function() {
                add_to_checklist_html(checklist_id, document_ids);
            });

        } else {

            add_to_checklist_html(checklist_id, document_ids);

        }
    }

    window.add_to_checklist_html = function(checklist_id, document_ids) {

        let transaction_type = $('#transaction_type').val();
        axios.get('/agents/doc_management/transactions/add_document_to_checklist_item_html', {
            params: {
                document_ids: document_ids,
                checklist_id: checklist_id,
                transaction_type: transaction_type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
            .then(function (response) {
                $('#add_items_to_checklist_div').html(response.data);

                disable_arrows();

                $('.doc-list-arrow').on('click', function() {
                    let new_active_item = '';
                    if($(this).data('dir') == 'up') {
                        new_active_item = $('.add-to-checklist-document-div.active').prev('.add-to-checklist-document-div');
                    } else {
                        new_active_item = $('.add-to-checklist-document-div.active').next('.add-to-checklist-document-div');
                    }
                    arrows(new_active_item);
                });

                $('.add-to-checklist-document-div').on('click', function() {
                    arrows($(this));
                });

                $('#add_to_checklist_documents_wrapper').off('wheel').on('wheel', function(e){
                    if(e.originalEvent.deltaY > 0) {
                        $('.add-to-checklist-document-div.active').next('.add-to-checklist-document-div').trigger('click');
                    } else {
                        $('.add-to-checklist-document-div.active').prev('.add-to-checklist-document-div').trigger('click');
                    }
                    return false;
                });

                $('.assign-button').off('click').on('click', assign_document);

                $('#save_add_to_checklist_button').off('click').on('click', function () {
                    $(this).hide();
                    save_add_to_checklist($(this).data('checklist-id'));
                });

                let matches = [];
                $('.add-to-checklist-document-div').each(function() {
                    let doc_name = $(this).data('file-name');
                    let match_document_id = $(this).data('document-id');
                    if($('.assign-button[data-file-name="' + doc_name + '"]').length == 1) {
                        matches.push(match_document_id);
                    }
                });


                if(matches.length > 0) {
                    setTimeout(function() {
                        $('#confirm_matches_modal').modal();
                    }, 500);
                    $('#confirm_matches_modal').find('#match_count').text(matches.length);
                    $('#confirm_matches_button').off('click').on('click', confirm_matches);
                    $('#cancel_matches_button').on('click', function() {
                        setTimeout(function() {
                            $('#add_to_checklist_modal').modal();
                        }, 500);
                        disable_closing_docs();
                    });
                } else {
                    setTimeout(function() {
                        $('#add_to_checklist_modal').modal();
                    }, 500);
                    disable_closing_docs();
                }

            })
            .catch(function (error) {

            });

    }


    window.disable_closing_docs = function() {

        $('.assign-button.closing_doc, .assign-button.release').addClass('disabled').prop('disabled', true);
        if(!$('.add-to-checklist-document-div.active').hasClass('assigned')) {
            if($('.assign-button.contract').not('.rejected').length > 0) {
                if($('.assign-button.contract').closest('.list-group-item').find('.docs').length > 0) {
                    $('.assign-button.closing_doc, .assign-button.release').removeClass('disabled').prop('disabled', false);
                }
            }
        }
    }

    window.confirm_matches = function() {
        let release_submitted = false;
        $('.add-to-checklist-document-div').each(function() {
            let doc = $(this);

            let doc_name = doc.data('file-name');
            if($('.assign-button[data-file-name="' + doc_name + '"]').length == 1) {
                doc.trigger('click');
                let id = 'rand_'+Math.floor(Math.random() * 10000000);
                $('.assign-button[data-file-name="' + doc_name + '"]').prop('id', id).trigger('click');
                setTimeout(function() {
                    if(document.getElementById(id)) {
                        document.getElementById(id).scrollIntoView();
                    }
                }, 500);
                if($('#'+id).hasClass('release') || $(id).hasClass('contract')) {
                    release_submitted = true;
                }
            }
        });
        $('#confirm_matches_modal').modal('hide');
        setTimeout(function() {
            $('#add_to_checklist_modal').modal();
        }, 1000);
        $('.add-to-checklist-document-div').not('.assigned').first().trigger('click');

        if(release_submitted == true) {
            // if contract item in checklist and not rejected
            if($('.assign-button.contract').not('.rejected').length > 0) {
                // if contract not submitted
                if($('.assign-button.contract').closest('.list-group-item').find('.docs').length == 0) {
                    $('.assign-button.closing_doc, .assign-button.release').closest('.list-group-item').find('.delete-doc').trigger('click');
                    $('#modal_danger').modal().find('.modal-body').html('You must submit a contract before you can submit any closing documents or a release');
                }
            }
        }

    }

    window.arrows = function(new_active_item) {

        let active_item = $('.add-to-checklist-document-div.active');
        active_item.removeClass('active shadow p-4').addClass('p-2').find('.helper').hide();

        let wrapper = $('#add_to_checklist_documents_wrapper');

        new_active_item.addClass('active shadow p-4').find('.helper').show();
        if(new_active_item.hasClass('assigned')) {
            $('.assign-button').prop('disabled', true);
        } else {
            $('.assign-button').not('.disabled').prop('disabled', false);
        }

        wrapper.scrollTop(wrapper.scrollTop() + new_active_item.position().top - (wrapper.height()/2) + (new_active_item.height()/2) + 30);

        disable_arrows();

    }


    window.disable_arrows = function() {

        let active_item = $('.add-to-checklist-document-div.active');
        $('.doc-list-arrow').prop('disabled', true);
        if(active_item.prev('.add-to-checklist-document-div').length > 0) {
            $('.doc-list-arrow[data-dir="up"]').prop('disabled', false);
        }
        if(active_item.next('.add-to-checklist-document-div').length > 0) {
            $('.doc-list-arrow[data-dir="down"]').prop('disabled', false);
        }

    }


    window.assign_document = function() {

        let checklist_id = $(this).data('checklist-id');
        let checklist_item_id = $(this).data('checklist-item-id');
        let active_div = $('.add-to-checklist-document-div.active');
        let document_id = active_div.data('document-id');
        let file_name = active_div.data('file-name');
        let docs_div = $(this).closest('.list-group-item').find('.submitted-docs');

        docs_div.closest('.submitted-docs-div').show();

        docs_div.prepend(' \
        <div class="added-document d-flex justify-content-between align-items-center docs" data-document-id="'+document_id+'" data-checklist-id="'+checklist_id+'" data-checklist-item-id="'+checklist_item_id+'"> \
            <div class="d-flex justify-content-start align-items-center text-success"> \
                <div><i class="fal fa-check-circle mr-2"></i></div> \
                <div>'+file_name.substring(0, 70)+'</div> \
            </div> \
            <div><a href="javascript: void(0)" class="delete-doc text-danger" data-document-id="'+document_id+'"><i class="fad fa-times-circle mr-1"></i> <span class="small">Remove</span></a></div> \
        </div>');

        active_div.addClass('assigned');
        if(active_div.nextAll('.add-to-checklist-document-div').not('.assigned').length > 0) {
            active_div.nextAll('.add-to-checklist-document-div').not('.assigned').first().trigger('click');
        } else {
            active_div.trigger('click');
        }

        disable_closing_docs();

        $('.delete-doc').on('click', function() {
            let remove_document_id = $(this).data('document-id');
            let container = $(this).closest('.submitted-docs-div');

            $(this).closest('.added-document').remove();
            $('.add-to-checklist-document-div[data-document-id="' + remove_document_id + '"]').removeClass('assigned');
            if(container.find('.added-document').length == 0) {
                container.hide();
            }

            if(document_id == remove_document_id) {
                $('.assign-button').prop('disabled', false);
            }
            disable_closing_docs();

        });

    }

    window.save_add_to_checklist = function(checklist_id) {


        let checklist_items = [];
        $('.added-document').each(function () {

            let checklist_id = $(this).data('checklist-id');
            let checklist_item_id = $(this).data('checklist-item-id');
            let document_id = $(this).data('document-id');

            checklist_items.push({
                checklist_id: checklist_id,
                checklist_item_id: checklist_item_id,
                document_id: document_id
            });

        });

        let formData = new FormData();

        let Agent_ID = $('#Agent_ID').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        checklist_items = JSON.stringify(checklist_items);

        formData.append('Agent_ID', Agent_ID);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('checklist_items', checklist_items);
        axios.post('/agents/doc_management/transactions/save_assign_documents_to_checklist', formData, axios_options)
            .then(function (response) {
                toastr['success']('Documents Successfully Added')
                load_tabs('documents', false);
                //load_checklist_on_tab_click();
                if(response.data) {
                    if(response.data.release_submitted == 'yes') {
                        $('#cancel_contract_button').trigger('click');
                        load_details_header();
                    }
                }
                $('#save_add_to_checklist_button').show();
            })
            .catch(function (error) {

            });

    }

    window.show_add_checklist_template = function() {
        $('#add_checklist_template_modal').modal();
    }

    /* window.tag_search = function() {

        let selected_tags = $('#form_categories_search').find('option:checked');

        if (selected_tags.length > 0) {

            let form_group = $('.select-form-group').val();

            if (form_group == 'all') {

                $('.form-group-div').each(function () {
                    let form_group = $(this);
                    if(form_group.find('.form-name').length > 0) {
                        form_group.find('.form-name').hide();
                        selected_tags.each(function () {
                            let tag = $(this).val();
                            form_group.find('.form-name').each(function () {
                                //console.log($(this).data('tags'), tag);
                                if (String($(this).data('tags')).match(new RegExp(tag, 'i'))) {
                                    // show name
                                    $(this).show();
                                }
                            });
                        });
                    }
                });

            } else {

                $('[data-form-group-id="' + form_group + '"]').find('.form-name').hide();
                selected_tags.each(function () {
                    let tag = $(this).val();
                    $('[data-form-group-id="' + form_group + '"]').find('.form-name').each(function () {
                        if (String($(this).data('tags')).match(new RegExp(tag, 'i'))) {
                            // show name
                            $(this).show();
                        }
                    });
                });

            }
        } else {
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
        }
    } */

    /* window.form_search = function() {
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
                let regex = new RegExp(v, 'i');
                if ($(this).data('text').match(regex)) {
                    // show name
                    $(this).show();
                    // show header
                    $(this).closest('.form-group-div').find('.list-group-header').show();
                }
            });
        }
    } */

    window.save_add_template_documents = function(type) {

        //$('[data-dismiss="modal"]').trigger('click');
        $('#add_individual_template_modal, #add_checklist_template_modal').modal('hide');

        let Agent_ID = $('#Agent_ID').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        let folder = '';
        let forms = '';
        let form = '';
        if (type == 'checklist') {
            folder = $('#checklist_templates_folder').val();
            forms = $('.checklist-template-form:checked');
            form = $('#add_checklist_template_form');
        } else {
            folder = $('#individual_templates_folder').val();
            forms = $('.individual-template-form:checked');
            form = $('#add_individual_template_form');
        }

        let formData = new FormData();
        formData.append('Agent_ID', Agent_ID);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('folder', folder);

        let files = [];
        let c = 0;
        forms.each(function () {

            let file_data = {};
            file_data['file_id'] = $(this).data('file-id');
            file_data['template_id'] = $(this).data('template-id');
            file_data['file_name'] = $(this).data('file-name');
            file_data['file_size'] = $(this).data('file-size');
            file_data['file_name_display'] = $(this).data('file-name-display');
            file_data['pages_total'] = $(this).data('pages-total');
            file_data['file_location'] = $(this).data('file-location');
            file_data['order'] = c;
            files.push(file_data);

            c += 1;

        });

        let files_json = JSON.stringify(files);
        formData.append('files', files_json);

        let validate = validate_form(form);
        if (validate == 'yes') {

            file_progress(files);

            axios.post('/agents/doc_management/transactions/save_add_template_documents', formData, axios_options)
                .then(function (response) {

                    load_tabs('documents');
                    //load_checklist_on_tab_click();

                    setTimeout(function() {

                        let sortables = $('.document-div[data-folder-id="' + folder + '"]');
                        reorder_documents(sortables);

                        $('#save_add_checklist_template_button').html('<i class="fal fa-check mr-2"></i> Add Documents').off('click').on('click', function() {
                            if($('.checklist-template-form:checked').length > 0) {
                                $('#save_add_individual_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
                                save_add_template_documents('checklist');
                            } else {
                                $('#modal_danger').modal().find('.modal-body').html('You must select at least one form to add');
                            }
                        });

                        $('#save_add_individual_template_button').html('<i class="fal fa-check mr-2"></i> Add Documents').off('click').on('click', function() {
                            if($('.individual-template-form:checked').length > 0) {
                                $('#save_add_individual_template_button').html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Documents...').prop('disabled', true);
                                save_add_template_documents('individual');
                            } else {
                                $('#modal_danger').modal().find('.modal-body').html('You must select at least one form to add');
                            }
                        });

                        global_loading_off();
                        toastr['success']('Documents Successfully Added');

                        $('.progress-bar').css({ width: '0%' });
                        $('.individual-template-form').prop('checked', false);

                    }, 200);


                })
                .catch(function (error) {

                });
        }
    }

    window.file_progress = function(files) { // file_name_display and file_size

        global_loading_off();

        let loading_html = ' \
        <div class="h5 text-white mb-3">Importing Documents...</div> \
        <div class="w-100 text-left document-loading-container"> \
            <div id="loading_div"></div> \
        </div> \
        ';

        global_loading_on('', loading_html);

        files.forEach(function(file, index) {

            // bigger the interval longer it takes
            let multiplier = 900;
            if(file['pages_total']) {
                multiplier = 300;
            }

            let interval = file['file_size'] * multiplier;

            let file_html = ' \
            <div class="text-white w-100 p-1"> \
                <span>'+file['file_name_display']+'</span> \
                <div class="progress"> \
                    <div id="progress_'+index+'" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div> \
                </div> \
            </div> \
            ';
            setTimeout(function () {
                $('#loading_div').append(file_html);
                let progress_bar = $('#progress_'+index);
                let width = 0;
                let progress_interval = setInterval(progress, interval);
                function progress() {
                    if (width >= 100) {
                        clearInterval(progress_interval);
                        progress_bar.removeClass('progress-bar-striped').addClass('bg-success text-center');
                    } else {
                        width++;
                        progress_bar.css({ width: width + '%' }).attr('aria-valuenow', width);
                    }
                }
                document.getElementById('progress_'+index).scrollIntoView();
            }, index * 1000);

        });

    }

    window.show_add_individual_template = function() {
        $('#add_individual_template_modal').modal();
        // search forms
        $('.form-search').on('keyup', function() {
            form_search($(this))
        });
        /* // search by tag
        $('#form_categories_search').off('change').on('change', tag_search); */
        // select and show form groups
        $('.select-form-group').on('change', function () {
            select_form_group();
        });
    }

    window.select_form_group = function () {
        // clear search input
        $('.form-search').val('');
        $('#form_categories_search').val('');

        // if all show everything or just the selected group
        if ($('.select-form-group').val() == 'all') {
            $('.form-group-div, .list-group-header, .form-name').show();
        } else {
            $('.list-group-header, .form-name').show();
            $('.form-group-div').hide();
            $('[data-form-group-id="' + $('.select-form-group').val() + '"]').show();
        }
        //select_refresh($('#add_individual_template_modal'));
    }

    window.show_upload_documents = function() {
        $('#upload_documents_modal').modal();
        upload_documents();
        $('#save_upload_documents_button').on('click', function () {
            $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Uploading Documents');
            $("#file_upload").dmUploader('start');
        });
        $(document).on('click', '.cancel-upload', function () {
            $("#file_upload").dmUploader('cancel', $(this).data('id'));
            $(this).closest('li').remove();
            $('#save_upload_documents_button').html('<i class="fal fa-check mr-2"></i> Upload Documents');
        });
    }

    window.show_add_folder = function() {
        $('#add_folder_modal').modal();
        setTimeout(function () {
            $('#new_folder_name').focus();
        }, 500);
        $('#save_add_folder_button').on('click', add_folder);
    }

    window.add_folder = function() {
        let form = $('#add_folder_form');
        let validate = validate_form(form);
        if (validate == 'yes') {
            let folder = $('#new_folder_name').val();
            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let Agent_ID = $('#Agent_ID').val();
            let transaction_type = $('#transaction_type').val();
            let formData = new FormData();
            formData.append('folder', folder);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('Agent_ID', Agent_ID);
            formData.append('transaction_type', transaction_type);
            axios.post('/agents/doc_management/transactions/add_folder', formData, axios_options)
                .then(function (response) {
                    load_tabs('documents', false);
                    $('.modal').modal('hide');
                })
                .catch(function (error) {

                });
        }
    }

    window.confirm_delete_folder = function(folder_id) {
        $('#confirm_delete_folder_modal').modal();
        $('#confirm_delete_folder_button').on('click', function () {
            delete_folder(folder_id);
            $('#confirm_delete_folder_modal').modal('hide');
        });
    }

    window.delete_folder = function(folder_id) {
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        let formData = new FormData();
        formData.append('folder_id', folder_id);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        axios.post('/agents/doc_management/transactions/delete_folder', formData, axios_options)
            .then(function (response) {
                load_tabs('documents', false);
                //load_checklist_on_tab_click();
            })
            .catch(function (error) {

            });
    }

    window.show_move_documents = function() {
        $('#move_documents_modal').modal();
        $('#save_move_documents_button').on('click', move_documents);
    }

    window.move_documents = function() {
        $('.documents-container').fadeOut('1000');
        let document_ids = [];
        $('.check-document:checked').each(function () {
            document_ids.push($(this).data('document-id'));
        });

        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        let folder_id = $('#move_documents_folder').val();
        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        formData.append('folder_id', folder_id);
        axios.post('/agents/doc_management/transactions/move_documents_to_folder', formData, axios_options)
            .then(function (response) {
                load_tabs('documents');
                //load_checklist_on_tab_click();
                $('#move_documents_modal').modal('hide');
            })
            .catch(function (error) {

            });
    }

    window.show_delete_one_document = function(ele) {
        let document_id = ele.data('document-id');
        let document_name = ele.data('document-name');
        $('#confirm_delete_document_modal').modal();
        $('#delete_document_name').text(document_name);
        $('#confirm_delete_document_button').off('click').on('click', function () {
            delete_one_document(document_id);
        });
    }

    window.delete_one_document = function(document_id) {

        $('.documents-container').fadeOut('1000');
        let document_ids = [document_id];
        let formData = new FormData();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        axios.post('/agents/doc_management/transactions/move_documents_to_trash', formData, axios_options)
            .then(function (response) {
                //button.closest('.document-div').appendTo($('.folder-div').last().find('[id^=documents_folder_]'));
                load_tabs('documents', false);
                //load_checklist_on_tab_click();
                toastr['success']('Document Moved To Trash');
            })
            .catch(function (error) {

            });
    }

    window.show_delete_documents = function() {
        $('#confirm_delete_documents_modal').modal();
        $('#confirm_delete_documents_button').off('click').on('click', delete_documents);
    }

    window.delete_documents = function() {
        $('.documents-container').fadeOut('1000');
        let document_ids = [];
        $('.check-document:checked').not('.assigned').each(function () {
            document_ids.push($(this).data('document-id'));
        });

        let formData = new FormData();

        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        formData.append('document_ids', document_ids);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('Referral_ID', Referral_ID);
        formData.append('transaction_type', transaction_type);
        axios.post('/agents/doc_management/transactions/move_documents_to_trash', formData, axios_options)
            .then(function (response) {
                load_tabs('documents', false);
                //load_checklist_on_tab_click();
                toastr['success']('Documents Moved To Trash');
                $('#confirm_delete_documents_modal').modal('hide');
            })
            .catch(function (error) {

            });
    }

    window.show_bulk_options = function() {
        if ($('#add_documents_div').hasClass('show')) {
            $('#add_documents_div').collapse('hide');
        }
        if ($('.check-document:checked').length > 0) {
            $('#bulk_options_div').collapse('show');
            return false;
        } else {
            $('#bulk_options_div').collapse('hide');
            $(this).closest('.folder-div').find('.check-all').prop('checked', false);
        }
        $('.check-document').closest('.document-div').removeClass('bg-blue-light');
        $('.check-document:checked').closest('.document-div').addClass('bg-blue-light');

    }

    window.check_all = function(ele) {
        if ($('#add_documents_div').hasClass('show')) {
            $('#add_documents_div').collapse('hide');
        }
        if (!ele.closest('.folder-header').next('.collapse').hasClass('show')) {
            ele.closest('.folder-header').next('.collapse').collapse('show');
        }
        if (ele.is(':checked')) {
            ele.closest('.folder-div').find('.document-div').find('input').not('input:disabled').prop('checked', true);
        } else {
            ele.closest('.folder-div').find('.document-div').find('input').prop('checked', false);
        }
        show_bulk_options();
    }

    /* window.toggle_caret = function(ele) {
        console.log('toggling');
        let i = ele.find('i');
        if (i.hasClass('fa-angle-right')) {
            i.removeClass('fa-angle-right').addClass('fa-angle-down');
        } else if (i.hasClass('fa-angle-down')) {
            i.removeClass('fa-angle-down').addClass('fa-angle-right');
        }
    } */

    window.print_pdf = function(url) {

        var wnd = window.open(url);
        setTimeout(function() {
            wnd.print();
            //wnd.close();
        }, 500);

    }





}
