if (document.URL.match(/commission/) || document.URL.match(/transaction_details/)) {

    let page = 'commission';
    if(document.URL.match(/details/)) {
        page = 'details';
    } else if(document.URL.match(/commission_other/)) {
        page = 'other';
    }

    $(function() {

        if(page == 'commission') {
            //form_elements();
            global_format_money();
            get_check_info();
            get_commissions_pending();
            get_checks();


            $('#add_check_button').off('click').on('click', function() {
                show_fields();
                $('#add_check_in_modal').modal('show');
                $('#add_check_in_modal').on('hide.bs.modal', function() {
                    clear_add_check_form();
                });
            });

            $('#search_deleted_checks').on('keyup', search_deleted_checks);
            $(document).on('click', '.undo-delete-queue-check', undo_delete_queue_check);

            $(document).on('click', '#save_edit_queue_check_button', save_edit_queue_check);

            data_table($('#deleted_checks_table'), [1, 'desc'], [0, 8], [], false, false, false, false);

        }

        $(document).on('click', '#save_add_check_in_button', save_add_check_in);

    });

    function get_commissions_pending() {

        axios.get('/doc_management/commission/get_commissions_pending', {
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.commissions-pending').html(response.data);
            data_table($('.commissions-pending-table').eq(0), [1, 'desc'], [0], [], true, true, true, true);
        })
        .catch(function (error) {

        });
    }

    function get_checks() {

        axios.get('/doc_management/commission/get_checks_queue', {
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('.commission-checks-queue').html(response.data);

            data_table($('.checks-queue-table').eq(0), [1, 'desc'], [0, 7], [], true, true, true, true);
            data_table($('.checks-queue-table').eq(1), [1, 'desc'], [0, 8], [], true, true, true, true);

            $('.edit-queue-check-button').off('click').on('click', show_edit_queue_check);
            $('.delete-check-button').off('click').on('click', show_delete_queue_check);
            $('.undo-delete-check-button').off('click').on('click', undo_delete_queue_check);

            //select_refresh();

        })
        .catch(function (error) {

        });
    }

    window.get_check_info = function() {
        // get check info when adding a check
        $('#check_in_upload').off('change').on('change', function () {

            if($(this).val() != '') {

                $(this).closest('.form-ele').find('label').addClass('active');

                $('#check_in_date').val('');
                $('#check_in_amount').val('');
                $('#check_in_number').val('');

                global_loading_on('', '<div class="h5 text-white">Scanning Check</div>');
                let form = $('#add_check_in_form');
                let formData = new FormData(form[0]);
                axios.post('/agents/doc_management/transactions/get_check_details', formData, axios_options)
                .then(function (response) {
                    if(response.data.check_date) {
                        $('#check_in_date').val(response.data.check_date);
                        $('#check_in_amount').val(response.data.check_amount);
                        $('#check_in_number').val(response.data.check_number);
                    }
                    $('.check-in-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+response.data.check_location+'" class="w-100"></div>');
                    global_loading_off();
                    let modal_div = document.getElementById('add_check_in_modal');
                    modal_div.scrollTop = modal_div.scrollHeight;

                })
                .catch(function (error) {

                });
            }

        });
    }


    window.save_add_check_in = function() {

        let Commission_ID = $('#Commission_ID').val() ?? null;

        if(page == 'commission' && $('[name=check_in_type]:checked').val() == 'other') {
            if($('#check_in_client_name').val() == '' && $('#check_in_street').val() == '') {
                $('#modal_danger').modal('show').find('.modal-body').html('You must enter either the Client\'s Name or a Street Address');
                return false;
            }
        }

        let form = $('#add_check_in_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#save_add_check_in_button').prop('disabled', true).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Check...');
            let formData = new FormData(form[0]);
            formData.append('page', page);
            if(page == 'details' || page == 'other') {
                formData.append('Commission_ID', Commission_ID);
            }

            axios.post('/agents/doc_management/transactions/save_add_check_in', formData, axios_options)
            .then(function (response) {

                toastr['success']('Check Successfully Added');

                if(page == 'details' || page == 'other') {
                    get_checks_in(Commission_ID);
                    /* if(page == 'other') {
                        save_commission();
                    } */
                } else {
                    $('#add_check_div').collapse('hide');
                    clear_add_check_form();
                    get_checks();
                }

                $('#add_check_in_modal').modal('hide');
                $('#save_add_check_in_button').prop('disabled', false).html('<i class="fal fa-check mr-2"></i> Save');

            })
            .catch(function (error) {

            });

        }

    }

    window.show_edit_queue_check = function() {

        let button = $(this);

        $('#edit_queue_check_modal').modal('show');

        setTimeout(function() {
            $('.edit-queue-check-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+button.data('image-location')+'" class="w-100"></div>');
            $('#edit_queue_commission_id').val(button.data('commission-id'));
            $('#edit_queue_check_id').val(button.data('check-id'));
            $('#edit_queue_check_agent_id').val(button.data('check-agent-id'));
            $('#edit_queue_check_date').val(button.data('check-date'));
            $('#edit_queue_check_number').val(button.data('check-number'));
            $('#edit_queue_check_amount').val(button.data('check-amount'));
            $('#edit_queue_check_date_received').val(button.data('date-received'));
            $('#edit_queue_check_date_deposited').val(button.data('date-deposited'));
            $('#edit_queue_check_street').val(button.data('street'));
            $('#edit_queue_check_city').val(button.data('city'));
            $('#edit_queue_check_state').val(button.data('state'));
            $('#edit_queue_check_zip').val(button.data('zip'));
            $('#edit_queue_check_client_name').val(button.data('client-name'));

            $('#edit_queue_check_type').val('sale');
            $('.other_row').hide();
            $('.address').addClass('required').closest('.form-ele').find('.form-select-value-input').addClass('required-form-ele');
            if(button.hasClass('other')) {
                $('#edit_queue_check_type').val('other');
                $('.other_row').show();
                $('.address').removeClass('required').closest('.form-ele').find('.form-select-value-input').removeClass('required-form-ele');
            }

            //select_refresh();

        }, 100);



    }

    window.save_edit_queue_check = function() {

        if($('#edit_queue_check_type').val() == 'other') {
            if($('#edit_queue_check_client_name').val() == '' && $('#edit_queue_check_street').val() == '') {
                $('#modal_danger').modal('show').find('.modal-body').html('You must enter either the Client\'s Name or a Street Address');
                return false;
            }
        }

        let form = $('#edit_queue_check_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#save_edit_queue_check_button').prop('disabled', true).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Saving Check...');

            let formData = new FormData(form[0]);

            axios.post('/doc_management/commission/save_edit_queue_check', formData, axios_options)
            .then(function (response) {

                toastr['success']('Check Successfully Edited');

                get_checks();

                $('#edit_queue_check_modal').modal('hide');
                $('#save_edit_queue_check_button').prop('disabled', false).html('<i class="fal fa-check mr-2"></i> Save');

            })
            .catch(function (error) {

            });

        }

    }

    /* window.save_edit_queue_check = function() {

        let Commission_ID = $('#Commission_ID').val();
        let form = $('#edit_check_in_form');
        let formData = new FormData(form[0]);

        axios.post('/agents/doc_management/transactions/save_edit_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Edited');
        })
        .catch(function (error) {

        });
    } */

    window.show_delete_queue_check = function() {
        let check_id = $(this).data('check-id');
        //let commission_id = $(this).data('commission-id');
        let type = $(this).data('type');

        $('#confirm_modal').modal('show').find('.modal-body').html(' \
        <div class="d-flex justify-content-start align-items-center"> \
            <div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div> \
            <div class="text-center w-100">Are you sure you want to delete this check?<br>It will be moved to the deleted checks section.</div> \
        </div>');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Check');
        $('#confirm_button').off('click').on('click', function() {
            save_delete_queue_check(check_id, type);
        });
    }

    window.save_delete_queue_check = function(check_id, type) {

        let formData = new FormData();
        formData.append('check_id', check_id);
        formData.append('type', type);
        axios.post('/agents/doc_management/transactions/save_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks();
            toastr['success']('Check Successfully Deleted');

        })
        .catch(function (error) {

        });
    }

    window.undo_delete_queue_check = function() {

        let button = $(this);
        let check_id = button.data('check-id');
        let type = button.data('type');
        let formData = new FormData();
        formData.append('check_id', check_id);
        formData.append('type', type);
        axios.post('/agents/doc_management/transactions/undo_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks();
            button.closest('tr').fadeOut();
            toastr['success']('Check Successfully Reactivated');
        })
        .catch(function (error) {

        });

    }

    function search_deleted_checks() {

        let val = $('#search_deleted_checks').val();

        if(val.length > 0) {

            axios.get('/doc_management/commission/search_deleted_checks', {
                params: {
                    val: val
                }
            })
            .then(function (response) {

                let checks_in_queue = response.data.checks_in_queue;
                let checks_in = response.data.checks_in;
                let checks = checks_in_queue.concat(checks_in);
                //console.log(checks);

                $('#deleted_checks').html('');

                if(checks.length > 0) {

                    $('#deleted_checks_div').collapse('show');

                    checks.forEach(function(check) {

                        let agent_name = check['agent_name'] != null ? check['agent_name'] : '';
                        let client_name = check['client_name'] != null ? check['client_name'] : '';

                        let result = ' \
                        <tr> \
                            <td><a href="javascript: void(0)" class="btn btn-sm btn-primary btn-block m-0 undo-delete-queue-check" data-check-id="'+check['id']+'" data-type="'+check['type']+'"><i class="fad fa-recycle mr-2"></i> Reactivate</a></td> \
                            <td>'+check['date_received']+'</td> \
                            <td>'+agent_name+'</td> \
                            <td>'+client_name+'</td> \
                            <td>'+check['street']+' '+check['city']+', '+check['state']+' '+check['zip']+'</td> \
                            <td>'+check['check_number']+'</td> \
                            <td>'+check['check_date']+'</td> \
                            <td>$'+check['check_amount']+'</td> \
                            <td><a href="'+check['file_location']+'" target="_blank" class="btn btn-sm btn-block m-0 btn-primary"><i class="fad fa-eye mr-2"></i> View Check</a></td> \
                        ';
                        $('#deleted_checks').append(result);
                    });

                } else {

                    $('#deleted_checks_div').collapse('hide');

                }
            })
            .catch(function (error) {

            });

        } else {

        }
    }


    window.clear_add_check_form = function() {
        if(page == 'commission') {
            $('#add_check_in_form').find('[type=radio]').prop('checked', false);
            $('.add-check-in-form-div').hide();
            show_fields();
        }
        $('#add_check_in_form').find('.custom-form-element').not('[type=radio]').val('').trigger('change');
        $('.check-in-preview-div').html('');
        $('#check_in_date_received').val(format_date(new Date().getTime()));
        //select_refresh();

    }

    window.show_fields = function() {

        // show sale or BPO sections
        $('[name=check_in_type]').on('change', function() {

            $('.add-check-in-form-div').addClass('animate__fadeIn').show();

            if($('[name=check_in_type]:checked').val() == 'other') {
                $('.other_row').show();
                $('.address').removeClass('required').closest('.form-ele').find('.form-select-value-input').removeClass('required-form-ele');
            } else {
                $('.other_row').hide();
                $('.address').addClass('required').closest('.form-ele').find('.form-select-value-input').addClass('required-form-ele');
            }

        });
    }

}


