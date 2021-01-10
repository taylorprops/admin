
if(document.URL.match(/common_fields/)) {

    $(function() {

        get_common_fields();

        $(document).on('focus', '.common-field-input, .common-field-input-value, .form-select-search-input', function() {
            $('.save-edit-common-field-button').hide();
            $(this).closest('.common-field-input-container').find('.save-edit-common-field-button').show();
        });

        $(document).on('click', '.form-ele *', function() {
            $('.save-edit-common-field-button').hide();
            $(this).closest('.common-field-input-container').find('.save-edit-common-field-button').show();
        });

        $(document).on('click', '.save-edit-common-field-button', function() {
            let id = $(this).data('id');
            let container = $(this).closest('.common-field-input-container');
            let field_name = container.find('.common-field-input-value').text();
            let field_type = container.find('.field-type').val();
            let group_id = container.find('.group-id').val();
            let sub_group_id = container.find('.sub-group-id').val();
            let db_column_name = container.find('.field-name-db').val();
            save_edit_common_field(id, field_name, field_type, group_id, sub_group_id, db_column_name);
        });

        $(document).on('click', '#save_add_common_field_button', function() {
            $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Saving');
            let field_name = $('#field_name').val();
            let field_type = $('#field_type').val();
            let group_id = $('#group_id').val();
            let sub_group_id = $('#sub_group_id').val();
            let db_column_name = $('#db_column_name').val();
            save_add_common_field(field_name, field_type, group_id, sub_group_id, db_column_name);
        });



        function get_common_fields() {
            axios.get('/doc_management/resources/get_common_fields', {
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                $('#common_fields_div').html(response.data);

                let options = {
                    selector: '.common-field-input',
                    inline: true,
                    menubar: false,
                    statusbar: false,
                    toolbar: false,
                    plugins: 'save'
                }
                text_editor(options);

                //form_elements();

                $('.sortable-fields').sortable({
                    //placeholder: 'bg-sortable',
                    handle: '.sortable-handle',
                    stop: function (event, ui) {

                        let ele = $(ui.item);
                        let fields = [];

                        ele.closest('.sortable-fields').find('.common-field-input-container').each(function() {
                            let field_id = $(this).data('field-id');
                            let order = $(this).index();
                            fields.push({
                                field_id: field_id,
                                order: order
                            });
                        });

                        fields = JSON.stringify(fields);

                        let formData = new FormData();
                        formData.append('fields', fields);

                        axios.post('/doc_management/resources/reorder_common_fields', formData, axios_options)
                        .then(function (response) {
                            toastr['success']('Reorder Successfully');
                        })
                        .catch(function (error) {

                        });

                    }

                });
                $('.sortable-fields').disableSelection();

            })
            .catch(function (error) {

            });
        }

        function save_add_common_field(field_name, field_type, group_id, sub_group_id, db_column_name) {
            let formData = new FormData();
            formData.append('field_name', field_name);
            formData.append('field_type', field_type);
            formData.append('group_id', group_id);
            formData.append('sub_group_id', group_id);
            formData.append('db_column_name', db_column_name);
            axios.post('/doc_management/resources/save_add_common_field', formData, axios_options)
            .then(function (response) {
                toastr['success']('Field Successfully Added');
                get_common_fields();
                $('#add_common_field_collapse').collapse('hide').find('input, select').val('');
                //select_refresh();
                $('#save_add_common_field_button').html('<i class="fal fa-save mr-2"></i> Save');
            })
            .catch(function (error) {

            });
        }

        function save_edit_common_field(id, field_name, field_type, group_id, sub_group_id, db_column_name) {

            let formData = new FormData();
            formData.append('id', id);
            formData.append('field_name', field_name);
            formData.append('field_type', field_type);
            formData.append('group_id', group_id);
            formData.append('sub_group_id', sub_group_id);
            formData.append('db_column_name', db_column_name);
            axios.post('/doc_management/resources/save_edit_common_field', formData, axios_options)
            .then(function (response) {
                toastr['success']('Field Successfully Saved');
            })
            .catch(function (error) {

            });
        }

    })

}
