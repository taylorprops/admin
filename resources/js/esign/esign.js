if(document.URL.match(/esign$/)) {

    $(function () {

        load_tab('sent');

        $('#esign_tabs .nav-link').on('click', function() {
            load_tab($(this).data('tab'));
            if($(this).data('tab') == 'drafts') {
                load_tab('deleted_drafts');
            } else if($(this).data('tab') == 'templates') {
                load_tab('deleted_templates');
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

                    $('.delete-draft-button').off('click').on('click', function() {
                        delete_draft($(this));
                    });

                } else if(tab == 'deleted_drafts') {

                    data_table($('#deleted_drafts_table'), [3, 'desc'], [0], false, true, true, true, true);

                    $('.restore-draft-button').off('click').on('click', function() {
                        restore_draft($(this));
                    });

                    setTimeout(function() {
                        $('.show-deleted-drafts').addClass('hidden');
                    if($('#deleted_drafts_count').val() > 0) {
                        $('.show-deleted-drafts').removeClass('hidden');
                    } else {
                        $('#deleted_drafts_div').collapse('hide');
                    }
                    }, 200);

                } else if(tab == 'sent') {

                    data_table($('#sent_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                } else if(tab == 'completed') {

                    data_table($('#completed_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                } else if(tab == 'templates') {

                    data_table($('#templates_table'), [3, 'desc'], [0,4], false, true, true, true, true);

                    $('.delete-template-button').off('click').on('click', function() {
                        delete_template($(this));
                    });

                } else if(tab == 'deleted_templates') {

                    data_table($('#deleted_templates_table'), [3, 'desc'], [0], false, true, true, true, true);

                    $('.restore-template-button').off('click').on('click', function() {
                        restore_template($(this));
                    });

                    setTimeout(function() {
                        $('.show-deleted-templates').addClass('hidden');
                        if($('#deleted_templates_count').val() > 0) {
                            $('.show-deleted-templates').removeClass('hidden');
                        } else {
                            $('#deleted_templates_div').collapse('hide');
                        }
                    }, 200);

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
                load_tab('deleted_drafts');
                setTimeout(function() {
                    ele.closest('tr').remove();
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
                load_tab('deleted_drafts');
                load_tab('drafts');
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function delete_template(ele) {

            let envelope_id = ele.data('envelope-id');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            axios.post('/esign/delete_template', formData, axios_options)
            .then(function (response) {
                ele.closest('tr').fadeOut();
                load_tab('deleted_templates');
                setTimeout(function() {
                    ele.closest('tr').remove();
                }, 800);
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function restore_template(ele) {

            let envelope_id = ele.data('envelope-id');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            axios.post('/esign/restore_template', formData, axios_options)
            .then(function (response) {
                load_tab('deleted_templates');
                load_tab('templates');
            })
            .catch(function (error) {
                console.log(error);
            });
        }



    });

}
