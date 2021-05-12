if (document.URL.match(/transaction_details/)) {

    $(function() {



    });

    window.agent_commission_init = function() {

        $('#admin_fee_in_total').on('keyup change', function() {
            if($(this).val().replace(/[,\$]/g, '') > 0) {
                //$('.client-paid-admin').removeClass('hidden');
                //$('.agent-paid-admin').addClass('hidden');
                $('#admin_fee_from_client').val($(this).val());
                $('#admin_fee_from_agent').val('$0.00');
            } else {
                //$('.client-paid-admin').addClass('hidden');
                //$('.agent-paid-admin').removeClass('hidden');
                $('#admin_fee_from_client').val('$0.00');
                $('#admin_fee_from_agent').val($('#admin_fee_from_agent').data('default-value'));
            }
        });

        if($('.admin-fee-in-total').length == 0) {

            $('#admin_fee_from_client').on('keyup change', function() {
                if($(this).val() != '$0.00' && $(this).val() != '0') {
                    $('#admin_fee_from_agent').val('$0.00');
                }
            });

            $('#admin_fee_from_agent').on('keyup change', function() {
                if($(this).val() != '$0.00' && $(this).val() != '0') {
                    $('#admin_fee_from_client').val('$0.00');
                }
            });

        }



        $('#add_deduction_button').on('click', function() {
            let html = $('#deduction_template').html();
            $('#deduction_container').append(html);
            $('#deduction_container').find('.row.template').find('input').addClass('custom-form-element form-input');
            $('#deduction_container').find('.row.template').removeClass('template');
            global_format_money();
            numbers_only_agent();
        });

        $(document).on('click', '.delete-deduction-button', function() {
            if($(this).hasClass('fedex-delete')) {
                $('#add_fedex').prop('checked', false);
            }
            $(this).closest('.row').remove();

            total_agent_commission();
        });

        $('#add_fedex').on('change', function() {
            if($(this).is(':checked')) {
                let html = $('#deduction_template').html();
                $('#deduction_container').append(html);
                $('#deduction_container').find('.row.template').find('input').addClass('custom-form-element form-input');
                $('#deduction_container').find('.row.template').removeClass('template');
                $('.deduction-description').last().val('FedEx');
                $('.deduction-amount').last().val('$22.00');
                $('.delete-deduction-button').last().addClass('fedex-delete');

                $('#delivery_method').val('fedex');

                numbers_only_agent();

            } else {
                $('.fedex-delete').closest('.row').remove();
                if($('#delivery_method').val() == 'fedex') {
                    $('#delivery_method').val('');
                }
            }
            total_agent_commission();
        });

        $('.pay-from-commission-button').on('click', function() {

            let type = $(this).data('type');
            let amount = $(this).data('amount');

            let desc = type == 'dues' ? 'Dues Payment' : 'E&O Payment';

            let html = $('#deduction_template').html();
            $('#deduction_container').append(html);
            $('#deduction_container').find('.row.template').find('input').addClass('custom-form-element form-input');
            $('#deduction_container').find('.row.template').removeClass('template');
            $('.deduction-description').last().val(desc);
            $('.deduction-amount').last().val('$'+amount);
            $('.deduction-payment-type').last().val(type);
            $('.delete-deduction-button').last().addClass('fedex-delete');

            numbers_only_agent();

            total_agent_commission();

        });

        if($('#referral_company_deduction').length > 0) {
            $('#checks_in_total').on('change', function() {
                let deduction = parseFloat($(this).val().replace(/[,\$]/g, '')) * .15;
                $('#referral_company_deduction').val(deduction.toFixed(2));
            });
        }

        $(document).on('keyup change', '.total-trigger, .deduction-amount', function(e) {
            if($(this).val() != '') {
                total_agent_commission();
            }
            /* if(e.eventType == 'change') {
                global_format_money();
            } */
        });

        $('.address-input').each(function() {
            $(this).data('default-value', $(this).val());
        });

        $('#delivery_method').on('change', function() {
            delivery_method();
        });

        $('#save_agent_commission_button').off('click').on('click', function() {
            $(this).prop('disabled', true);
            save_agent_commission();
        });

        setTimeout(function() {
            delivery_method();
            numbers_only_agent();
            total_agent_commission();
            setTimeout(function() {
                global_format_money();
            }, 100);
        }, 100);


    }


    window.delivery_method = function() {

        if($('#delivery_method').val().match(/(mail|fedex)/)) {
            $('.mail-details').removeClass('hidden').find('.address-input').each(function() {
                $(this).val($(this).data('default-value')).addClass('required');
            });
        } else {
            $('.mail-details').addClass('hidden').find('.address-input').val('').removeClass('required');
        }

    }


    window.save_agent_commission = function() {

        $('.disabled').prop('disabled', false);

        let form = $('#commission_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            if($('#delivery_method').val().match(/(mail|fedex)/) && !$('#mail_disclosure').is(':checked')) {
                $('.disclosure-div').addClass('invalid-border');
                toastr['error']('Please accept the "Required Authorization"');
                $('#save_agent_commission_button').prop('disabled', false);

                $('#mail_disclosure').on('change', function() {
                    if($(this).is(':checked')) {
                        $('.disclosure-div').removeClass('invalid-border');
                    }
                });
                return false;
            }

            $('#save_agent_commission_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving...');

            let form = $('#commission_form');
            let formData = new FormData(form[0]);

            let Commission_ID = $('#Commission_ID').val();
            let Agent_ID = $('#Agent_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            formData.append('Commission_ID', Commission_ID);
            formData.append('Agent_ID', Agent_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);

            axios.post('/agents/doc_management/transactions/save_commission_agent', formData, axios_options)
            .then(function (response) {
                $('#save_agent_commission_button').prop('disabled', false).html('<i class="fad fa-save mr-2"></i> Save Details');
                $('.disabled').prop('disabled', true);
                $('html, body').animate({ scrollTop: 0 }, 'slow');
                $('#modal_success').modal().find('.modal-body').html('Your Commission Breakdown was successfully submitted!<br><br>You may edit any details until the review process is completed by our staff.');

            })
            .catch(function (error) {
                console.log(error);
            });

        } else {

            $('#save_agent_commission_button').prop('disabled', false);
            $('.disabled').prop('disabled', true);

        }

    }


    window.total_agent_commission = function() {


        let checks_in_total = parseFloat($('#checks_in_total').val().replace(/[,\$]/g, ''));
        let admin_fee_in_total = parseFloat($('#admin_fee_in_total').val().replace(/[,\$]/g, ''));
        let earnest_deposit_amount = parseFloat($('#earnest_deposit_amount').val().replace(/[,\$]/g, ''));

        let total_income = checks_in_total + admin_fee_in_total + earnest_deposit_amount;
        $('#total_income').val(total_income);

        let admin_fee_from_client = parseFloat($('#admin_fee_from_client').val().replace(/[,\$]/g, ''));

        let sub_total = total_income - admin_fee_from_client;
        $('#sub_total').val(sub_total);

        let agent_commission_deduction_percent = parseFloat($('#agent_commission_deduction_percent').val() / 100);
        let agent_commission_deduction_amount = agent_commission_deduction_percent * sub_total;
        $('#agent_commission_deduction').val(agent_commission_deduction_amount);

        let admin_fee_from_agent = parseFloat($('#admin_fee_from_agent').val().replace(/[,\$]/g, ''));

        let referral_company_deduction = 0;
        if($('#referral_company_deduction').length > 0) {
            referral_company_deduction = parseFloat($('#referral_company_deduction').val().replace(/[,\$]/g, ''));
        }

        let deductions_total = agent_commission_deduction_amount + admin_fee_from_agent + referral_company_deduction;
        $('.deduction-amount').each(function() {
            if($(this).val() != '') {
                deductions_total += parseFloat($(this).val().replace(/[,\$]/g, ''));
            }
        });
        $('#commission_deductions_total').val(deductions_total);

        let total_commission_to_agent = sub_total - deductions_total;
        $('#total_commission_to_agent').val(total_commission_to_agent);

        if(total_commission_to_agent < 0) {
            $('.commission-total-row').removeClass('bg-success').addClass('bg-danger');
            $('.commission-total-input').removeClass('text-success').addClass('text-danger');
        } else {
            $('.commission-total-row').removeClass('bg-danger').addClass('bg-success');
            $('.commission-total-input').removeClass('text-danger').addClass('text-success');
        }

    }


    window.numbers_only_agent = function() {
        $('.numbers-only').on('focus', function () {
            $(this).select();
        });

        $('.numbers-only').on('change', function() {
            if($(this).hasClass('money') || $(this).hasClass('money-decimal')) {
                if($(this).val() == '') {
                    $(this).val('$0.00');
                }
            }
        });
    }

}
