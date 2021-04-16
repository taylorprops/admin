if(document.URL.match(/active_earnest/)) {

    $(function() {

        get_earnest('all');

        $('.earnest-deposit-account').on('change', function() {
            get_earnest($(this).val());
        });

        $(document).on('change', '.check-all', function() {
            if($(this).is(':checked')) {
                $('.deposit-input').prop('checked', true);
            } else {
                $('.deposit-input').prop('checked', false);
            }
        });

        $(document).on('change', '.deposit-input, .check-all', function() {
            if($('.deposit-input:checked').length > 0) {
                email_button('show');
                $('button.email-agent').prop('disabled', true);
            } else {
                email_button('hide');
                $('button.email-agent').prop('disabled', false);
            }
        });

        $(document).on('click', '.email-agent', function() {

            if($(this).hasClass('single')) {
                $(this).closest('tr').find('td input[type=checkbox]').prop('checked', true);
            }
            $('#email_agents_missing_earnest_modal').modal('show');

            let ids = [];
            $('.deposit-input:checked').each(function() {
                ids.push($(this).data('contract-id'));
            });
            $('#contract_ids').val(ids.join());

            $('#email_agents_missing_earnest_modal').on('hide.bs.modal', function() {
                $('.deposit-input, .check-all').prop('checked', false);
                email_button('hide');
                $('button.email-agent').prop('disabled', false);
            });

            $('#send_email_agents_missing_earnest_button').off('click').on('click', function() {

                let form = $('#email_agents_missing_earnest_form');
                let validate = validate_form(form);

                if(validate == 'yes') {

                    $('#email_agents_missing_earnest_modal').modal('hide');
                    toastr['success']('Emails Successfully Sent!');

                    let subject = $('#email_agent_earnest_subject').val();
                    let message = tinyMCE.activeEditor.getContent();
                    let contract_ids = $('#contract_ids').val();

                    let formData = new FormData();
                    formData.append('subject', subject);
                    formData.append('message', message);
                    formData.append('contract_ids', contract_ids);
                    axios.post('/doc_management/email_agents_missing_earnest', formData, axios_options)
                    .then(function (response) {
                        get_earnest('all');
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                }

            });

        });

        $(document).on('click', '.delete-earnest-note-button', function() {

            let Earnest_ID = $(this).data('earnest-id');
            let note_id = $(this).data('note-id');

            let formData = new FormData();
            formData.append('note_id', note_id);
            axios.post('/agents/doc_management/transactions/delete_note', formData, axios_options)
            .then(function (response) {
                get_earnest_notes(Earnest_ID);
            })
            .catch(function (error) {
                console.log(error);
            });
        });

        let options = {
            menubar: false,
            statusbar: false,
            height: 370,
            selector: '#email_agent_earnest_message',
            toolbar: 'undo redo | styleselect | bold italic | forecolor backcolor | align outdent indent |'
        }
        text_editor(options);

    });

    function get_earnest(account_id) {

        $.each(['pending', 'active', 'missing', 'waiting'], function(index, tab) {

            axios.get('/doc_management/get_earnest_deposits', {
                params: {
                    account_id: account_id,
                    tab: tab
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                // reload if gets caught up
                if(!response.data) {
                    if(account_id == 'all') {
                        window.location.reload();
                    }
                }

                $('#'+tab+'_content').html(response.data);
                if(tab == 'missing') {
                    data_table('25', $('.earnest-table.'+tab+''), [6, 'desc'], [0,1,10,11], [], true, true, true, true, true);
                } else {
                    data_table('25', $('.earnest-table.'+tab+''), [5, 'desc'], [0], [], true, true, true, true, true);
                }

                $('.save-earnest-notes-button').off('click').on('click', function() {

                    let Earnest_ID = $(this).data('earnest-id');
                    let notes = $('.earnest-notes-'+Earnest_ID).val();

                    save_add_earnest_notes(Earnest_ID, notes);

                });

            })
            .catch(function (error) {
                console.log(error);
            });

        });

    }

    function save_add_earnest_notes(Earnest_ID, notes) {

        let formData = new FormData();
        formData.append('Earnest_ID', Earnest_ID);
        formData.append('notes', notes);
        axios.post('/agents/doc_management/transactions/save_add_earnest_notes', formData, axios_options)
        .then(function (response) {
            get_earnest_notes(Earnest_ID);
            $('.earnest-notes-'+Earnest_ID).val('');
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_earnest_notes(Earnest_ID) {

        axios.get('/agents/doc_management/transactions/get_earnest_notes', {
            params: {
                Earnest_ID: Earnest_ID
            }
        })
        .then(function (response) {
            $('#earnest_notes_div_'+Earnest_ID).html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    function email_button(action) {

        $('.email-agents-div').addClass('d-none');
        if(action == 'show') {
            $('.email-agents-div').removeClass('d-none');
        }

    }

}
