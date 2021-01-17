if(document.URL.match(/esign$/)) {

    $(function () {

        load_tab('drafts');
        load_tab('deleted_drafts');

        $('#esign_tabs .nav-link').on('click', function() {
            load_tab($(this).data('tab'));
            if($(this).data('tab') == 'drafts') {
                load_tab('deleted_drafts');
            }
        });



        // functions

        function load_tab(tab) {

            let envelope_id = $('#envelope_id').val();

            axios.get('/esign/get_'+tab, {
                params: {
                    envelope_id: envelope_id
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                $('#'+tab+'_div').html(response.data);

                if(tab == 'drafts') {

                    data_table($('#drafts_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                    $(document).on('click', '.delete-draft-button', function() {
                        delete_draft($(this));
                    });

                } else if(tab == 'deleted_drafts') {

                    data_table($('#deleted_drafts_table'), [3, 'desc'], [0], false, true, true, true, true);

                    $(document).on('click', '.restore-draft-button', function() {
                        restore_draft($(this));
                    });

                } else if(tab == 'sent') {

                    data_table($('#sent_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                } else if(tab == 'completed') {

                    data_table($('#completed_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                }

            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function delete_draft(ele) {

            let envelope_id = ele.data('envelope-id');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            axios.post('/esign/delete_draft', formData, axios_options)
            .then(function (response) {
                ele.closest('tr').fadeOut();
                setTimeout(function() {
                    ele.closest('tr').remove();
                    load_tab('deleted_drafts');
                }, 800);
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function restore_draft(ele) {

            let envelope_id = ele.data('envelope-id');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            axios.post('/esign/restore_draft', formData, axios_options)
            .then(function (response) {
                ele.closest('tr').fadeOut();
                setTimeout(function() {
                    ele.closest('tr').remove();
                    load_tab('drafts');
                }, 800);
            })
            .catch(function (error) {
                console.log(error);
            });
        }



    });

}
