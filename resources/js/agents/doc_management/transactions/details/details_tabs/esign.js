if (document.URL.match(/transaction_details/)) {


    $(function() {



    });

    window.esign_init = function() {

        load_tab('in_process');

        $('#esign_tabs .nav-link').on('click', function() {
            load_tab($(this).data('tab'));
            if($(this).data('tab') == 'drafts') {
                load_tab('deleted_drafts');
            }
        });

    }

    window.load_tab = function(tab) {

        let envelope_id = $('#envelope_id').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let Referral_ID = $('#Referral_ID').val();
        let transaction_type = $('#transaction_type').val();

        axios.get('/agents/doc_management/transactions/esign/get_'+tab, {
            params: {
                envelope_id: envelope_id,
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                Referral_ID: Referral_ID,
                transaction_type: transaction_type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('#'+tab+'_esign_div').html(response.data);

            if(tab == 'drafts') {

                data_table('10', $('#drafts_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

                $(document).on('click', '.delete-draft-button', function() {
                    delete_draft($(this));
                });

            } else if(tab == 'deleted_drafts') {

                data_table('10', $('#deleted_drafts_table'), [3, 'desc'], [0], [], false, true, true, true, true);

                $(document).on('click', '.restore-draft-button', function() {
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

                data_table('10', $('#in_process_table'), [3, 'desc'], [4], [], false, true, true, true, true);
                $(document).on('click', '.cancel-envelope-button', function() {
                    cancel_envelope($(this));
                });

                $(document).on('click', '.resend-envelope-button', function() {
                    resend_envelope($(this));
                });

            } else if(tab == 'completed') {

                data_table('10', $('#completed_table'), [3, 'desc'], [0,4], [], false, true, true, true, true);

            } else if(tab == 'cancelled') {

                data_table('10', $('#cancelled_table'), [3, 'desc'], [0], [], false, true, true, true, true);

            }

        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.delete_draft = function(ele) {

        let envelope_id = ele.data('envelope-id');

        let formData = new FormData();
        formData.append('envelope_id', envelope_id);
        axios.post('/agents/doc_management/transactions/esign/delete_draft', formData, axios_options)
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

    window.restore_draft = function(ele) {

        let envelope_id = ele.data('envelope-id');

        let formData = new FormData();
        formData.append('envelope_id', envelope_id);
        axios.post('/agents/doc_management/transactions/esign/restore_draft', formData, axios_options)
        .then(function (response) {
            load_tab('deleted_drafts');
            load_tab('drafts');
        })
        .catch(function (error) {
            console.log(error);
        });
    }

    window.cancel_envelope = function(ele) {

        $('#confirm_cancel_modal').modal('show');
        $('#confirm_cancel_button').off('click').on('click', function() {

            $('#confirm_cancel_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Cancelling');

            envelope_id = ele.data('envelope-id');
            ele.find('i').addClass('fa-spin');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            axios.post('/agents/doc_management/transactions/esign/cancel_envelope', formData, axios_options)
            .then(function (response) {
                setTimeout(function() {
                    $('[data-envelope-id="'+envelope_id+'"]').closest('tr').fadeOut();
                    $('#confirm_cancel_modal').modal('hide');
                    $('#confirm_cancel_button').html('<i class="fal fa-check mr-2"></i> Confirm');
                    load_tab('cancelled');
                }, 1000);
                toastr['success']('Signature Request Cancelled');
            })
            .catch(function (error) {
                console.log(error);
            });

        });

    }

    window.resend_envelope = function(ele) {

        $('#resend_envelope_modal').modal('show');
        $('#resend_envelope_button').off('click').on('click', function() {

            $('#resend_envelope_button').html('<span class="spinner-border spinner-border-sm mr-2"></span> Resending');

            envelope_id = ele.data('envelope-id');
            singer_id = ele.data('signer-id');
            ele.find('i').addClass('fa-spin');

            let formData = new FormData();
            formData.append('envelope_id', envelope_id);
            formData.append('singer_id', singer_id);
            axios.post('/agents/doc_management/transactions/esign/resend_envelope', formData, axios_options)
            .then(function (response) {

                load_tab('in_process');
                $('#resend_envelope_modal').modal('hide');
                $('#resend_envelope_button').html('<i class="fal fa-check mr-2"></i> Confirm</a>');

                if(response.data.status == 'document_deleted') {
                    $('#modal_info').modal().find('.modal-body').html('The document you were trying to send was already cancelled. It may have expired or been declined by a signer. It has been moved to the Cancelled folder');
                } else {
                    toastr['success']('Signature Request Resent');
                }

            })
            .catch(function (error) {
                console.log(error);
            });

        });

    }

}
