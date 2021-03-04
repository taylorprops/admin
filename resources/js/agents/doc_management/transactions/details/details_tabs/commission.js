if (document.URL.match(/transaction_details/) || document.URL.match(/commission_other/)) {

    let page = 'other';
    if(document.URL.match(/transaction_details/)) {
        page = 'details';
    }
    $(function() {

        $(document).on('click', '.show-view-add-button', popout_row);

        $(document).on('keyup change', '.total', total_commission);

        $(document).on('click', '#save_commission_button', function() {
            save_commission('yes');
        });

        $(document).on('change', '#using_heritage', function() {
            show_title();
        });


        $(document).on('mouseup', function (e) {
            var container = $('.popout-row, .modal-backdrop, .modal, .export-deductions-button');
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $('.popout-action, .popout').removeClass('active bg-blue-light lightSpeedInRight lightSpeedOutRight');
                $('.popout').hide();
                $('.commission-details-tabs').animate({ opacity: '1' });
            }
        });


    });

    window.commission_init = function(Commission_ID, Agent_ID) {

        //$('.popout').eq(0).show();
        get_checks_in(Commission_ID);
        get_checks_out(Commission_ID);
        get_commission_notes(Commission_ID);
        get_income_deductions(Commission_ID);
        get_commission_deductions(Commission_ID);
        get_agent_details(Agent_ID);
        get_agent_commission_details(Commission_ID);
        setTimeout(function() {
            save_commission('no');
        }, 2000);

        show_title();
        // $('#using_heritage').on('change', function() {
        //     show_title();
        // });

        $('.add-check-in-button').off('click').on('click', show_add_check_in);
        $('.add-check-out-button').off('click').on('click', show_add_check_out);

        $('#save_add_check_in_button').off('click').on('click', save_add_check_in);
        $('#save_add_check_out_button').off('click').on('click', save_add_check_out);

        $('#save_add_income_deduction_button').off('click').on('click', function() {
            save_add_income_deduction();
        });

        $('#add_income_deduction_div').on('hidden.bs.collapse', function () {
            $('#income_deduction_description, #income_deduction_amount').val('');
        });

        $('#save_add_commission_deduction_button').off('click').on('click', function() {
            save_add_commission_deduction();
        });

        $('#add_commission_deduction_div').on('hidden.bs.collapse', function () {
            $('#commission_deduction_description, #commission_deduction_amount').val('');
        });

        $('.save-commission-notes-button').off('click').on('click', add_commission_notes);

        $(document).on('click', '.export-deductions-button', add_deductions_to_breakdown);


        numbers_only();

    }

    window.numbers_only = function() {
        $('.numbers-only').on('focus', function () {
            $(this).select();
        });

        $('.numbers-only').on('change', function() {
            if($(this).val() == '') {
                $(this).val('$0.00');
            }
        });
    }

    window.add_deductions_to_breakdown = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }

        $('.deduction-row').each(function() {

            let description = $(this).find('.deduction-description').text();
            let amount = $(this).find('.deduction-amount').text();

            let formData = new FormData();
            formData.append('Commission_ID', Commission_ID);
            formData.append('description', description);
            formData.append('amount', amount);

            axios.post('/agents/doc_management/transactions/save_add_commission_deduction', formData, axios_options)
            .then(function (response) {
            })
            .catch(function (error) {

            });

        });

        $('.commission-popout-button').trigger('click');
        document.getElementById('commission_deductions_popout').scrollIntoView();

        toastr['success']('Deduction Successfully Added');

        setTimeout(function() {
            get_commission_deductions(Commission_ID);
            save_commission('no');
        }, 500);

    }

    window.get_agent_commission_details = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/details/data/get_agent_commission_details', {
            params: {
                Commission_ID: Commission_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.agent-commission-div').html(response.data);
        })
        .catch(function (error) {

        });
    }

    window.get_agent_details = function(Agent_ID) {
        axios.get('/agents/doc_management/transactions/details/data/get_agent_details', {
            params: {
                Agent_ID: Agent_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.agent-details-div').html(response.data);
            $('.show-soc-sec').on('click', function() {
                $('.soc-sec').toggle();
            });
        })
        .catch(function (error) {

        });
    }

    window.import_check_in = function() {

        let button = $(this);
        let check_id = button.data('check-id');
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }

        let formData = new FormData();
        formData.append('Commission_ID', Commission_ID);
        formData.append('check_id', check_id);

        axios.post('/agents/doc_management/transactions/import_check_in', formData, axios_options)
        .then(function (response) {
            toastr['success']('Check Successfully Imported');
            get_checks_in(Commission_ID);
            $('#add_check_in_modal').modal('hide');
            get_checks_in_queue();
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.show_title = function() {

        $('#title_company_row').hide();
        //$('#title_company').removeClass('required');
        if($('#using_heritage').val() == 'no') {
            $('#title_company_row').show();
            //$('#title_company').addClass('required');
        } else {
            $('#title_company').val('Heritage Title, Ltd');
        }
    }


    window.total_commission = function() {

        let fields_filled = true;
        $('.total').each(function() {
            if($(this).val() == '') {
                fields_filled = false;
                $(this).val('0.00');
            }
        });

        if(fields_filled == true) {

            let total = 0;

            let checks_in = parseFloat($('#checks_in_total').val().replace(/[,\$]/g, ''));
            let earnest_deposit_amount = parseFloat($('#earnest_deposit_amount').val().replace(/[,\$]/g, ''));
            let income_deductions = parseFloat($('#income_deductions_total').val().replace(/[,\$]/g, ''));
            let admin_fee_from_client = parseFloat($('#admin_fee_from_client').val().replace(/[,\$]/g, ''));
            let checks_out = parseFloat($('#checks_out_total').val().replace(/[,\$]/g, ''));

            let total_income = (checks_in + earnest_deposit_amount) - income_deductions - admin_fee_from_client;

            $('#total_income_display').html(global_format_number_with_decimals(total_income.toString()));
            $('#total_income').val(total_income);

            let agent_commission_percent = parseInt($('#agent_commission_percent').val()) / 100;
            let agent_commission_amount = total_income * agent_commission_percent;
            $('#agent_commission_amount').val(global_format_number_with_decimals(Math.floor(agent_commission_amount).toFixed(2)));

            let admin_fee_from_agent = parseFloat($('#admin_fee_from_agent').val().replace(/[,\$]/g, ''));
            let commission_deductions = parseFloat($('#commission_deductions_total').val().replace(/[,\$]/g, ''));

            let total_commission = agent_commission_amount - admin_fee_from_agent - commission_deductions;

            $('#total_commission_to_agent_display').html(global_format_number_with_decimals(total_commission.toString()));
            $('#total_commission_to_agent').val(total_commission);

            let total_left = total_commission - checks_out;
            $('#total_left_display').html(global_format_number_with_decimals(total_left.toString()));
            $('#total_left').val(total_left);

            $('.total-left').removeClass('bg-green-light text-success bg-orange-light text-danger');

            if(total_left != '0.00') {
                $('.total-left').addClass('bg-orange-light text-danger');
            } else {
                $('.total-left').addClass('bg-green-light text-success');
            }


        }



    }

    window.save_commission = function (show_toastr_commission = 'no') {

        let Contract_ID = $('#Contract_ID').val();
        let form = $('#commission_form');
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }

        let formData = new FormData();

        form.find('.form-value').each(function() {
            let val = $(this).val().replace(/[,\$]/g, '');
            formData.append($(this).attr('id'), val);
        });



        formData.append('Contract_ID', Contract_ID);
        formData.append('Commission_ID', Commission_ID);
        axios.post('/agents/doc_management/transactions/save_commission', formData, axios_options)
        .then(function (response) {
            if(show_toastr_commission == 'yes') {
                toastr['success']('Commission Details Successfully Saved');
            }
            load_details_header();
            /* if(page == 'details') {
                load_tabs('details');
            } */
        })
        .catch(function (error) {

        });

    }

    // Income Deductions

    window.get_income_deductions = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_income_deductions', {
            params: {
                Commission_ID: Commission_ID
            }
        })
        .then(function (response) {

            $('.check-deductions-div').html('');

            let deductions = response.data.deductions;
            let income_deductions_count = deductions.length;
            let income_deductions_total = 0;

            if(income_deductions_count > 0) {

                deductions.forEach(function(deduction) {

                    income_deductions_total += parseFloat(deduction['amount']);

                    let list_item = ' \
                    <div class="list-group-item d-flex justify-content-between align-items-center"> \
                        <div>'+deduction['description']+'</div> \
                        <div class="d-flex justify-content-end align-items-center"> \
                            <div class="pr-5">'+global_format_number_with_decimals(deduction['amount'])+'</div> \
                            <div><a href="javascript: void(0)" class="btn btn-sm btn-danger delete-income-deduction-button" data-deduction-id="'+deduction['id']+'"><i class="fal fa-trash"></i></a></div> \
                        </div> \
                    </div> \
                    ';
                    $('.check-deductions-div').append(list_item);
                });

            }

            $('.delete-income-deduction-button').off('click').on('click', delete_income_deduction);
            income_deductions_total = global_format_number_with_decimals(income_deductions_total.toString());
            $('#deductions_total_value').val(income_deductions_total);
            $('#income_deductions_total').val(income_deductions_total);
            $('#income_deductions_total_display').text(income_deductions_total);
            $('#income_deductions_count').text(income_deductions_count);

            total_commission();

        })
        .catch(function (error) {

        });
    }

    window.delete_income_deduction = function() {
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let button = $(this);
        let deduction_id = button.data('deduction-id');
        let formData = new FormData();
        formData.append('deduction_id', deduction_id);
        axios.post('/agents/doc_management/transactions/delete_income_deduction', formData, axios_options)
        .then(function (response) {
            get_income_deductions(Commission_ID);
            toastr['success']('Deduction Successfully Deleted');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.save_add_income_deduction = function() {

        let form = $('#add_income_deduction_div');
        let validate = validate_form(form);

        if(validate == 'yes') {

            let Commission_ID = $('#Commission_ID').val();
            if($('#Commission_Other_ID').length > 0) {
                Commission_ID = $('#Commission_Other_ID').val();
            }
            let description = $('#income_deduction_description').val();
            let amount = $('#income_deduction_amount').val();

            let formData = new FormData();
            formData.append('Commission_ID', Commission_ID);
            formData.append('description', description);
            formData.append('amount', amount);

            axios.post('/agents/doc_management/transactions/save_add_income_deduction', formData, axios_options)
            .then(function (response) {
                $('#add_income_deduction_div').collapse('hide');

                toastr['success']('Deduction Successfully Added');
                get_income_deductions(Commission_ID);
                setTimeout(function() {
                    save_commission('no');
                }, 500);
            })
            .catch(function (error) {

            });

        }

    }

    // Commission Deductions

    window.get_commission_deductions = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_commission_deductions', {
            params: {
                Commission_ID: Commission_ID
            }
        })
        .then(function (response) {

            $('.commission-deductions-div').html('');

            let deductions = response.data.deductions;
            let commission_deductions_count = deductions.length;
            let commission_deductions_total = 0;

            if(commission_deductions_count > 0) {

                deductions.forEach(function(deduction) {

                    commission_deductions_total += parseFloat(deduction['amount']);

                    let list_item = ' \
                    <div class="list-group-item d-flex justify-content-between align-items-center"> \
                        <div>'+deduction['description']+'</div> \
                        <div class="d-flex justify-content-end align-items-center"> \
                            <div class="pr-5">'+global_format_number_with_decimals(deduction['amount'])+'</div> \
                            <div><a href="javascript: void(0)" class="btn btn-sm btn-danger delete-commission-deduction-button" data-deduction-id="'+deduction['id']+'"><i class="fal fa-trash"></i></a></div> \
                        </div> \
                    </div> \
                    ';
                    $('.commission-deductions-div').append(list_item);
                });

            }

            $('.delete-commission-deduction-button').off('click').on('click', delete_commission_deduction);
            commission_deductions_total = global_format_number_with_decimals(commission_deductions_total.toString());
            $('#deductions_total_value').val(commission_deductions_total);
            $('#commission_deductions_total').val(commission_deductions_total);
            $('#commission_deductions_total_display').text(commission_deductions_total);
            $('#commission_deductions_count').text(commission_deductions_count);

            total_commission();

        })
        .catch(function (error) {

        });
    }

    window.delete_commission_deduction = function() {
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let button = $(this);
        let deduction_id = button.data('deduction-id');
        let formData = new FormData();
        formData.append('deduction_id', deduction_id);
        axios.post('/agents/doc_management/transactions/delete_commission_deduction', formData, axios_options)
        .then(function (response) {
            get_commission_deductions(Commission_ID);
            toastr['success']('Deduction Successfully Deleted');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.save_add_commission_deduction = function() {

        let form = $('#add_commission_deduction_div');
        let validate = validate_form(form);

        if(validate == 'yes') {

            let Commission_ID = $('#Commission_ID').val();
            if($('#Commission_Other_ID').length > 0) {
                Commission_ID = $('#Commission_Other_ID').val();
            }
            let description = $('#commission_deduction_description').val();
            let amount = $('#commission_deduction_amount').val();

            let formData = new FormData();
            formData.append('Commission_ID', Commission_ID);
            formData.append('description', description);
            formData.append('amount', amount);

            axios.post('/agents/doc_management/transactions/save_add_commission_deduction', formData, axios_options)
            .then(function (response) {
                $('#add_commission_deduction_div').collapse('hide');

                toastr['success']('Deduction Successfully Added');
                get_commission_deductions(Commission_ID);
                setTimeout(function() {
                    save_commission('no');
                }, 500);
            })
            .catch(function (error) {

            });

        }

    }

    // Checks

    window.get_checks_in = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_checks_in', {
            params: {
                Commission_ID: Commission_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.checks-in-div').html(response.data);
            let checks_in_total = $('#checks_in_total_amount').val() ?? '0.00';
            $('#checks_in_total_display').text(global_format_number_with_decimals(checks_in_total.toString()));
            $('#checks_in_total').val(checks_in_total.toString());
            $('#checks_in_count').text($('#checks_in_total_count').val());
            $('.delete-check-in-button').off('click').on('click', show_delete_check_in);
            $('.edit-check-in-button').on('click', show_edit_check_in);
            $('.re-queue-check-button').on('click', re_queue_check);
            //$('#save_edit_check_in_button').off('click').on('click', save_edit_check_in);
            $('.undo-delete-check-in-button').off('click').on('click', undo_delete_check_in);
            $('.show-deleted-in-button').off('click').on('click', function() {
                $('.check-image-container.in.inactive').toggleClass('hidden');
            });
            total_commission();
        })
        .catch(function (error) {

        });

    }

    window.show_add_check_in = function() {

        clear_add_check_form();

        $('#add_check_in_modal').modal('show');

        // shared with commission js
        get_check_info();

        $('#check_in_agent_id').val($('#Agent_ID').val());
        $('#check_in_client_name').val($('#other_client_name').val());
        $('#check_in_street').val($('#other_street').val());
        $('#check_in_city').val($('#other_city').val());
        $('#check_in_state').val($('#other_state').val());
        $('#check_in_zip').val($('#other_zip').val());

        if($('#other_street').length > 0) {
            $('#add_check_in_address').text($('#other_street').val()+' '+$('#other_city').val()+' '+$('#other_state').val()+' '+$('#other_zip').val());
        } else {
            $('#add_check_in_address').text($('#address').val());
        }

        get_checks_in_queue();

    }

    function get_checks_in_queue() {
        let Agent_ID = $('#Agent_ID').val();
        axios.get('/agents/doc_management/transactions/get_checks_in_queue', {
            params: {
                Agent_ID: Agent_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.checks-queue-div').html(response.data);
            $('.import-check-button').on('click', import_check_in);
        })
        .catch(function (error) {

        });
    }

    window.show_edit_check_in = function() {

        clear_add_check_form();
        $('#edit_check_in_modal').modal();

        $('.edit-check-in-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+$(this).data('image-location')+'" class="w-100"></div>');
        $('#edit_check_in_id').val($(this).data('check-id'));
        $('#edit_check_in_date').val($(this).data('check-date'));
        $('#edit_check_in_number').val($(this).data('check-number'));
        $('#edit_check_in_amount').val($(this).data('check-amount'));
        $('#edit_check_in_date_received').val($(this).data('date-received'));
        $('#edit_check_in_date_deposited').val($(this).data('date-deposited'));
        //$('input')./* trigger('change') */;

        $('#save_edit_check_in_button').off('click').on('click', save_edit_check_in);

    }

    window.save_edit_check_in = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let form = $('#edit_check_in_form');
        let formData = new FormData(form[0]);

        axios.post('/agents/doc_management/transactions/save_edit_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Edited');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.show_delete_check_in = function() {
        let check_id = $(this).data('check-id');
        let type = $(this).data('type');
        $('#confirm_modal').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div><div class="text-center">Are you sure you want to delete this check?</div></div>');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Check');
        $('#confirm_button').off('click').on('click', function() {
            save_delete_check_in(check_id, type);
        });
    }

    window.save_delete_check_in = function(check_id, type) {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let formData = new FormData();
        formData.append('check_id', check_id);
        formData.append('type', type);
        axios.post('/agents/doc_management/transactions/save_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Deleted');
            setTimeout(function() {
                save_commission('no');
            }, 500);

        })
        .catch(function (error) {

        });
    }

    window.undo_delete_check_in = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let check_id = $(this).data('check-id');
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/undo_delete_check_in', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Reactivated');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });

    }

    function re_queue_check() {

        let check_id = $(this).data('check-id');
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }

        let formData = new FormData();
        formData.append('check_id', check_id);

        axios.post('/agents/doc_management/transactions/re_queue_check', formData, axios_options)
        .then(function (response) {
            get_checks_in(Commission_ID);
            toastr['success']('Check Successfully Re Queued');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.get_checks_out = function(Commission_ID) {

        axios.get('/agents/doc_management/transactions/get_checks_out', {
            params: {
                Commission_ID: Commission_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.checks-out-div').html(response.data);
            $('#checks_out_total_display').text(global_format_number_with_decimals($('#checks_out_total_amount').val().toString()));
            $('#checks_out_total').val($('#checks_out_total_amount').val().toString());
            $('#checks_out_count').text($('#checks_out_total_count').val());
            $('.delete-check-out-button').off('click').on('click', show_delete_check_out);
            $('.edit-check-out-button').off('click').on('click', show_edit_check_out);
            //$('#save_edit_check_out_button').off('click').on('click', save_edit_check_out);
            $('.undo-delete-check-out-button').off('click').on('click', undo_delete_check_out)
            $('.show-deleted-out-button').off('click').on('click', function() {
                $('.check-image-container.out.inactive').toggleClass('hidden');
            });
            total_commission();
        })
        .catch(function (error) {

        });

    }

    window.show_add_check_out = function() {

        clear_add_check_form();

        $('#add_check_out_modal').modal('show');

        $('#check_out_upload').off('change').on('change', function () {

            if($(this).val() != '') {

                //$(this).closest('.form-ele').find('label').addClass('active');

                $('#check_out_date, #check_out_amount, #check_out_number').val('');

                global_loading_on('', '<div class="h5 text-white">Scanning Check</div>');
                let form = $('#add_check_out_form');
                let formData = new FormData(form[0]);
                axios.post('/agents/doc_management/transactions/get_check_details', formData, axios_options)
                .then(function (response) {
                    if(response.data.check_date) {
                        $('#check_out_date').val(response.data.check_date);
                        $('#check_out_amount').val(response.data.check_amount);
                        $('#check_out_number').val(response.data.check_number);
                        if(response.data.check_pay_to_agent_id) {
                            $('#check_out_agent_id').val(response.data.check_pay_to_agent_id);
                            //select_refresh($('#add_check_out_form'));
                        }
                    }
                    $('.check-out-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+response.data.check_location+'" class="w-100"></div>');
                    global_loading_off();

                })
                .catch(function (error) {

                });
            }

        });

        $('#check_out_agent_id').on('change', function() {
            if($(this).val() != '') {
                $('#check_out_recipient').val($(this).find('option:selected').data('recipient'));
            } else {
                $('#check_out_recipient').val('');
            }
        });

        $('#check_out_agent_id').val($('#Agent_ID').val());

        $('.mail-to-div').hide();
        show_mail_to_address();
        $('#check_out_delivery_method').on('change', function() {
            show_mail_to_address();
        });

    }

    function show_mail_to_address() {
        if($('#check_out_delivery_method').val() == 'mail' || $('#check_out_delivery_method').val() == 'fedex') {
            $('.mail-to-div').fadeIn();
            $('.mail-to-div').find('input, select').addClass('required');
        } else {
            $('.mail-to-div').fadeOut();
            $('.mail-to-div').find('input, select').removeClass('required');
        }
    }

    window.save_add_check_out = function() {

        let form = $('#add_check_out_form');
        let validate = validate_form(form);

        if(validate == 'yes') {

            $('#save_add_check_out_button').prop('disabled', true).html('<i class="fas fa-spinner fa-pulse mr-2"></i> Adding Check...');
            let formData = new FormData(form[0]);
            let Commission_ID = $('#Commission_ID').val();
            if($('#Commission_Other_ID').length > 0) {
                Commission_ID = $('#Commission_Other_ID').val();
            }
            formData.append('Commission_ID', Commission_ID);

            axios.post('/agents/doc_management/transactions/save_add_check_out', formData, axios_options)
            .then(function (response) {
                $('#add_check_out_modal').modal('hide');
                toastr['success']('Check Successfully Added');
                get_checks_out(Commission_ID);
                $('#save_add_check_out_button').prop('disabled', false).html('<i class="fal fa-check mr-2"></i> Save');
                setTimeout(function() {
                    save_commission('no');
                }, 500);
            })
            .catch(function (error) {

            });

        }

    }

    window.show_edit_check_out = function() {

        clear_add_check_form();
        $('#edit_check_out_modal').modal();
        let button = $(this);
        setTimeout(function() {
            $('.edit-check-out-preview-div').html('<div class="border border-primary mt-2 check-preview"><img src="'+button.data('image-location')+'" class="w-100"></div>');

            $('#edit_check_out_id').val(button.data('check-id'));
            $('#edit_check_out_date').val(button.data('check-date'));
            $('#edit_check_out_number').val(button.data('check-number'));
            $('#edit_check_out_amount').val(button.data('check-amount'));

            if(button.data('recipient-agent-id') > 0) {
                $('#edit_check_out_agent_id').val(button.data('recipient-agent-id'));
            }

            $('#edit_check_out_recipient').val(button.data('recipient'));
            $('#edit_check_out_delivery_method').val(button.data('delivery-method'));
            $('#edit_check_out_date_ready').val(button.data('date-ready'));
            $('#edit_check_out_mail_to_street').val(button.data('mail-to-street'));
            $('#edit_check_out_mail_to_city').val(button.data('mail-to-city'));
            $('#edit_check_out_mail_to_state').val(button.data('mail-to-state'));
            $('#edit_check_out_mail_to_zip').val(button.data('mail-to-zip'));
            $('.edit-mail-to-div').hide();
            if(button.data('delivery-method') == 'mail' || button.data('delivery-method') == 'fedex') {
                $('.edit-mail-to-div').show();
            }

            $('#edit_check_out_modal').find('.custom-form-element').each(function() {
                if(button.val() != '') {
                    button;
                }
            });


            $('#save_edit_check_out_button').off('click').on('click', save_edit_check_out);

            $('.edit-mail-to-div').hide();
            show_edit_mail_to_address();
            $('#edit_check_out_delivery_method').on('change', function() {
                show_edit_mail_to_address();
            });

            $('#edit_check_out_agent_id').on('change', function() {
                if(button.val() > 0) {
                    $('#edit_check_out_recipient').val(button.find('option:selected').data('recipient'));
                } else {
                    $('#edit_check_out_recipient').val('');
                }
            });

            //select_refresh($('#edit_check_out_modal'));
        }, 100);

    }

    function show_edit_mail_to_address() {
        if($('#edit_check_out_delivery_method').val() == 'mail' || $('#edit_check_out_delivery_method').val() == 'fedex') {
            $('.edit-mail-to-div').fadeIn();
            $('.edit-mail-to-div').find('input, select').addClass('required');
        } else {
            $('.edit-mail-to-div').fadeOut();
            $('.edit-mail-to-div').find('input, select').removeClass('required');
        }
    }

    window.save_edit_check_out = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let form = $('#edit_check_out_form');
        let formData = new FormData(form[0]);

        axios.post('/agents/doc_management/transactions/save_edit_check_out', formData, axios_options)
        .then(function (response) {
            get_checks_out(Commission_ID);
            toastr['success']('Check Successfully Edited');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });
    }

    window.show_delete_check_out = function() {
        let check_id = $(this).data('check-id');
        $('#confirm_modal').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div><div class="text-center">Are you sure you want to delete this check?</div></div>');
        $('#confirm_modal').modal().find('.modal-title').html('Delete Check');
        $('#confirm_button').off('click').on('click', function() {
            save_delete_check_out(check_id);
        });
    }

    window.save_delete_check_out = function(check_id) {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/save_delete_check_out', formData, axios_options)
        .then(function (response) {
            get_checks_out(Commission_ID);
            toastr['success']('Check Successfully Deleted')
            setTimeout(function() {
                save_commission('no');
            }, 500);

        })
        .catch(function (error) {

        });
    }

    window.undo_delete_check_out = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let check_id = $(this).data('check-id');
        let formData = new FormData();
        formData.append('check_id', check_id);
        axios.post('/agents/doc_management/transactions/undo_delete_check_out', formData, axios_options)
        .then(function (response) {
            get_checks_out(Commission_ID);
            toastr['success']('Check Successfully Reactivated');
            setTimeout(function() {
                save_commission('no');
            }, 500);
        })
        .catch(function (error) {

        });

    }

    function clear_add_check_form() {
        $('#add_check_in_form, #edit_check_in_form, #add_check_out_form, #edit_check_out_form').find('input, select').val('');
        $('.check-in-preview-div, .edit-check-in-preview-div, .check-out-preview-div, .edit-check-out-preview-div').html('');
    }


    // Notes
    window.get_commission_notes = function() {
        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        axios.get('/agents/doc_management/transactions/get_commission_notes', {
            params: {
                Commission_ID: Commission_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('.notes-list-group').html(response.data);
        })
        .catch(function (error) {

        });
    }

    window.add_commission_notes = function() {

        let Commission_ID = $('#Commission_ID').val();
        if($('#Commission_Other_ID').length > 0) {
            Commission_ID = $('#Commission_Other_ID').val();
        }
        let notes = $('.commission-notes-input').val();

        let formData = new FormData();
        formData.append('Commission_ID', Commission_ID);
        formData.append('notes', notes);

        axios.post('/agents/doc_management/transactions/add_commission_notes', formData, axios_options)
        .then(function (response) {
            get_commission_notes();
            toastr['success']('Note Successfully Added');
            $('.commission-notes-input').val('');
        })
        .catch(function (error) {

        });
    }



    window.popout_row = function() {

        if($(this).hasClass('toggle-agent-info')) {
            $('.agent-info-toggle').hide();
        } else {
            $('.agent-info-toggle').show();
        }

        let popout_row = $(this).closest('.popout-row');
        let popout_action = popout_row.find('.popout-action');
        let popout = popout_row.find('.popout');

        let anime_in = 'lightSpeedInRight';
        let anime_out = 'lightSpeedOutRight';

        if(!popout_action.hasClass('bg-blue-light')) {

            $('.popout-action, .popout').removeClass('active bg-blue-light '+ anime_in+' '+anime_out);
            popout_action.addClass('bg-blue-light');

            $('.popout').not(popout).addClass(anime_out).hide();

            popout.addClass('active bg-blue-light '+ anime_in).fadeIn();
            if($(window).width() > 992) {
                $('.popout.middle').css({ top: '-'+ ((popout.height() / 2) - 30) + 'px' });
            }

        } else {

            $('.popout-action, .popout').removeClass('active bg-blue-light '+ anime_in+' '+anime_out);
            popout.addClass(anime_out).hide();

        }

        if(!$(this).is('.show-view-add-button:last')) {
            if($('.popout.active').length > 0) {
                $('.commission-details-tabs').animate({ opacity: '0.3' });
            } else {
                $('.commission-details-tabs').animate({ opacity: '1' });
            }
        } else {
            $('.commission-details-tabs').animate({ opacity: '1' });
        }

    }

}
