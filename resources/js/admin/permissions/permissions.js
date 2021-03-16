if(document.URL.match(/permissions/)) {

    $(function() {

        let options = {
            selector: '.permission-text-editor',
            inline: true,
            menubar: false,
            statusbar: false,
            toolbar: 'backcolor forecolor | bold italic underline'
        }
        text_editor(options);

        $('.list-group').sortable({
            handle: '.list-group-handle',
            stop: function (event, ui) {
                let items = [];

                $('.list-group-item').each(function() {
                    let config_id = $(this).data('config-id');
                    let order = $(this).index();
                    items.push({
                        config_id: config_id,
                        order: order
                    });
                });

                items = JSON.stringify(items);

                let formData = new FormData();
                formData.append('items', items);

                axios.post('/permissions/reorder_permissions', formData, axios_options)
                .then(function (response) {
                    toastr['success']('Reorder Successfully');
                })
                .catch(function (error) {

                });

            }
        });

        //$('.list-group').disableSelection();

        $('.save-config-button').on('click', function () {

            let type = $(this).data('type');
            let container = $(this).closest('.permission-container');

            let config_id = $(this).data('config-id');
            let title = container.find('.permission-text-editor[data-field="title"]').html();
            let description = container.find('.permission-text-editor[data-field="description"]').html();
            let emails = container.find('.emails').val();

            let formData = new FormData();
            formData.append('config_id', config_id);
            formData.append('title', title);
            formData.append('description', description);
            formData.append('emails', emails);
            axios.post('/permissions/save_permissions', formData, axios_options)
            .then(function (response) {
                toastr['success']('Changes Successfully Saved');
            })
            .catch(function (error) {
                console.log(error);
            });

        });

    });



}
