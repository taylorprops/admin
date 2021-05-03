if(document.URL.match(/esign_template_add_documents_and_signers/)) {


    $(function() {

        show_save_template();

        $('.available-signers-checkbox').on('change', function () {

            let id = $(this).data('id');
            if($(this).is(':checked')) {

                $('.selected-signer-item[data-id="'+id+'"]').removeClass('hidden').addClass('selected');
                $(this).closest('.list-group-item').addClass('active');

            } else {

                $('.selected-signer-item[data-id="'+id+'"]').addClass('hidden').removeClass('selected');
                $(this).closest('.list-group-item').removeClass('active');

            }

            show_save_template();

        });

        $('#template_upload').on('change', show_save_template);

        $('.remove-signer-button').on('click', function () {

            let id = $(this).data('id');
            $(this).closest('.selected-signer-item').addClass('hidden').removeClass('selected');
            $('.available-signers-checkbox[data-id="'+id+'"]').prop('checked', false).closest('.list-group-item').removeClass('active');

        });

        $('.selected-signers-list').sortable({
            handle: '.signer-handle'
        });

        $('#save_template_button').on('click', save_signers);


    });



    function save_signers() {


        $('#save_template_button').prop('disabled', true).html('Adding Signers <span class="spinner-border spinner-border-sm ml-2"></span>');

        let template_type = $('#template_type').val();
        let template_id = $('#template_id').val();

        let form = null;
        let formData;
        if(template_type != 'system') {
            form = $('#upload_form');
            formData = new FormData(form[0]);
        } else {
            formData = new FormData();
        }

        formData.append('template_type', template_type);

        let signers = [];

        let c = 0;
        $('.selected-signer-item.selected').each(function () {
            let data = {
                'order': c,
                'id': $(this).data('id'),
                'role': $(this).data('role')
            }
            signers.push(data);
            c += 1;
        });

        signers = JSON.stringify(signers);

        formData.append('template_id', template_id);
        formData.append('signers', signers);

        axios.post('/esign/esign_template_save_add_signers', formData, axios_options)
        .then(function (response) {
            template_id = response.data.template_id;
            window.location = '/esign/esign_template_add_fields/'+template_type+'/'+template_id;
        })
        .catch(function (error) {
            console.log(error);
        });


    }

    function show_save_template() {
        if($('.available-signers-checkbox:checked').length > 0 && $('#template_upload').val() != '') {
            $('#save_template_button').prop('disabled', false);
        } else {
            $('#save_template_button').prop('disabled', true);
        }
    }

}
