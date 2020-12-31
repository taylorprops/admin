if (document.URL.match(/transaction_details/)) {

    /* TODO:
    earnest_held_by
        needs to be disabled if there are checks in
        on change - show/hide checks div

    when saving checks
        update commission tab details - earnest amount
        update property - earnest amount

    */


    //// functions

    window.get_earnest_checks = function(check_type) {

        let Earnest_ID = $('#Earnest_ID').val();

        //$('#earnest_checks_'+check_type+'_div').html('');

        axios.get('/agents/doc_management/transactions/get_earnest_checks', {
            params: {
                Earnest_ID: Earnest_ID,
                check_type: check_type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('#earnest_checks_'+check_type+'_div').html(response.data);

            form_elements();

            $('.cleared-checkbox').off('change').on('change', function() {
                cleared_bounced($(this));
            });

            $('.delete-check-button').off('click').on('click', function() {
                delete_earnest_check($(this));
            });

            $('.undo-delete-check-button').off('click').on('click', function() {
                undo_delete_check($(this));
            })

            $('.earnest-check-div.in.inactive').appendTo('#earnest_checks_in_div');
            $('.earnest-check-div.out.inactive').appendTo('#earnest_checks_out_div');

            $('.show-deleted-checks-button').off('click').on('click', function() {
                let delete_check_type = $(this).data('check-type');
                $('.earnest-check-div.'+delete_check_type+'.inactive').toggleClass('hidden');
            });



            // set totals for in and out sections
            $('#checks_'+check_type+'_total').html(global_format_number_with_decimals($('#earnest_checks_'+check_type+'_total_amount').val()));

            // get in escrow amount
            let checks_in_total = $('#checks_in_total').html().replace(/[\$,]/g, '');
            if(checks_in_total == '') {
                checks_in_total = '0.00';
            }
            let checks_out_total = $('#checks_out_total').html().replace(/[\$,]/g, '');
            if(checks_out_total == '') {
                checks_out_total = '0.00';
            }
            let in_escrow_parsed = parseFloat(checks_in_total).toFixed(2) - parseFloat(checks_out_total).toFixed(2);
            in_escrow = global_format_number_with_decimals(in_escrow_parsed.toFixed(2));
            $('#in_escrow').html(in_escrow);

            $('.in-escrow-alert').removeClass('alert-success alert-danger').addClass('alert-info');
            if(in_escrow_parsed > 0) {
                $('.in-escrow-alert').removeClass('alert-info').addClass('alert-success');
            } else if(in_escrow_parsed < 0) {
                $('.in-escrow-alert').removeClass('alert-info').addClass('alert-danger');
            }

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.undo_delete_check = function(ele) {

        let check_id = ele.data('check-id');
        let check_type = ele.data('check-type');
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/undo_delete_earnest_check', formData, axios_options)
        .then(function (response) {

            get_earnest_checks(check_type);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.delete_earnest_check = function(ele) {

        let check_id = ele.data('check-id');
        let check_type = ele.data('check-type');
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/delete_earnest_check', formData, axios_options)
        .then(function (response) {

            get_earnest_checks(check_type);

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.cleared_bounced = function(checkbox) {

        let other_checkbox = checkbox.closest('.checkbox-li').find('.cleared-checkbox').not(checkbox);
        other_checkbox.prop('checked', false);

        let formData = new FormData();
        let check_id = checkbox.data('check-id');
        let check_type = checkbox.data('check-type');
        let status = '';
        if(checkbox.is(':checked')) {
            status = checkbox.val();
        }

        formData.append('check_id', check_id);
        formData.append('status', status);
        formData.append('check_type', check_type);

        axios.post('/agents/doc_management/transactions/clear_bounce_earnest_check', formData, axios_options)
        .then(function (response) {
            get_earnest_checks(check_type);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    window.add_earnest_check = function(check_type) {

        $('#add_earnest_check_modal').modal('show');

        $('.check-in, .check-out').hide();
        $('#add_earnest_check_name, #add_earnest_check_payable_to').removeClass('required');
        if(check_type == 'in') {
            $('.check-in').show();
            $('#add_earnest_check_name').addClass('required');
        } else if(check_type == 'out') {
            $('.check-out').show();
            $('#add_earnest_check_payable_to').addClass('required');
        }

        $('#add_earnest_check_type').val(check_type);

    }

    window.save_add_earnest_check = function() {

        let Earnest_ID = $('#Earnest_ID').val();
        let form = $('#add_earnest_check_form');
        let formData = new FormData(form[0]);
        formData.append('Earnest_ID', Earnest_ID);

        let validate = validate_form(form);

        if(validate == 'yes') {

            let check_type = $('#add_earnest_check_type').val();

            $('#save_add_earnest_check_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving...');

            axios.post('/agents/doc_management/transactions/save_add_earnest_check', formData, axios_options)
            .then(function (response) {
                toastr['success']('Check Successfully Added');
                $('#add_earnest_check_modal').modal('hide');
                clear_add_earnest_check_form();
                $('#save_add_earnest_check_button').html('<i class="fad fa-check mr-2"></i> Save');
                get_earnest_checks(check_type);
            })
            .catch(function (error) {
                console.log(error);
            });
        }

    }

    window.get_earnest_check_info = function() {
        // get check info when adding a check
        $('#add_earnest_check_upload').off('change').on('change', function () {

            if($(this).val() != '') {

                $('#add_earnest_check_date').val('');
                $('#add_earnest_check_amount').val('');
                $('#add_earnest_check_number').val('');

                global_loading_on('', '<div class="h5 text-white">Scanning Check</div>');
                let form = $('#add_earnest_check_form');
                let formData = new FormData(form[0]);
                axios.post('/agents/doc_management/transactions/get_check_details', formData, axios_options)
                .then(function (response) {
                    if(response.data.check_date) {
                        $('#add_earnest_check_date').val(response.data.check_date);
                        $('#add_earnest_check_amount').val(response.data.check_amount);
                        $('#add_earnest_check_number').val(response.data.check_number);
                    }
                    $('.add-earnest-check-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+response.data.check_location+'" class="w-100"></div>');
                    global_loading_off();

                })
                .catch(function (error) {
                    console.log(error);
                });
            }

        });
    }

    window.save_earnest = function () {

        let form = $('#earnest_form');
        let formData = new FormData(form[0]);
        formData.append('Earnest_ID', Earnest_ID);

        axios.post('/agents/doc_management/transactions/save_earnest', formData, axios_options)
        .then(function (response) {
            toastr['success']('Details Saved Successfully');
            load_tabs('details');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.clear_add_earnest_check_form = function() {
        $('#add_earnest_check_form, #edit_earnest_check_form').find('input, select').val('');
        $('.add-earnest-check-preview-div, .edit-earnest-check-preview-div').html('');
    }

}
