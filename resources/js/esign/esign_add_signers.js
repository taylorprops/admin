if(document.URL.match(/esign_add_signers/)) {

    $(function () {

        $(document).on('click', '#save_add_signer', function() {
            save_add_signer($(this).data('type'));
        });

        $(document).on('click', '.remove-user', function() {
            remove_user($(this));
        });

        $(document).on('keyup', '.signer-email', function() {
            let type = $(this).data('type');
            $(this).closest('.'+type+'-item').data('email', $(this).val());
        });


        $(document).on('click', '#save_signers_button', save_signers);

        $('#add_signer_member_id').off('change').on('change', function() {

            let type = $(this).data('type');

            if($(this).val() != '') {

                let option = $(this).find('option:selected');

                $('#add_signer_name').val(option.data('name'));
                $('#add_signer_email').val(option.data('email'));
                $('#add_signer_role').val(option.data('member-type'));

                save_add_signer(type);

            } else {
                $('.add-signer-fields').find('.add-signer-field').val('').trigger('change');
            }


        });


        $('.signers-container').sortable({
            handle: '.user-handle',
            stop: function() {
                reorder('signer');
            }
        });
        reorder('signer');
        $('.recipients-container').sortable({
            handle: '.user-handle',
            stop: function() {
                reorder('recipient');
            }
        });
        reorder('recipient');

        $('.envelope-role[data-role="Signer"]').focus().trigger('click');

        $('.envelope-role').on('click', function() {

            let role = $(this).data('role');

            $('.envelope-role').removeClass('active');
            $(this).addClass('active');

            $('#save_type, #header_text').text(role);
            $('#envelope_role').val(role);

            $('#save_add_signer, #add_signer_member_id').data('type', role.toLowerCase());

        });

        show_no_results();

        disable_selected_signers();

        ////////////////////////////////////////////////////////////////

        function disable_selected_signers() {

            $('.form-select-li').show();

            $('.signer-item, .recipient-item').each(function() {

                let email = $(this).data('email');
                let value = $('#add_signer_member_id').find('option[data-email="'+email+'"]').val();
                $('.form-select-li[data-value="'+value+'"]').hide();

            });

        }

        function save_signers() {

            let form = $('.page-esign-add-signers');
            let validate = validate_form(form);

            if(validate == 'yes') {

                $('#save_signers_button').prop('disabled', true).html('Adding Signers <span class="spinner-border spinner-border-sm ml-2"></span>');

                let envelope_id = $('#envelope_id').val();

                let signers_data = [];

                let c = 0;
                $('.signer-item').each(function () {
                    let data = {
                        'order': c,
                        'id': $(this).data('id') ?? null,
                        'name': $(this).data('name'),
                        'email': $(this).data('email'),
                        'role': $(this).data('role')
                    }
                    signers_data.push(data);
                    c += 1;
                });

                signers_data = JSON.stringify(signers_data);

                let recipients_data = [];

                c = 0;
                $('.recipient-item').each(function () {
                    let data = {
                        'order': c,
                        'id': $(this).data('id') ?? null,
                        'name': $(this).data('name'),
                        'email': $(this).data('email'),
                        'role': $(this).data('role')
                    }
                    recipients_data.push(data);
                    c += 1;
                });

                recipients_data = JSON.stringify(recipients_data);


                let formData = new FormData();
                formData.append('envelope_id', envelope_id);
                formData.append('signers_data', signers_data);
                formData.append('recipients_data', recipients_data);

                axios.post('/esign/esign_add_signers_to_envelope', formData, axios_options)
                .then(function (response) {
                    window.location = '/esign/esign_add_fields/'+response.data.envelope_id;
                })
                .catch(function (error) {
                    console.log(error);
                });

            }

        }


        function save_add_signer(type) {

            let form = $('.add-signer-fields');
            $('.add-signer-field').addClass('required');

            let name = $('#add_signer_name').val();
            let email = $('#add_signer_email').val();
            let role = $('#add_signer_role').val();

            let validate = validate_form(form);

            if(validate == 'yes') {

                let new_user = ' \
                <div class="list-group-item '+type+'-item d-flex justify-content-between align-items-center text-gray w-100 py-0 px-0" \
                    data-name="'+name+'" \
                    data-email="'+email+'" \
                    data-role="'+role+'"> \
                    <div class="ml-2 w-8 text-center"> \
                        <a href="javascript: void(0)" class="user-handle"><i class="fal fa-bars text-primary fa-lg"></i></a> \
                    </div> \
                    <div class="w-6"> \
                        <span class="'+type+'-count font-11 text-orange"></span> \
                    </div> \
                    <div class="w-34"> \
                        <span class="font-10">'+name+'</span> \
                        <br> \
                        <span class="font-9 font-italic">'+role+'</span> \
                    </div> \
                    <div class="w-34"> \
                        <input type="text" class="custom-form-element form-input '+type+'-email required" data-type="'+type+'" value="'+email+'" data-label="Email"> \
                    </div> \
                    <div class="w-8 text-center"> \
                        <button type="button" class="btn btn-danger remove-user" data-type="'+type+'"><i class="fal fa-times fa-lg"></i></button> \
                    </div> \
                </div> \
                ';


                $('.'+type+'s-container').append(new_user).sortable({
                    handle: '.user-handle',
                    stop: function() {
                        reorder(type);
                    }
                });

                $('.add-signer-field').removeClass('required').val('');

                reorder(type);

                disable_selected_signers();

                $('.no-'+type).addClass('hidden');

                show_no_results();

            }

        }


        function remove_user(ele) {

            let type = ele.data('type');
            ele.closest('.'+type+'-item').remove();

            ele.closest('.'+type+'-item').remove();
            reorder(type);

            setTimeout(function() {
                disable_selected_signers();
            }, 400);

            if($('.'+type+'-item').length > 0) {
                $('.no-'+type).addClass('hidden');
            } else {
                $('.no-'+type).removeClass('hidden');
            }

            show_no_results();

        }

        function reorder(type) {

            $('.'+type+'-item').each(function() {

                let previous = $(this).prevAll('.'+type+'-item'),
                index = previous.length;
                $(this).find('.'+type+'-count').text(index + 1);
            });
        }

        function show_no_results() {

            $('.no-signers, .no-recipients').addClass('hidden');
            if($('.signer-item').length == 0) {
                $('.no-signers').removeClass('hidden');
            }
            if($('.recipient-item').length == 0) {
                $('.no-recipients').removeClass('hidden');
            }

        }

    });

}
