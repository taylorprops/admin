
if (document.URL.match(/transaction_details/)) {

    $(function() {

        load_tabs('');

        $('#open_members_tab').one('click', function () {
            load_tabs('members');
        });
        $('#open_checklist_tab').one('click', function () {
            load_tabs('checklist');
        });
        $('#open_documents_tab').one('click', function () {
            load_tabs('documents');
        });
        $('#open_contracts_tab').one('click', function () {
            load_tabs('contracts');
        });
        $('#open_commission_tab').one('click', function () {
            load_tabs('commission');
        });

        $('#open_earnest_tab').one('click', function () {
            load_tabs('earnest');
        });

        $(document).on('click', '.process-cancellation-button', function() {
            let Contract_ID = $(this).data('contract-id');
            window.location = '/doc_management/document_review/' + Contract_ID;
        });

        load_details_header();


        let agent_search_request = null;

        function search_bright_agents() {

            let val = $(this).val();

            if (val.length > 4) {

                $('.search-results').html('');

                if (agent_search_request) {
                    agent_search_request.cancel();
                }
                agent_search_request = axios.CancelToken.source();

                axios.get('/agents/doc_management/transactions/search_bright_agents', {
                    cancelToken: agent_search_request.token,
                    params: {
                        val: val
                    },
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(function (response) {

                    let data = response.data;

                    $.each(data, function (k, agents) {

                        if (agents.length > 0) {

                            $.each(agents, function (k, agent) {

                                let agent_div = ' \
                                    <div class="search-result list-group-item" data-agent-first="'+ agent.MemberFirstName + '" data-agent-last="' + agent.MemberLastName + '" data-agent-phone="' + agent.MemberPreferredPhone + '" data-agent-email="' + agent.MemberEmail + '" data-agent-company="' + agent.OfficeName + '" data-agent-mls-id="' + agent.MemberMlsId + '" data-agent-street="' + agent.OfficeAddress1 + '" data-agent-city="' + agent.OfficeCity + '" data-agent-state="' + agent.OfficeStateOrProvince + '" data-agent-zip="' + agent.OfficePostalCode + '"> \
                                        <div class="row"> \
                                            <div class="col-6 col-md-3"> \
                                                <span class="font-weight-bold">'+ agent.MemberLastName + ', ' + agent.MemberFirstName + '</span><br><span class="small">' + agent.MemberType + ' (' + agent.MemberMlsId + ')<br>' + agent.MemberEmail + ' \
                                            </div> \
                                            <div class="col-6 col-md-3"> \
                                            <span class="font-weight-bold">'+ agent.OfficeName + '</span><br><span class="small">' + agent.OfficeMlsId + '</span>\
                                            </div> \
                                            <div class="col-12 col-md-6"> \
                                                '+ agent.OfficeAddress1 + '<br>' + agent.OfficeCity + ', ' + agent.OfficeStateOrProvince + ' ' + agent.OfficePostalCode + ' \
                                            </div> \
                                        </div> \
                                    </div> \
                                ';

                                $('.search-results').append(agent_div);

                            });

                            $('.search-results-container').show();

                        } else {

                            $('.search-results-container').show();
                            $('.search-results').append('<div class="search-result list-group-item text-danger"><i class="fad fa-exclamation-triangle mr-2"></i> No Matching Results</div>');

                        }

                    });

                    $('.search-result').off('click').on('click', function () {
                        add_buyers_agent($(this));
                    });

                })
                .catch(function (error) {
                    if (axios.isCancel(error)) {

                    } else {
                        //
                    }
                });


            } else {

                $('.search-results-container').hide();
                $('.search-results').html('');

            }

        }

        $('#agent_search').on('keyup', search_bright_agents);

        $(document).on('mouseup', function (e) {
            var container = $('.search-results-container');
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        // open tabs from url parameters
        let tab = global_get_url_parameters('tab');
        if(tab == 'commission') {
            $('#open_commission_tab').trigger('click');
        }


    });

    // TODO: remove this and undo_cancel
    // remove confirm_undo_cancel_modal
    // use undo_cancel_contract function in controller
    function show_undo_cancel_contract() {
        let Contract_ID = $(this).data('contract-id');
        $('#confirm_undo_cancel_modal').modal();
        $('#undo_cancel_button').off('click').on('click', function() {
            undo_cancel_contract(Contract_ID)
        });
    }

    function undo_cancel_listing() {

        let Listing_ID = $('#Listing_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        let formData = new FormData();
        formData.append('Listing_ID', Listing_ID);
        formData.append('Agent_ID', Agent_ID);
        axios.post('/agents/doc_management/transactions/undo_cancel_listing', formData, axios_options)
        .then(function (response) {

            if(response.data.expired == 'expired') {

                $('#modal_danger').modal('show').find('.modal-body').html(' \
                <div class="d-flex justify-content-center align-items-center"> \
                    <div><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div> \
                    <div class="ml-3 text-center">The Listing has passed it expiration date.<br>Please enter the new expiration date.</div> \
                </div> \
                ');

            }

            load_tabs('checklist');
            load_tabs('details');
            load_details_header();
            toastr['success']('Undo Successful');

        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function undo_cancel_contract(Contract_ID) {

        let formData = new FormData();
        let Agent_ID = $('#Agent_ID').val();
        formData.append('Contract_ID', Contract_ID);
        formData.append('Agent_ID', Agent_ID);
        axios.post('/agents/doc_management/transactions/undo_cancel_contract', formData, axios_options)
        .then(function (response) {

            if(response.data.error) {
                if($('#for_sale').val() == 'yes') {
                    $('#modal_danger').modal().find('.modal-body').html('The Listing is currently under contract with another Contract. You must release the current Contract before Reactivating this one.');
                } else {
                    $('#modal_danger').modal().find('.modal-body').html('Another Lease Agreement has already been accepted for this listing. You must cancel the current Lease before Reactivating this one.');
                }
                return false;
            }

            load_tabs('checklist');
            load_details_header();
            toastr['success']('Undo Successful');
            $('#confirm_undo_cancel_modal').modal('hide');

        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function show_cancel_listing() {

        let Listing_ID = $('#Listing_ID').val();

        // check if listing agreement submitted. If not cancel
        axios.get('/agents/doc_management/transactions/check_docs_submitted_and_accepted', {
            params: {
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {

            let cancel_listing = false;

            let cancel_success = '<div class="d-flex justify-content-center align-items-center p-3"><div><i class="fad fa-check-circle fa-2x text-success"></i></div><div class="text-center ml-3">Your Listing has been successfully canceled.</div></div>';

            // if listing agreement submitted
            if(response.data.listing_accepted == true) {

                if(response.data.listing_expired == true) {
                    // show cancel listing - expired | don't need a withdraw because it is already expired.
                    $('#modal_success').modal().find('.modal-body').html(cancel_success);
                    cancel_listing = true;
                } else {
                    // check if withdraw is submitted
                    if(response.data.listing_withdraw_submitted == true) {
                        // withdraw submitted already so cancel it
                        $('#modal_success').modal().find('.modal-body').html(cancel_success);
                        cancel_listing = true;
                    } else {
                        // require the withdraw be submitted
                        $('#modal_danger').modal().find('.modal-body').html(' \
                            <div class="d-flex justify-content-start align-items-center"> \
                                <div class="mr-3"><i class="fad fa-exclamation-circle fa-2x text-danger"></i></div> \
                                <div class="text-center">Your Listing Agreement is still in effect. You must submit a Listing Withdraw on your checklist before you cancel the Listing.</div> \
                            </div>');
                        return false;
                    }
                }
            } else {
                // show cancel listing - not submitted
                $('#modal_success').modal().find('.modal-body').html(cancel_success);
                cancel_listing = true;
            }

            if(cancel_listing == true) {
                let formData = new FormData();
                formData.append('Listing_ID', Listing_ID);
                axios.post('/agents/doc_management/transactions/cancel_listing', formData, axios_options)
                .then(function (response) {
                    load_details_header();
                    load_tabs('details');
                })
                .catch(function (error) {
                    console.log(error);
                });
            }

        });

    }



    function show_cancel_contract() {

        let cancel = $(this);
        let for_sale = cancel.data('for-sale');
        let listing_expiration_date = cancel.data('listing-expiration-date') || null;
        let today = new Date();
        let expire = new Date(listing_expiration_date);


        // check if any docs have been submitted and accepted
        let Contract_ID = $('#Contract_ID').val();

        $('.cancel-contract, .cancel-lease, .expired-listing').removeClass('d-flex').hide();

        axios.get('/agents/doc_management/transactions/check_docs_submitted_and_accepted', {
            params: {
                Contract_ID: Contract_ID
            }
        })
        .then(function (response) {

            let hide_modal = false;

            if(for_sale == 'yes') {

                if(listing_expiration_date) {
                    $('.cancel-contract.has-listing').addClass('d-flex').show();
                } else {
                    $('.cancel-contract.has-listing').removeClass('d-flex').hide();
                }

                if(response.data.contract_submitted == true) {
                    $('.cancel-contract.docs-not-submitted').removeClass('d-flex').hide();
                    if(response.data.release_submitted == false) {
                        $('#modal_danger').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><i class="fa fa-exclamation-circle fa-2x text-danger mr-2"></i> <div class="text-center">You must submit a RELEASE for the Sales Contract on the checklist before you can request a cancellation.</div></div>');
                        return false;
                    } else {
                        // cleared to cancel
                        hide_modal = true;
                        let formData = new FormData();
                        formData.append('Contract_ID', Contract_ID);
                        formData.append('status', 'Cancel Pending');
                        axios.post('/agents/doc_management/transactions/update_contract_status', formData, axios_options)
                        .then(function (response) {
                            load_details_header();
                            toastr['success']('Cancellation Successfully Submitted');
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                    }

                } else {
                    if(response.data.release_submitted == true) {
                        $('#modal_danger').modal().find('.modal-body').html('<div class="d-flex justify-content-start align-items-center"><i class="fa fa-exclamation-circle fa-2x text-danger mr-2"></i> <div class="text-center">You must submit a Sales Contract and it must be reviewed before you can submit a release and request a cancellation.</div></div>');
                        return false;
                    } else {
                        $('.cancel-contract.docs-not-submitted').addClass('d-flex').show();
                    }
                }

            } else {

                if(listing_expiration_date) {
                    $('.cancel-lease.has-listing').addClass('d-flex').show();
                } else {
                    $('.cancel-lease.has-listing').removeClass('d-flex').hide();
                }
                if(response.data.contract_submitted == true) {
                    //$('.cancel-lease.docs-submitted').addClass('d-flex').show();
                    $('.cancel-lease.docs-not-submitted').removeClass('d-flex').hide();
                } else {
                    //$('.cancel-lease.docs-submitted').removeClass('d-flex').hide();
                    $('.cancel-lease.docs-not-submitted').addClass('d-flex').show();
                }

            }

            if(listing_expiration_date) {
                if(today > expire) {
                    $('.expired-listing').addClass('d-flex').show();
                } else {
                    $('.expired-listing').removeClass('d-flex').hide();
                }
            }

            if(response.data.our_listing == false) {
                $('.has-listing').removeClass('d-flex').hide();
            }

            if(hide_modal == false) {
                $('#save_cancel_contract_button').off('click').on('click', function() {
                    cancel_contract(for_sale);
                });
                $('#cancel_contract_modal').modal();
            }

            load_details_header();

        })
        .catch(function (error) {

        });

    }

    function cancel_contract(for_sale) {

        let Contract_ID = $('#Contract_ID').val();

        let type = 'Contract';
        if(for_sale == 'no') {
            type = 'Lease';
        }

        let success = type+' Successfully Canceled';

        let formData = new FormData();
        formData.append('Contract_ID', Contract_ID);
        formData.append('contract_submitted', 'no');
        axios.post('/agents/doc_management/transactions/cancel_contract', formData, axios_options)
        .then(function (response) {
            $('#cancel_contract_modal').modal('hide');
            load_details_header();
            toastr['success'](success);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function show_accept_contract() {
        $('#accept_contract_modal').modal();
        $('#save_accept_contract_button').off('click').on('click', save_accept_contract);
        $('.our-agent-div').hide();
        $('#agent_search_div').on('show.bs.collapse', function () {
            setTimeout(function () {
                $('#agent_search').focus().trigger('click');
            }, 500);
        });

        $('#accept_contract_BuyerRepresentedBy').on('change', function () {

            $('.agent-details').val('');
            $('.buyer-agent-details, .our-agent-div').hide();
            $('.bright-search-row').hide();
            $('#accept_contract_our_agent').removeClass('required');

            if($(this).val() == 'other_agent') {
                $('.buyer-agent-details, .bright-search-row').show();
                $('.agent-details-required').addClass('required');
            } else if($(this).val() == 'agent') {
                $('.buyer-agent-details').show();
                $('.agent-details-required').addClass('required');
                $('.agent-details').each(function() {
                    $(this).val($(this).data('agent-detail'))/* .trigger('change') */;
                });
            } else if($(this).val() == 'our_agent') {
                $('.our-agent-div').show();
                $('.agent-details-required').addClass('required');
                $('#accept_contract_our_agent').addClass('required').on('change', function() {
                    if($(this).val() == '') {
                        $('.agent-details').val('')/* .trigger('change') */;
                        $('.buyer-agent-details').hide();
                    } else {
                        $('.buyer-agent-details').show();
                        $('.agent-details').each(function() {
                            $(this).val($(this).data('agent-detail'))/* .trigger('change') */;
                        });
                        let option = $(this).find('option:selected');
                        $('#accept_contract_buyer_agent_company').val(option.data('company'));
                        $('#accept_contract_buyer_agent_first').val(option.data('first'));
                        $('#accept_contract_buyer_agent_last').val(option.data('last'));
                        $('#accept_contract_buyer_agent_phone').val(option.data('phone'));
                        $('#accept_contract_buyer_agent_email').val(option.data('email'));
                        $('#accept_contract_OtherAgent_ID').val(option.data('id'));
                    }
                });
            } else {
                $('.buyer-agent-details').hide();
                $('.agent-details-required').removeClass('required');
            }
        });

        $('#accept_contract_using_heritage').on('change', function() {
            if($(this).val() == 'yes') {
                $('.not-using-heritage').hide();
                $('#accept_contract_title_company').val('')/* .trigger('change') */;
            } else {
                $('.not-using-heritage').show();
            }
        });

    }

    function add_buyers_agent(ele) {

        let agent_first = ele.data('agent-first');
        let agent_last = ele.data('agent-last');
        let agent_email = ele.data('agent-email');
        let agent_phone = ele.data('agent-phone');
        let agent_mls_id = ele.data('agent-mls-id');
        let agent_company = ele.data('agent-company');
        let office_street = ele.data('agent-street');
        let office_city = ele.data('agent-city');
        let office_state = ele.data('agent-state');
        let office_zip = ele.data('agent-zip');

        $('#accept_contract_buyer_agent_first').val(agent_first)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_last').val(agent_last)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_email').val(agent_email)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_phone').val(agent_phone)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_mls_id').val(agent_mls_id)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_company').val(agent_company)/* .trigger('change') */;
        $('#accept_contract_buyer_agent_street').val(office_street);
        $('#accept_contract_buyer_agent_city').val(office_city);
        $('#accept_contract_buyer_agent_state').val(office_state);
        $('#accept_contract_buyer_agent_zip').val(office_zip);

        $('.search-results').fadeOut('slow');
        $('#agent_search_div').collapse('hide');
    }

    function save_accept_contract() {

        $('#accept_contract_contract_price, #accept_contract_earnest_amount').each(function() {
            if ($(this).val() == '$0') {
                $(this).val('');
            }
        });

        let form = $('#accept_contract_form');
        let validate = validate_form(form);

        if (validate == 'yes') {

            let agent_first = $('#accept_contract_buyer_agent_first').val();
            let agent_last = $('#accept_contract_buyer_agent_last').val();
            let agent_email = $('#accept_contract_buyer_agent_email').val();
            let agent_phone = $('#accept_contract_buyer_agent_phone').val();
            let agent_mls_id = $('#accept_contract_buyer_agent_mls_id').val();
            let agent_company = $('#accept_contract_buyer_agent_company').val();
            let agent_street = $('#accept_contract_buyer_agent_street').val();
            let agent_city = $('#accept_contract_buyer_agent_city').val();
            let agent_state = $('#accept_contract_buyer_agent_state').val();
            let agent_zip = $('#accept_contract_buyer_agent_zip').val();

            let buyer_one_first = $('#accept_contract_buyer_one_first').val();
            let buyer_one_last = $('#accept_contract_buyer_one_last').val();
            let buyer_two_first = $('#accept_contract_buyer_two_first').val();
            let buyer_two_last = $('#accept_contract_buyer_two_last').val();
            let contract_date = $('#accept_contract_contract_date').val() || null;
            let close_date = $('#accept_contract_close_date').val();
            let lease_amount = $('#accept_contract_lease_amount').val() || null;
            let contract_price = $('#accept_contract_contract_price').val() || null;
            let using_heritage = $('#accept_contract_using_heritage').val() || null;
            let title_company = $('#accept_contract_title_company').val() || null;
            let earnest_amount = $('#accept_contract_earnest_amount').val() || null;
            let earnest_held_by = $('#accept_contract_earnest_held_by').val() || null;
            let Listing_ID = $('#Listing_ID').val();
            let BuyerRepresentedBy = $('#accept_contract_BuyerRepresentedBy').val();
            let OtherAgent_ID = $('#accept_contract_OtherAgent_ID').val();

            let formData = new FormData();
            formData.append('agent_first', agent_first);
            formData.append('agent_last', agent_last);
            formData.append('agent_email', agent_email);
            formData.append('agent_phone', agent_phone);
            formData.append('agent_mls_id', agent_mls_id);
            formData.append('agent_company', agent_company);
            formData.append('agent_street', agent_street);
            formData.append('agent_city', agent_city);
            formData.append('agent_state', agent_state);
            formData.append('agent_zip', agent_zip);
            formData.append('buyer_one_first', buyer_one_first);
            formData.append('buyer_one_last', buyer_one_last);
            formData.append('buyer_two_first', buyer_two_first);
            formData.append('buyer_two_last', buyer_two_last);
            formData.append('contract_date', contract_date);
            formData.append('close_date', close_date);
            formData.append('contract_price', contract_price);
            formData.append('lease_amount', lease_amount);
            formData.append('using_heritage', using_heritage);
            formData.append('title_company', title_company);
            formData.append('earnest_amount', earnest_amount);
            formData.append('earnest_held_by', earnest_held_by);
            formData.append('Listing_ID', Listing_ID);
            formData.append('OtherAgent_ID', OtherAgent_ID);
            formData.append('BuyerRepresentedBy', BuyerRepresentedBy);
            axios.post('/agents/doc_management/transactions/accept_contract', formData, axios_options)
                .then(function (response) {
                    $('#accept_contract_modal').modal('hide');
                    load_tabs('contracts');
                    load_details_header();
                    let Contract_ID = response.data.Contract_ID;
                    let type = 'Contract';
                    if($('#for_sale').val() == 'no') {
                        type = 'Lease';
                    }
                    $('#modal_info').modal().find('.modal-body').html('<div class="w-100 text-center">Your '+type+' was successfully added. You will find it in the "'+type+'s" tab<br><br><a class="btn btn-primary" href="/agents/doc_management/transactions/transaction_details/' + Contract_ID + '/contract">View '+type+'</a></div>');
                })
                .catch(function (error) {

                });

        }
    }


    window.load_details_header = function () {
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();
        axios.get('/agents/doc_management/transactions/transaction_details_header', {
            params: {
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID,
                transaction_type: transaction_type
            },
            headers: axios_headers_html
        })
            .then(function (response) {
                $('#details_header').html(response.data);
                $('[data-toggle="popover"]').popover({ placement: 'bottom' });
                $('#accept_contract_button').off('click').on('click', show_accept_contract);
                $('#cancel_contract_button').off('click').on('click', show_cancel_contract);
                $('#cancel_listing_button').off('click').on('click', show_cancel_listing);
                $('.undo-cancel-listing-button').off('click').on('click', undo_cancel_listing);
                $('.undo-cancel-contract-button').off('click').on('click', show_undo_cancel_contract);

            })
            .catch(function (error) {

        });
    }

    window.load_tabs = function (tab, reorder = true) {

        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let Agent_ID = $('#Agent_ID').val();
        let Commission_ID = $('#Commission_ID').val();
        let transaction_type = $('#transaction_type').val();

        if (tab == '') {
            tab = 'details';
        }
        axios.get('/agents/doc_management/transactions/get_' + tab, {
            params: {
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID,
                Agent_ID: Agent_ID,
                Commission_ID: Commission_ID,
                transaction_type: transaction_type
            },
            headers: axios_headers_html
        })
            .then(function (response) {

                $('#' + tab + '_tab').html(response.data);

                if (tab == 'details') {

                    // update counties when state is changed
                    $('#StateOrProvince').on('change', update_county_select);

                    $('#search_mls_button').off('click').on('click', search_mls);

                    $('.save-details-button').off('click').on('click', save_details);

                    if($('#UsingHeritage').val() == 'no') {
                        $('.not-using-heritage').show();
                    }

                    $('#UsingHeritage').on('change', function() {
                        if($(this).val() == 'yes') {
                            $('.not-using-heritage').hide();
                            $('#TitleCompany').val('')/* .trigger('change') */;
                        } else {
                            $('.not-using-heritage').show();
                        }
                    });

                } else if (tab == 'members') {

                    $(document).on('click', '.import-contact-button', function () {
                        show_import_modal($(this).data('ele'));
                    });

                    let contacts_table = data_table($('#contacts_table'), [1, 'desc'], [0], false, true, true, true);




                    $('#add_member_button').off('click').on('click', show_add_member);
                    //$('.save-member-div').off('click').on('click', '.save-member-button', save_add_member);
                    $('.delete-member-button').off('click').on('click', confirm_delete_member);

                    /* setTimeout(function () {
                        show_hide_fields();
                        $('.member-type-id').on('change', show_hide_fields);
                        $('.bank-trust').on('click', show_bank_trust);
                    }, 200); */

                    $('a[data-toggle="list"]').on('shown.bs.tab', function (e) {
                        show_hide_fields();
                    });

                } else if (tab == 'documents') {

                    let document_ids = [];

                    setTimeout(function () {

                        $('.document-div').each(function() {
                            document_ids.push($(this).data('document-id'));
                        });
                        in_process(document_ids);

                        $('.check-all').next('label').css({ transform: 'scale(1.2)' });
                        select_form_group();
                        $('#add_documents_div').on('show.bs.collapse', function () {
                            $('#bulk_options_div').collapse('hide');
                            $('.check-document, .check-all').prop('checked', false).closest('.document-div').removeClass('bg-blue-light');
                        });
                        sortable_documents();

                        $('.dropdown-submenu .dropdown-item').on('click', function (e) {
                            e.stopPropagation();
                            e.preventDefault();
                            $(this).next('.dropdown-menu').toggle();
                        });

                        $('.form-in-use').each(function () {
                            $(this).find('.individual-template-form').prop('disabled', true);
                        });


                        $('.add-to-checklist-button').off('click').on('click', show_add_to_checklist);

                        $('.documents-container').find('modal').appendTo('body');

                        if(reorder) {
                            reorder_documents('yes');
                        }

                        get_emailed_documents();


                    }, 100);

                    setInterval(function() {
                        in_process(document_ids);
                    }, 3000);

                } else if (tab == 'checklist') {

                    setTimeout(function() {
                        $('.save-notes-button').off().on('click', save_add_notes);

                        $('.add-document-button').off('click').on('click', show_add_document);

                        $('.view-docs-button').off('click').on('click', toggle_view_docs_button);

                        $('.view-notes-button').off('click').on('click', toggle_view_notes_button);

                        $('.delete-doc-button').off('click').on('click', show_delete_doc);

                        $('.mark-read-button').off('click').on('click', mark_note_read);

                        $('#change_checklist_button').off('click').on('click', confirm_change_checklist);

                        $('.accept-checklist-item-button').off('click').on('click', function() {
                            checklist_item_review_status($(this), 'accepted', null);
                        });
                        $('.reject-checklist-item-button').off('click').on('click', function() {
                            show_checklist_item_review_status($(this), 'rejected');
                        });

                        $('.undo-accepted, .undo-rejected').off('click').on('click', function() {
                            checklist_item_review_status($(this), 'not_reviewed', null);
                        });

                        $('.mark-required').off('click').on('click', function() {
                            mark_required($(this), $(this).data('checklist-item-id'), $(this).data('required'));
                        });

                        $('.remove-checklist-item').off('click').on('click', function() {
                            show_remove_checklist_item($(this), $(this).data('checklist-item-id'));
                        });

                        $('.add-checklist-item-button').off('click').on('click', show_add_checklist_item);

                        $('.email-agent-button').off('click').on('click', function() {
                            reset_email();

                            show_email_agent();
                            let options = {
                                menubar: false,
                                statusbar: false,
                                toolbar: false
                            }
                            text_editor(options);
                        });

                        $('.notes-div').each(function() {
                            get_notes($(this).data('checklist-item-id'));
                        });

                        /* $('.notes-collapse').on('show.bs.collapse', function () {
                            let textarea_id = $(this).find('.form-textarea').prop('id');
                            setTimeout(function() {
                                let screen_height = window.innerHeight;
                                let to_scroll = screen_height/3;
                                document.getElementById(textarea_id).scrollIntoView(false);
                                window.scrollBy(0,to_scroll);
                                $('#'+textarea_id).focus();
                            }, 500);
                        }); */

                        /* text-editor */


                    }, 1);


                    $('.notes-collapse').on('show.bs.collapse', function () {
                        $('.documents-collapse.show').collapse('hide');
                        //$('.checklist-item-div').removeClass('bg-blue-light');
                        $(this).closest('.checklist-item-div').addClass('bg-blue-light');
                    });
                    $('.documents-collapse').on('show.bs.collapse', function () {
                        $('.notes-collapse.show').collapse('hide');
                        //$('.checklist-item-div').removeClass('bg-blue-light');
                        $(this).closest('.checklist-item-div').addClass('bg-blue-light');
                    });

                    $('.collapse').on('hide.bs.collapse', function () {
                        $(this).closest('.checklist-item-div').removeClass('bg-blue-light');
                    });

                    $('.transaction-option-trigger').off('change').on('change', listing_options);

                    listing_options();



                    // search forms
                    $('.form-search').on('keyup', function() {
                        form_search($(this))
                    });


                } else if (tab == 'contracts') {

                    $('.contract-div').on('mouseenter', function () {
                        $(this).addClass('z-depth-3').removeClass('shadow');
                    });
                    $('.contract-div').on('mouseleave', function () {
                        $(this).removeClass('z-depth-3').addClass('shadow');
                    });

                } else if(tab == 'commission') {

                    $('.popout').eq(0).show();
                    get_checks_in(Commission_ID);
                    get_checks_out(Commission_ID);
                    get_commission_notes(Commission_ID);
                    get_income_deductions(Commission_ID);
                    get_commission_deductions(Commission_ID);
                    get_agent_details(Agent_ID);
                    save_commission('no');

                    show_title();
                    /* $('#using_heritage').on('change', function() {
                        show_title();
                    }); */

                    $('.add-check-in-button').off('click').on('click', show_add_check_in);
                    $('.add-check-out-button').off('click').on('click', show_add_check_out);

                    $('#save_add_check_in_button').off('click').on('click', save_add_check_in);
                    $('#save_add_check_out_button').off('click').on('click', save_add_check_out);

                    $('#save_add_income_deduction_button').off('click').on('click', function() {
                        save_add_income_deduction();
                    });

                    $('#add_income_deduction_div').on('hidden.bs.collapse', function () {
                        $('#income_deduction_description, #income_deduction_amount').val('')/* .trigger('change') */;
                    });

                    $('#save_add_commission_deduction_button').off('click').on('click', function() {
                        save_add_commission_deduction();
                    });

                    $('#add_commission_deduction_div').on('hidden.bs.collapse', function () {
                        $('#commission_deduction_description, #commission_deduction_amount').val('')/* .trigger('change') */;
                    });

                    $('.save-commission-notes-button').off('click').on('click', add_commission_notes);

                    $('.total').each(function() {
                        if($(this).val() == '') {
                            $(this).val('0')/* .trigger('change') */;
                        }
                        /* $(this).on('focus', function () {
                            $(this).select();
                        }); */
                    });



                } else if(tab == 'earnest') {

                    $('#save_earnest_button').off('click').on('click', save_earnest);
                    $('.add-check-button').off('click').on('click', function() {
                        add_earnest_check($(this).data('type'));
                    });
                    $('#save_add_earnest_check_button').off('click').on('click', save_add_earnest_check);
                    get_earnest_check_info();
                    get_earnest_checks('in');
                    get_earnest_checks('out');

                }


                $('.nav-link[data-tab]').on('click', function() {

                    if(window.get_emailed_docs_interval) {
                        clearInterval(get_emailed_docs_interval);
                    }
                    if($(this).data('tab') == 'documents') {
                        window.get_emailed_docs_interval = setInterval(get_emailed_documents, 5000);
                    }

                });

                $('.draggable').draggable({
                    handle: '.draggable-handle'
                });

                setTimeout(show_title_fields, 500);
                $('#required_fields_using_heritage').on('change', function() {
                    show_title_fields();
                });

                global_format_money();

                // init tooltips and form elements
                global_tooltip();

                setTimeout(function() {
                    select_refresh();
                    global_loading_off();
                }, 100);

            })
            .catch(function (error) {

            });
    }

    window.show_title_fields = function() {

        if($('#required_fields_using_heritage').val() == 'yes') {
            $('.not-using-heritage').hide();
            $('#required_fields_title_company').val('');
            $('#required_fields_title_company').prop('required', false).removeClass('required');
        } else {
            $('.not-using-heritage').show();
            $('#required_fields_title_company').prop('required', true).addClass('required');
        }

    }

    window.load_documents_on_tab_click = function() {
        $('#open_documents_tab').off().on('show.bs.tab', function (e) {
            load_tabs('documents', false);
        });
    }

    window.load_checklist_on_tab_click = function() {
        $('#open_checklist_tab').off().on('show.bs.tab', function (e) {
            load_tabs('checklist');
        });
    }


    function sortable_documents() {
        $('.sortable-documents').sortable({
            connectWith: '.sortable-documents',
            placeholder: 'bg-sortable',
            handle: '.document-handle',
            stop: function (event, ui) {
                reorder_documents('no');
            }

        });
        $('.sortable-documents').disableSelection();
    }

    window.reorder_documents = function (on_load) {

        let c = 0;
        let stop = $('.sortable-documents').length - 1;

        $('.sortable-documents').each(function () {
            let els = $(this).find('.document-div');
            let folder_id = $(this).data('folder-id');

            let documents = {
                document: []
            }

            els.each(function () {
                let el, document_id, document_index;
                el = $(this);
                document_id = el.data('document-id');
                document_index = el.index();
                documents.document.push(
                    {
                        'folder_id': folder_id,
                        'document_id': document_id,
                        'document_index': document_index
                    }
                );
            });

            let formData = new FormData();
            documents = JSON.stringify(documents);
            formData.append('data', documents);
            axios.post('/agents/doc_management/transactions/reorder_documents', formData, axios_options)
                .then(function (response) {
                    if (c == stop && on_load == 'no') {
                        toastr['success']('Documents Reordered');
                        docs_count();
                    }
                    c += 1;
                })
                .catch(function (error) {

                });

        });

    }

    function docs_count() {
        $('.folder-div').each(function () {
            let docs_count = $(this).find('.sortable-documents').find('.document-div').length;
            $(this).find('.docs-count').text(docs_count);
        });
    }


    window.listing_options = function () {

        let sale_type = $('[name=property_sub_type]').val();
        if (sale_type == 'Standard' || sale_type == 'Short Sale') {
            $('.hoa').show();
        } else {
            $('.hoa').hide();
            $('[name=hoa_condo]').val('none')/* .trigger('change') */.attr('required', false);
        }

        let listing_type = $('[name=listing_type]').val();
        if (listing_type == 'rental') {
            $('.property-sub-type').hide().find('[name=property_sub_type]').val('Standard');
            $('.year-built, .hoa').hide();
            $('[name=year_built], [name=hoa_condo]').attr('required', false);
        } else {
            if (sale_type == 'Standard' || sale_type == 'Short Sale') {
                $('.property-sub-type, .year-built, .hoa').show();
                $('[name=year_built], [name=hoa_condo]').attr('required', true);
            } else {
                $('.property-sub-type, .year-built').show();
                $('[name=year_built]').attr('required', true);
            }
        }

    }

}
