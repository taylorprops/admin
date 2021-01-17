if(document.URL.match(/esign_add_signers/)) {

    $(function () {

        $('.add-signer-fields').find('.add-signer-field').on('change', function() {
            if($(this).val() !== '') {
                $('.signer-select').val('').trigger('change');
            }
        });

        $('.add-recipient-fields').find('.add-recipient-field').on('change', function() {
            if($(this).val() !== '') {
                $('.recipient-select').val('').trigger('change');
            }
        });

        $(document).on('click', '.save-add-user', function() {
            add_user($(this).data('type'));
        });

        $(document).on('click', '.remove-user', function() {
            remove_user($(this));
        });


        $(document).on('click', '#add_fields_button', save_signers);

        $('.signer-recipient-select').off('change').on('change', function() {

            let type = $(this).data('type');

            if($(this).val() != '') {

                $('.add-'+type+'-fields').find('.add-'+type+'-field').val('').trigger('change');

                let option = $(this).find('option:selected');

                $('.'+type+'-select-fields').removeClass('hidden');
                $('.'+type+'-select-fields').find('.add-'+type+'-name').val(option.data('name'));
                $('.'+type+'-select-fields').find('.add-'+type+'-email').val(option.data('email'));
                $('.'+type+'-select-fields').find('.add-'+type+'-role').val(option.data('member-type'));

            } else {
                $('.'+type+'-select-fields').addClass('hidden');
                $('.'+type+'-select-fields').find('.add-'+type+'-field').val('').trigger('change');
            }


        });


        function save_signers() {

            $('#add_fields_button').prop('disabled', true).html('Adding Signers <span class="spinner-border spinner-border-sm ml-2"></span>');

            let envelope_id = $('#envelope_id').val();

            let signers_data = [];

            let c = 0;
            $('.signer-item').each(function () {
                let data = {
                    'order': c,
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
                console.log(response.data.envelope_id);
                window.location = '/esign/esign_add_fields/'+response.data.envelope_id;
            })
            .catch(function (error) {
                console.log(error);
            });

        }


        function add_user(type) {

            let form, name, email, role;
            if($('.'+type+'-select').length > 0 && $('.'+type+'-select').val() != '') {
                form = $('.'+type+'-select-fields');
            } else {
                form = $('.add-'+type+'-fields');
            }

            $('.add-'+type+'-field').removeClass('required');

            form.find('.add-'+type+'-field').addClass('required');
            name = form.find('.add-'+type+'-name').val();
            email = form.find('.add-'+type+'-email').val();
            role = form.find('.add-'+type+'-role').val();

            let validate = validate_form(form);

            if(validate == 'yes') {

                $('#add_'+type+'_div').collapse('hide');

                let new_user = ' \
                <div class="list-group-item '+type+'-item d-flex justify-content-between align-items-center text-gray w-100" data-name="'+name+'" data-email="'+email +'" data-role="'+role+'"> \
                    <div class="row d-flex align-items-center w-100"> \
                    <div class="col-1 user-handle"><i class="fal fa-bars text-primary fa-lg"></i></div> \
                        <div class="col-1"><span class="'+type+'-count font-11 text-orange"></span></div> \
                        <div class="col-3 font-weight-bold">'+name+'</div> \
                        <div class="col-3">'+role+'</div> \
                        <div class="col">'+email+'</div> \
                    </div> \
                    <div><a href="javascript: void(0)"class="text-danger remove-user" data-type="'+type+'"><i class="fal fa-times fa-2x"></i></a></div> \
                </div>';

                $('.'+type+'s-container').append(new_user).sortable({
                    handle: '.user-handle',
                    stop: function() {
                        reorder(type);
                    }
                });

                $('.add-'+type+'-field').removeClass('required').val('');

                reorder(type);

                $('.next-div').removeClass('hidden');

            }

        }

        function remove_user(ele) {

            let type = ele.data('type');
            ele.closest('.'+type+'-item').fadeOut('slow');
            setTimeout(function() {

                ele.closest('.'+type+'-item').remove();

                reorder(type);

                if($('.'+type+'-item').length > 0) {
                    $('.next-div').removeClass('hidden');
                } else {
                    $('.next-div').addClass('hidden');
                }

            }, 800);

        }

        function reorder(type) {
            $('.'+type+'-item').each(function() {
                let position = $(this).index();
                $(this).find('.'+type+'-count').text(position);
            });
        }

    });

}
