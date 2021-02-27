if (document.URL.match(/transaction_details/)) {

    $(function() {

        dt_contacts = setInterval(function() {
            if($('#contacts_table').length == 1) {
                let contacts_table = data_table($('#contacts_table'), [1, 'desc'], [0], [], false, true, true, true, true);
                clearInterval(dt_contacts);
            }
        }, 500)

        $(document).on('click', '.btn, input', function(e) {

            let ele = $(this);
            let id = ele.attr('id');
            let classes = ele[0].classList;

            if(classes.contains('save-member-button')) {
                save_add_member();
            } else if(classes.contains('bank-trust')) {
                show_bank_trust(ele);
            } else if(classes.contains('delete-member-button')) {
                confirm_delete_member(ele);
            } else if(classes.contains('bank-trust')) {
                show_bank_trust();
            } else if(classes.contains('import-contact-button')) {
                show_import_modal($(this).data('ele'));
            } else if(id == 'add_member_button') {
                show_add_member();
            }
        });


        $(document).on('change', '.member-type-id', show_hide_fields);

    });

    window.members_init = function() {

        $('a[data-toggle="list"]').on('shown.bs.tab', function (e) {
            show_hide_fields();
        });

    }


    window.show_hide_fields = function() {

        let member_div = $('.member-div.active');
        member_div.not('.disabled').find('input, select').attr('disabled', false);
        let type = member_div.find('.member-type-id option:selected').text();

        let bank_trust_div = member_div.find('.bank-trust-div');
        let bank_trust_label = bank_trust_div.find('label');
        let member_entity_name_div = member_div.find('.member-entity-name-div');
        let company_div = member_div.find('.company-div');
        let home_address_div = member_div.find('.home-address-div');
        let office_address_div = member_div.find('.office-address-div');
        let bright_mls_id_div = member_div.find('.bright-mls-id-div');

        if(type == 'Buyer' || type == 'Seller' || type == 'Owner' || type == 'Renter') {

            bank_trust_div.show().find('.bank-trust').data('member', type);
            bank_trust_label.text(type +' is a Trust, Company or other Entity');
            if(member_entity_name_div.find('input').val() != '') {
                member_entity_name_div.show();
            }
            company_div.hide();
            home_address_div.show();
            office_address_div.hide();
            bright_mls_id_div.hide();

        } else if(type == 'Listing Agent' || type == 'Buyer Agent' || type == 'Renter Agent') {

            bank_trust_div.hide();
            member_entity_name_div.hide();
            company_div.show();
            home_address_div.hide();
            office_address_div.show();
            bright_mls_id_div.show();

        } else {

            bank_trust_div.hide();
            member_entity_name_div.hide();
            company_div.show();
            home_address_div.show();
            office_address_div.show();
            bright_mls_id_div.hide();

        }

        $('.import-contact-button').prop('disabled', false);


    }

    window.show_bank_trust = function(ele) {

        let input = ele.closest('.col-12').next('.member-entity-name-div');
        if(ele.is(':checked')) {
            input.fadeIn();
        } else {
            input.fadeOut();
        }
    }

    window.save_add_member = function() {

        let form = $('.member-div.active');
        let validate = validate_form(form);

        if(validate == 'yes') {

            let formData = new FormData();

            formData.append('id', form.find('.member-id').val());
            formData.append('transaction_type', $('#transaction_type').val());
            formData.append('Listing_ID', $('#Listing_ID').val());
            formData.append('Contract_ID', $('#Contract_ID').val());
            formData.append('Agent_ID', $('#Agent_ID').val());
            formData.append('member_type_id', form.find('.member-type-id').val());
            formData.append('entity_name', form.find('.member-entity-name').val());
            formData.append('first_name', form.find('.member-first-name').val());
            formData.append('last_name', form.find('.member-last-name').val());
            formData.append('company', form.find('.member-company').val());
            formData.append('cell_phone', form.find('.member-phone').val());
            formData.append('email', form.find('.member-email').val());
            formData.append('address_home_street', form.find('.member-home-street').val());
            formData.append('address_home_city', form.find('.member-home-city').val());
            formData.append('address_home_state', form.find('.member-home-state').val());
            formData.append('address_home_zip', form.find('.member-home-zip').val());
            formData.append('address_office_street', form.find('.member-office-street').val());
            formData.append('address_office_city', form.find('.member-office-city').val());
            formData.append('address_office_state', form.find('.member-office-state').val());
            formData.append('address_office_zip', form.find('.member-office-zip').val());
            formData.append('CRMContact_ID', form.find('.member-crm-contact-id').val());
            formData.append('bright_mls_id', form.find('.member-bright-mls-id').val());

            axios.post('/agents/doc_management/transactions/save_member', formData, axios_options)
            .then(function (response) {
                reload_member_tab();
                toastr['success']('Member Successfully Added');
                setTimeout(function() {
                    //scrollToAnchor('scroll_to');
                    //load_tabs('documents');
                    //load_tabs('checklist');
                    load_details_header();
                }, 500);
            })
            .catch(function (error) {

            });
        }

    }

    window.show_add_member = function() {

        let transaction_type = $('#transaction_type').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();

        axios.get('/agents/doc_management/transactions/add_member_html', {
            params: {
                transaction_type: transaction_type,
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            // show add button in list group
            $('#add_member_group').show().trigger('click');
            // fill add member div with form
            $('#add_member_div').html(response.data);
            global_tooltip();
            //load_details_header();

            $('.cancel-add-member-button').off('click').on('click', function() {
                $('#add_member_group').hide();
                $('.list-group-item-member').eq(0).trigger('click');
            });
            $('.list-group-item-member').on('click', function() {
                $('#add_member_group').hide();
            });


            let add_list_agent = true;
            let add_buyer_agent = true;
            if($('.list-group-item-member[data-member-type="Listing Agent"]').length > 0) {
                add_list_agent = false;
            }
            if($('.list-group-item-member[data-member-type="Buyer Agent"]').length > 0 || $('.list-group-item-member[data-member-type="Renter Agent"]').length > 0) {
                add_buyer_agent = false;
            }
            $('#members_tab_div').find('.member-type-id').find('option').each(function() {
                if($(this).text() == 'Listing Agent') {
                    if(add_list_agent) {
                        $(this).remove();
                    }
                } else if($(this).text() == 'Buyer Agent') {
                    if(add_buyer_agent) {
                        $(this).remove();
                    }
                }
            });

            /* setTimeout(function() {
                form_elements();
            }, 100); */

        })
        .catch(function (error) {

        });
    }

    window.reload_member_tab = function() {
        let active_tab = $('.list-group-item-member.active').eq();
        load_tabs('members');
        $('.list-group-item-member.active').eq(active_tab).trigger('click');
    }

    window.confirm_delete_member = function(ele) {
        let id = ele.data('member-id');
        $('#confirm_delete_member_modal').modal();
        $('#delete_member_button').off('click').on('click', function() {
            delete_member(id);
            $('#confirm_delete_member_modal').modal('hide');
        });
    }

    window.delete_member = function(id) {
        let transaction_type = $('#transaction_type').val();
        let Listing_ID = $('#listing_id').val();
        let Contract_ID = $('#Contract_ID').val();

        let formData = new FormData();
        formData.append('id', id);
        formData.append('Listing_ID', Listing_ID);
        formData.append('Contract_ID', Contract_ID);
        formData.append('transaction_type', transaction_type);
        axios.post('/agents/doc_management/transactions/delete_member', formData, axios_options)
        .then(function (response) {
            reload_member_tab();
            load_details_header();
            toastr['success']('Member Successfully Deleted');

        })
        .catch(function (error) {

        });
    }

    window.show_import_modal = function(member_div) {
        member_div = $(member_div);
        $('#import_contact_modal').modal();

        $('#contacts_table').off('click').on('click', '.add-contact-button', function() {
            /* if($(this).data('contact-type-id')) {
                member_div.find('.member-type-id').val($(this).data('contact-type-id'));
            } */
            member_div.find('.member-first-name').val($(this).data('contact-first'));
            member_div.find('.member-last-name').val($(this).data('contact-last'));
            member_div.find('.member-company').val($(this).data('contact-company'));
            member_div.find('.member-phone').val($(this).data('contact-phone'));
            member_div.find('.member-email').val($(this).data('contact-email'));
            member_div.find('.member-home-street').val($(this).data('contact-street'));
            member_div.find('.member-home-city').val($(this).data('contact-city'));
            member_div.find('.member-home-state').val($(this).data('contact-state'));
            member_div.find('.member-home-zip').val($(this).data('contact-zip'));
            member_div.find('.member-crm-contact-id').val($(this).data('contact-id'));

            //$('input');
            //setTimeout(select_refresh, 500);
            $('#import_contact_modal').modal('hide');

            setTimeout(function() {
                show_hide_fields();
            }, 500);
        });
    }

}
