

if(document.URL.match(/missing_transactions/)) {

    $(function() {

        global_loading_on('#missing_listings', '');
        global_loading_on('#missing_contracts', '');

        get_missing_listings();
        get_missing_contracts();
        get_missing_contracts_our_listing();

        let options = {
            menubar: false,
            statusbar: false,
            height: 370,
            selector: '#email_agent_missing_transaction_message',
            toolbar: 'undo redo | styleselect | bold italic | forecolor backcolor | align outdent indent |'
        }
        text_editor(options);



        function init() {

            $('.check-all').on('change', function() {
                if($(this).is(':checked')) {
                    $('.transaction-checkbox').prop('checked', true);
                } else {
                    $('.transaction-checkbox').prop('checked', false);
                }
            });

            $('.transaction-checkbox, .check-all').on('change', function() {
                if($('.transaction-checkbox:checked').length > 0) {
                    email_button('show');
                    $('.email-agent.single').prop('disabled', true);
                    $('html, body').scrollTop(0);
                } else {
                    email_button('hide');
                    $('.email-agent.single').prop('disabled', false);
                }
            });

            $('.email-agent').off('click').on('click', function() {

                show_email($(this));

            });

            $('.save-transaction-notes-button').off('click').on('click', function() {
                let ListingKey = $(this).data('listing-key');
                let notes = $('.transaction-notes-'+ListingKey).val();
                save_add_transaction_notes(ListingKey, notes);
            });

            $('.delete-transaction-note-button').off('click').on('click', function() {
                delete_note($(this));
            });

            $('#email_agent_missing_transaction_message').data('orig_html', $('#email_agent_missing_transaction_message').html());


        }


        function get_missing_listings() {

            axios.get('/doc_management/compliance/get_missing_listings')
            .then(function (response) {
                $('#missing_listings').html(response.data);
                data_table('25', $('#listings_table'), [1, 'desc'], [0], [], true, true, true, true, true, false);
                init();
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function get_missing_contracts() {

            axios.get('/doc_management/compliance/get_missing_contracts')
            .then(function (response) {
                $('#missing_contracts').html(response.data);
                data_table('25', $('#contracts_table'), [1, 'desc'], [0], [], true, true, true, true, true, false);
                init();
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function get_missing_contracts_our_listing() {

            axios.get('/doc_management/compliance/get_missing_contracts_our_listing')
            .then(function (response) {
                $('#missing_contracts_our_listing').html(response.data);
                data_table('25', $('#contracts_our_listing_table'), [1, 'desc'], [0], [], true, true, true, true, true, false);
                init();
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function save_add_transaction_notes(ListingKey, notes) {

            let formData = new FormData();
            formData.append('ListingKey', ListingKey);
            formData.append('notes', notes);
            axios.post('/doc_management/compliance/save_add_transaction_notes', formData, axios_options)
            .then(function (response) {
                get_transaction_notes(ListingKey);
                $('.transaction-notes-'+ListingKey).val('');
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function get_transaction_notes(ListingKey) {

            axios.get('/doc_management/compliance/get_transaction_notes', {
                params: {
                    ListingKey: ListingKey
                }
            })
            .then(function (response) {
                $('#transaction_notes_div_'+ListingKey).html(response.data);
                $('.delete-transaction-note-button').off('click').on('click', function() {
                    delete_note($(this));
                });
            })
            .catch(function (error) {
                console.log(error);
            });
        }


        function delete_note(ele) {

            let ListingKey = ele.data('listing-key');
            let note_id = ele.data('note-id');

            let formData = new FormData();
            formData.append('note_id', note_id);
            axios.post('/doc_management/compliance/delete_transaction_note', formData, axios_options)
            .then(function (response) {
                get_transaction_notes(ListingKey);
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function email_button(action) {

            if(action == 'show') {
                $('.email-agents-div').removeClass('hide-me').addClass('show-me');
            } else {
                $('.email-agents-div').removeClass('show-me').addClass('hide-me');
            }

        }

        function show_email(ele) {

            tinyMCE.activeEditor.setContent($('#email_agent_missing_transaction_message').data('orig_html'));

            let type = $('.missing-nav-link.active').data('type');

            if(ele.hasClass('single')) {
                ele.closest('tr').find('td input[type=checkbox]').prop('checked', true);
            }
            $('#email_agents_missing_transactions_modal').modal('show');

            let ids = [];
            $('.transaction-checkbox:checked').each(function() {
                ids.push($(this).data('listing-key'));
            });
            $('#listing_keys').val(ids.join());

            $('#email_agents_missing_transactions_modal').on('hide.bs.modal', function() {
                $('.transaction-checkbox, .check-all').prop('checked', false);
                email_button('hide');
                $('button.email-agent').prop('disabled', false);
            });


            let message_details = '';
            if(type == 'listings') {
                message_details = 'Please submit your Listing Agreement on our document management system at <a href="https://agentdocuments.com" target="_blank">https://agentdocuments.com</a>';
            } else {
                message_details = 'BrightMLS has reported you have an active contract on the property above. Please submit your Sales Contract on our document management system at <a href="https://agentdocuments.com" target="_blank">https://agentdocuments.com</a>';
            }

            let email_message_orig = tinyMCE.activeEditor.getContent();
            let email_message = email_message_orig.replace('%%Message%%', message_details);
            email_message = '<div style="width: 100%">'+email_message+'</div>';
            tinyMCE.activeEditor.setContent(email_message);

            $('#send_email_agents_missing_transactions_button').off('click').on('click', function() {

                let form = $('#email_agents_missing_transactions_form');
                let validate = validate_form(form);

                if(validate == 'yes') {

                    $('#email_agents_missing_transactions_modal').modal('hide');
                    toastr['success']('Emails Successfully Sent!');

                    let subject = $('#email_agent_earnest_subject').val();
                    let message = tinyMCE.activeEditor.getContent();
                    let listing_keys = $('#listing_keys').val();

                    let formData = new FormData();
                    formData.append('type', type);
                    formData.append('subject', subject);
                    formData.append('message', message);
                    formData.append('listing_keys', listing_keys);
                    axios.post('/doc_management/compliance/email_agents_missing_transactions', formData, axios_options)
                    .then(function (response) {

                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }

            });

        }


    });

}
