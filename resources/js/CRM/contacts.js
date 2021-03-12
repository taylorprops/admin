if(document.URL.match(/contacts/)) {

    $(function() {

        get_contacts();

        $(document).on('change', '.check-all', function() {
            if($(this).is(':checked')) {
                $('.contact-checkbox').prop('checked', true);
            } else {
                $('.contact-checkbox').prop('checked', false);
            }
        });

        $(document).on('change', '.contact-checkbox, .check-all', function() {
            if($('.contact-checkbox:checked').length > 0) {
                $('.bulk-options').removeClass('hidden');
            } else {
                $('.bulk-options').addClass('hidden');
            }
        });

        $(document).on('click', '.add-contact-button', show_add_contact);

        $(document).on('click', '.edit-contact-button', function() {
            show_edit_contact($(this));
        });

        $(document).on('click', '.import-contacts-button', show_import);


        $(document).on('click', '.delete-button', delete_contacts);

        function get_contacts() {

            axios.get('/contacts/get_contacts')
            .then(function (response) {

                $('.contacts-table-div').html(response.data);

                data_table(25, $('#contacts_table'), [2, 'asc'], [0,1], [], true, true, true, true, true);

            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function delete_contacts() {

            $('#confirm_modal').modal().find('.modal-body').html('Are you sure you want to delete the selected contacts?');
            $('#confirm_modal').modal().find('.modal-title').html('Delete Contacts');
            $('#confirm_button').off('click').on('click', function() {

                let contact_ids = [];
                $('.contact-checkbox:checked').each(function() {
                    contact_ids.push($(this).data('contact-id'));
                });

                let formData = new FormData();
                formData.append('contact_ids', contact_ids);
                axios.post('/contacts/delete', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Contacts successfully deleted');
                    get_contacts();
                })
                .catch(function (error) {
                    console.log(error);
                });

            });

        }

        function show_add_contact() {

            $('#edit_contact_modal').modal('show');
            $('#edit_contact_modal_title').text('Add Contact');

            $('#save_edit_contact_button').off('click').on('click', save_contact);

        }

        function show_edit_contact(ele) {

            $('#edit_contact_modal').modal('show');
            $('#edit_contact_modal_title').text('Edit Contact');

            $('#contact_id').val(ele.data('contact-id'));
            $('#contact_first').val(ele.data('contact-first'));
            $('#contact_last').val(ele.data('contact-last'));
            $('#contact_company').val(ele.data('contact-company'));
            $('#contact_street').val(ele.data('contact-street'));
            $('#contact_city').val(ele.data('contact-city'));
            $('#contact_state').val(ele.data('contact-state'));
            $('#contact_zip').val(ele.data('contact-zip'));
            $('#contact_phone_cell').val(ele.data('contact-phone-cell'));
            $('#contact_phone_home').val(ele.data('contact-phone-home'));
            $('#contact_email').val(ele.data('contact-email'));

            $('#save_edit_contact_button').off('click').on('click', save_contact);

        }

        function save_contact() {

            let form = $('#edit_contact_form');
            let formData = new FormData(form[0]);
            axios.post('/contacts/save', formData, axios_options)
            .then(function (response) {
                $('#edit_contact_modal').modal('hide');
                get_contacts();
                toastr['success']('Contact Successfully Updated');
            })
            .catch(function (error) {

            });
        }

        function show_import() {

            $('#import_modal').modal('show');

            $('#save_import_button').off('click').on('click', run_import);

        }

        function run_import() {

            let form = $('#import_form');
            let formData = new FormData(form[0]);
            axios.post('/contacts/import_from_excel', formData, axios_options)
            .then(function (response) {
                $('#import_modal').modal('hide');
                get_contacts();
                toastr['success']('Contact Successfully Imported');
            })
            .catch(function (error) {
                console.log(error);
            });
        }


    });

}
