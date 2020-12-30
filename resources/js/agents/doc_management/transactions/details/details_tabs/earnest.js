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

    window.get_earnest_checks = function(type) {

        let Earnest_ID = $('#Earnest_ID').val();
        axios.get('/agents/doc_management/transactions/get_earnest_checks_'+type, {
            params: {
                Earnest_ID: Earnest_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('#earnest_checks_'+type+'_div').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.add_earnest_check = function(type) {

        $('#add_earnest_check_modal').modal('show');

        $('.check-in, .check-out').hide();
        $('#add_earnest_check_name, #add_earnest_check_payable_to').removeClass('required');
        if(type == 'in') {
            $('.check-in').show();
            $('#add_earnest_check_name').addClass('required');
        } else if(type == 'out') {
            $('.check-out').show();
            $('#add_earnest_check_payable_to').addClass('required');
        }

        $('#add_earnest_check_type').val(type);

    }

    window.save_add_earnest_check = function() {

        let Earnest_ID = $('#Earnest_ID').val();
        let form = $('#add_earnest_check_form');
        let formData = new FormData(form[0]);
        formData.append('Earnest_ID', Earnest_ID);

        let validate = validate_form(form);

        if(validate == 'yes') {

            let type = $('#add_earnest_check_type').val();

            $('#save_add_earnest_check_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving...');

            axios.post('/agents/doc_management/transactions/save_add_earnest_check', formData, axios_options)
            .then(function (response) {
                toastr['success']('Check Successfully Added');
                $('#add_earnest_check_modal').modal('hide');
                clear_add_earnest_check_form();
                $('#save_add_earnest_check_button').html('<i class="fad fa-check mr-2"></i> Save');
                get_earnest_checks(type);
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
