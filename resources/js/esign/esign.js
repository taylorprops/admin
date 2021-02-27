if(document.URL.match(/esign$/) || document.URL.match(/esign_show_sent/)) {

    $(function () {

        // show successful send modal
        if(document.URL.match(/esign_show_sent$/)) {
            $('#modal_success').modal().find('.modal-body').html('Your Documents Were Successfully Sent For Signatures');
            // remove esign_show_sent from url
            history.pushState(null, null, '/esign');
            let c = 0;
            let load_in_process = setInterval(function() {
                load_tab('in_process');
                if (++c === 5) {
                    window.clearInterval(load_in_process);
                }
            }, 1000);
        }

        setInterval(function() {
            load_tab('in_process');
        }, 5000);

        load_tab('in_process');

        $('#esign_tabs .nav-link').on('click', function() {
            load_tab($(this).data('tab'));
            if($(this).data('tab') == 'drafts') {
                load_tab('deleted_drafts');
            } else if($(this).data('tab') == 'templates') {
                load_tab('deleted_templates');
            } else if($(this).data('tab') == 'system_templates') {
                load_tab('deleted_system_templates');
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

                    data_table($('#drafts_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

                    $('.delete-draft-button').off('click').on('click', function() {
                        delete_draft($(this));
                    });

                } else if(tab == 'deleted_drafts') {

                    data_table($('#deleted_drafts_table'), [3, 'desc'], [0], [], false, true, true, true, true);

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

                } else if(tab == 'in_process') {

                    data_table($('#in_process_table'), [3, 'desc'], [4], [], false, true, true, true, true);
                    $('.cancel-envelope-button').off('click').on('click', function() {
                        cancel_envelope($(this));
                    });

                    $('.resend-envelope-button').off('click').on('click', function() {
                        resend_envelope($(this));
                    });

                } else if(tab == 'completed') {

                    data_table($('#completed_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

                } else if(tab == 'templates') {

                    data_table($('#templates_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

                    $('.delete-template-button').off('click').on('click', function() {
                        delete_template($(this));
                    });

                } else if(tab == 'deleted_templates') {

                    data_table($('#deleted_templates_table'), [3, 'desc'], [0], [], false, true, true, true, true);

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

                } else if(tab == 'system_templates') {

                    data_table($('#system_templates_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

                    $('.delete-system-template-button').off('click').on('click', function() {
                        delete_system_template($(this));
                    });

                } else if(tab == 'deleted_system_templates') {

                    data_table($('#deleted_system_templates_table'), [3, 'desc'], [0], [], false, true, true, true, true);

                    $('.restore-system-template-button').off('click').on('click', function() {
                        restore_system_template($(this));
                    });

                    setTimeout(function() {
                        $('.show-deleted-system-templates').addClass('hidden');
                        if($('#deleted_system_templates_count').val() > 0) {
                            $('.show-deleted-system-templates').removeClass('hidden');
                        } else {
                            $('#deleted_system_templates_div').collapse('hide');
                        }
                    }, 200);

                } else if(tab == 'cancelled') {

                    data_table($('#cancelled_table'), [3, 'desc'], [0], [], false, true, true, true, true);

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

            let template_id = ele.data('template-id');

            let formData = new FormData();
            formData.append('template_id', template_id);
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

            let template_id = ele.data('template-id');

            let formData = new FormData();
            formData.append('template_id', template_id);
            axios.post('/esign/restore_template', formData, axios_options)
            .then(function (response) {
                load_tab('deleted_templates');
                load_tab('templates');
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function delete_system_template(ele) {

            let template_id = ele.data('template-id');

            let formData = new FormData();
            formData.append('template_id', template_id);
            axios.post('/esign/delete_system_template', formData, axios_options)
            .then(function (response) {
                ele.closest('tr').fadeOut();
                load_tab('deleted_system_templates');
                setTimeout(function() {
                    ele.closest('tr').remove();
                }, 800);
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function restore_system_template(ele) {

            let template_id = ele.data('template-id');

            let formData = new FormData();
            formData.append('template_id', template_id);
            axios.post('/esign/restore_system_template', formData, axios_options)
            .then(function (response) {
                load_tab('deleted_system_templates');
                load_tab('system_templates');
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function cancel_envelope(ele) {

            $('#confirm_cancel_modal').modal('show');
            $('#confirm_cancel_button').off('click').on('click', function() {

                $('#confirm_cancel_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Cancelling');

                envelope_id = ele.data('envelope-id');
                ele.find('i').addClass('fa-spin');

                let formData = new FormData();
                formData.append('envelope_id', envelope_id);
                axios.post('/esign/cancel_envelope', formData, axios_options)
                .then(function (response) {
                    setTimeout(function() {
                        load_tab('in_process');
                        load_tab('cancelled');
                        $('#confirm_cancel_modal').modal('hide');
                    }, 1000);
                    toastr['success']('Signature Request Cancelled');
                })
                .catch(function (error) {
                    console.log(error);
                });

            });

        }

        function resend_envelope(ele) {

            $('#resend_envelope_modal').modal('show');
            $('#resend_envelope_button').off('click').on('click', function() {

                $('#resend_envelope_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Resending');

                envelope_id = ele.data('envelope-id');
                singer_id = ele.data('signer-id');
                ele.find('i').addClass('fa-spin');

                let formData = new FormData();
                formData.append('envelope_id', envelope_id);
                formData.append('singer_id', singer_id);
                axios.post('/esign/resend_envelope', formData, axios_options)
                .then(function (response) {
                    setTimeout(function() {
                        load_tab('in_process');
                        $('#resend_envelope_modal').modal('hide');
                        $('#resend_envelope_button').html('<i class="fal fa-check mr-2"></i> Confirm</a>');
                    }, 1000);
                    toastr['success']('Signature Request Resent');
                })
                .catch(function (error) {
                    console.log(error);
                });

            });

        }

    });

}
