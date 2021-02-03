if(document.URL.match(/esign_add_documents/) || document.URL.match(/esign_add_template_documents/)) {

    $(function () {


        $('#esign_file_upload').off('change').on('change', upload_files);

        $(document).on('click', '.remove-upload-button', function() {
            $(this).closest('li').fadeOut('slow');
            setTimeout(function() {
                $(this).closest('li').remove();
            }, 800);
        });

        $(document).on('click', '#create_envelope_button', create_envelope);

        $(document).on('click', '.show-apply-template-button', function(e) {
            show_apply_template($(this));
        });

        $('#uploads_div').sortable({
            handle: '.file-handle'
        });

        data_table($('#templates_table'), [1, 'asc'], [0], false, true, true, true);

        if($('#from_upload').val() == 'yes') {
            create_envelope();
            global_loading_on('', '<h4 class="text-white">Importing Documents...</h4>');
        }

        ////////// functions //////////


        function show_apply_template(ele) {

            let li = ele.closest('.upload-li');

            $('#add_template_modal').modal('show');

            $('.apply-template-button').off('click').on('click', function() {

                let template_id = $(this).data('template-id');
                li.data('template-applied-id', template_id);

                $('#add_template_modal').modal('hide');

                let template_status_html = ' \
                <div class="no-wrap"> \
                    <span class="text-success"><i class="fal fa-check mr-2"></i> <span class="font-8">Template Applied</span></span> \
                    <br> \
                    <div class="text-right"> \
                        <a href="javascript: void(0)" class="show-apply-template-button small">Edit <i class="fad fa-pencil ml-1"></i></a> \
                    </div> \
                </div> \
                ';

                li.find('.template-status').html(template_status_html);


            });
        }

        function create_envelope() {

            let is_template = $('#is_template').val();
            let from_upload = $('#from_upload').val();
            let template_name = '';
            if(is_template == 'yes') {
                let form = $('.template-name-div');
                let validate = validate_form(form);
                if(validate == 'yes') {
                    template_name = $('#template_name').val();
                } else {
                    return false;
                }
            }

            $('#create_envelope_button').prop('disabled', true).html('Adding Documents <span class="spinner-border spinner-border-sm ml-2"></span>');

            let Listing_ID = $('#Listing_ID').val() > 0 ? $('#Listing_ID').val() : 0;
            let Contract_ID = $('#Contract_ID').val() > 0 ? $('#Contract_ID').val() : 0;
            let Referral_ID = $('#Referral_ID').val() > 0 ? $('#Referral_ID').val() : 0;
            let transaction_type = $('#transaction_type').val();
            let Agent_ID = $('#Agent_ID').val();
            let User_ID = $('#User_ID').val();


            let file_data = [];
            $('.upload-li').each(function () {

                let data = {
                    'template_id': $(this).data('template-id') ?? 0,
                    'upload_id': $(this).data('upload-id') ?? 0,
                    'document_id': $(this).data('document-id') ?? 0,
                    'template_applied_id': $(this).data('template-applied-id') ?? 0,
                    'file_type': $(this).data('file-type'),
                    'file_name': $(this).data('file-name'),
                    'document_id': $(this).data('document-id') ?? 0,
                    'file_location': $(this).data('file-location')
                }
                file_data.push(data);

            });

            file_data = JSON.stringify(file_data);


            let formData = new FormData();
            formData.append('is_template', is_template);
            formData.append('from_upload', from_upload);
            formData.append('template_name', template_name);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);
            formData.append('Agent_ID', Agent_ID);
            formData.append('User_ID', User_ID);
            formData.append('file_data', file_data);


            axios.post('/esign/esign_create_envelope', formData, axios_options)
            .then(function (response) {
                window.location = '/esign/esign_add_signers/'+response.data.envelope_id+'/'+response.data.is_template+'/'+response.data.template_id;
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        function upload_files() {

            if($('#esign_file_upload').val() != '') {

                global_loading_on('', '<div class="h5 text-white">Adding Documents</div>');

                let form = $('#upload_form');
                let formData = new FormData(form[0]);

                axios_options['header'] = { 'content-type': 'multipart/form-data' };
                axios.post('/esign/upload', formData, axios_options)
                .then(function (response) {

                    $('#esign_file_upload').val('');

                    response.data.docs_to_display.forEach(function (doc_to_display) {

                        let file_name = doc_to_display.file_name;
                        let file_location = doc_to_display.file_location;
                        let image_location = doc_to_display.image_location;

                        let add_template = '';
                        if($('#is_template').val() == 'no') {
                            add_template = ' \
                            <div class="mr-4 template-status"> \
                                <a href="javascript: void(0)" class="btn btn-sm btn-primary show-apply-template-button"><i class="fal fa-plus mr-2 fa-lg"></i> Add Template</a> \
                            </div> \
                            ';
                        }

                        let upload_li = ' \
                        <li class="list-group-item upload-li" data-file-location="'+file_location+'" data-file-type="user" data-file-name="'+file_name+'" data-template-id="" data-template-applied-id=""> \
                            <div class="d-flex justify-content-between align-items-center"> \
                                <div class="d-flex justify-content-start align-items-center"> \
                                    <div class="file-preview mr-4 file-handle"> \
                                        <i class="fal fa-bars text-primary fa-lg"></i> \
                                    </div> \
                                    <div class="file-preview mr-2 file-handle"> \
                                        <img src="'+image_location+'" style="height: 60px"> \
                                    </div> \
                                    <div> \
                                        <a href="'+file_location+'" target="_blank">'+file_name+'</a> \
                                    </div> \
                                </div> \
                                <div class="d-flex justify-content-end align-items-center"> \
                                    '+add_template+' \
                                    <div> \
                                        <a href="javascript: void(0)" class="remove-upload-button"><i class="fal fa-times text-danger fa-2x"></i></a> \
                                    </div> \
                                </div> \
                            </div> \
                        </li> \
                        ';

                        $('#uploads_div').append(upload_li);
                    });

                    $('.upload-li').data('index', $(this).index());

                    if($('#uploads_div li').length > 0) {
                        $('#uploads_container').removeClass('hidden');
                        $('.next-div').removeClass('hidden');
                        global_loading_off();
                    }

                })
                .catch(function (error) {

                });

            }

        }

    });

}
