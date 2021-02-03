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

        $(document).on('click', '.quick-add', quick_add);

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

        $('.collapse').on('shown.bs.collapse', function () {
            $('#add_fields_button').prop('disabled', true);
        });
        $('.collapse').on('hidden.bs.collapse', function () {
            $('#add_fields_button').prop('disabled', false);
        });

        function quick_add() {

            let template_roles = ['Seller One', 'Seller Two', 'Buyer One', 'Buyer Two'];

            template_roles.forEach(function(template_role) {

                let role = template_role.match(/Seller/) ? 'Seller' : 'Buyer';

                let user = ' \
                <div class="list-group-item signer-item d-flex justify-content-between align-items-center text-gray w-100" data-name="" data-email="" data-role="'+role+'" data-template-role="'+template_role+'"> \
                    <div class="row d-flex align-items-center w-100"> \
                        <div class="col-1 user-handle"><i class="fal fa-bars text-primary fa-lg"></i></div> \
                        <div class="col-1"><span class="signer-count font-11 text-orange"></span></div> \
                        <div class="col-3 font-weight-bold hidden"></div> \
                        <div class="col-2">'+template_role+'</div> \
                        <div class="col-4 hidden"></div> \
                    </div> \
                    <div><a href="javascript: void(0)"class="text-danger remove-user" data-type="signer"><i class="fal fa-times fa-lg"></i></a></div> \
                </div>';

                $('.signers-container').append(user).sortable({
                    handle: '.user-handle',
                    stop: function() {
                        reorder('signer');
                    }
                });

            });

            $('.next-div').removeClass('hidden');
            reorder('signer');

        }


        function save_signers() {

            $('#add_fields_button').prop('disabled', true).html('Adding Signers <span class="spinner-border spinner-border-sm ml-2"></span>');

            let envelope_id = $('#envelope_id').val();
            let template_id = $('#template_id').val();
            let is_template = $('#is_template').val();

            let signers_data = [];

            let c = 0;
            $('.signer-item').each(function () {
                let data = {
                    'order': c,
                    'id': $(this).data('id') ?? null,
                    'name': $(this).data('name'),
                    'email': $(this).data('email'),
                    'role': $(this).data('role'),
                    'template_role': $(this).data('template-role')
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
                    'role': $(this).data('role'),
                    'template_role': $(this).data('template-role')
                }
                recipients_data.push(data);
                c += 1;
            });

            recipients_data = JSON.stringify(recipients_data);


            let formData = new FormData();
            formData.append('is_template', is_template);
            formData.append('envelope_id', envelope_id);
            formData.append('template_id', template_id);
            formData.append('signers_data', signers_data);
            formData.append('recipients_data', recipients_data);

            axios.post('/esign/esign_add_signers_to_envelope', formData, axios_options)
            .then(function (response) {
                window.location = '/esign/esign_add_fields/'+response.data.envelope_id+'/'+is_template+'/'+template_id;
            })
            .catch(function (error) {
                console.log(error);
            });

        }


        function add_user(type) {

            let form, role, template_role, display_role;
            let name = '';
            let email = '';
            let hidden = '';
            let other_selected = 'no';

            if($('#is_template').val() == 'yes') {

                form = $('.add-template-'+type+'-fields');
                template_role = form.find('.add-'+type+'-role').val();
                role = form.find('.add-'+type+'-role').val().replace(/\s(One|Two|Three|Four)/, '');
                hidden = 'hidden';
                display_role = template_role;

            } else {

                if($('.'+type+'-select').length > 0 && $('.'+type+'-select').val() != '') {
                    form = $('.'+type+'-select-fields');
                } else {
                    form = $('.add-'+type+'-fields');
                    other_selected = 'yes';
                }

                $('.add-'+type+'-field').removeClass('required');

                form.find('.add-'+type+'-field').addClass('required');
                name = form.find('.add-'+type+'-name').val();
                email = form.find('.add-'+type+'-email').val();
                role = form.find('.add-'+type+'-role').val();
                display_role = role;

            }

            let validate = validate_form(form);

            if(validate == 'yes') {

                $('#add_'+type+'_div').collapse('hide');

                let new_user = ' \
                <div class="list-group-item '+type+'-item d-flex justify-content-between align-items-center text-gray w-100" data-name="'+name+'" data-email="'+email +'" data-role="'+role+'" data-template-role="'+template_role+'"> \
                    <div class="row d-flex align-items-center w-100"> \
                        <div class="col-1 user-handle"><i class="fal fa-bars text-primary fa-lg"></i></div> \
                        <div class="col-1"><span class="'+type+'-count font-11 text-orange"></span></div> \
                        <div class="col-3 '+hidden+' font-weight-bold">'+name+'</div> \
                        <div class="col-2">'+display_role+'</div> \
                        <div class="col-4 '+hidden+'">'+email+'</div> \
                    </div> \
                    <div><a href="javascript: void(0)"class="text-danger remove-user" data-type="'+type+'"><i class="fal fa-times fa-lg"></i></a></div> \
                </div>';

                $('.'+type+'s-container').append(new_user).sortable({
                    handle: '.user-handle',
                    stop: function() {
                        reorder(type);
                    }
                });

                $('.add-'+type+'-field').removeClass('required').val('');

                if(other_selected == 'yes') {
                    $('.add-'+type+'-role').val('Other');
                }

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

            }, 400);

        }

        function reorder(type) {
            $('.'+type+'-item').each(function() {
                let position = $(this).index();
                $(this).find('.'+type+'-count').text(position);
            });
        }

    });

}
