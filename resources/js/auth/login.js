
$(function () {

    $(document).on('keyup', function (e) {
        if(e.key == 'Enter') {
            $('#login_button').trigger('click');
        }
    });

    $('#login_button').off('click').on('click', function(e) {

        e.preventDefault();

        let form = $('#login_form');
        let validate = validate_form(form);
        let previous_url = $('#previous_url').val();

        if(validate == 'yes') {

            let formData = new FormData(form[0]);
            axios.post('/login', formData, axios_options)
            .then(function (response) {
                if(previous_url != '' && !previous_url.match(/login/) && previous_url.replace(/http[s]*:\/\//, '') != location.hostname+'/') {
                    window.location = previous_url;
                } else {
                    window.location = '/dashboard';
                }

            })
            .catch(function(error) {
                let error_message = error.response.data.errors.email[0];
                if(error_message.match('credentials do not match our records')) {
                    error_message = 'Your email or password are incorrect';
                }
                $('#error_div').removeClass('hidden').find('#error_message').text(error_message);
            });

        }

    });

    $(document).on('click', '#forgot_password_button', function() {

        $('#forgot_password_modal').modal('show');

        $('#save_forgot_password_button').off('click').on('click', function() {

            $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Sending email...');

            let form = $('#forgot_password_form');
            let validate = validate_form(form);

            $('#reset_error_div').addClass('hidden');

            if(validate == 'yes') {

                let formData = new FormData(form[0]);
                axios.post('/password/email', formData, axios_options)
                .then(function (response) {
                    $('#forgot_password_modal').modal('hide');
                    $('#modal_success').modal().find('.modal-body').html('An email was just sent with a link to enter your new password');
                    $('#save_forgot_password_button').html('Reset Password <i class="fal fa-arrow-right ml-2"></i>');

                })
                .catch(function (error) {
                    let error_message = error.response.data.errors.email[0];
                    $('#reset_error_div').removeClass('hidden').find('#reset_error_message').text(error_message);
                    $('#save_forgot_password_button').html('Reset Password <i class="fal fa-arrow-right ml-2"></i>');
                });
            } else {
                $('#save_forgot_password_button').html('Reset Password <i class="fal fa-arrow-right ml-2"></i>');
            }
        });

    });

    $('#reset_password_button').off('click').on('click', function() {


        let form = $('#reset_password_form');
        let validate = validate_form(form);

        $('#reset_error_div').addClass('hidden').html('');

        if(validate == 'yes') {

            let formData = new FormData(form[0]);
            axios.post('/password/reset', formData, axios_options)
            .then(function (response) {
                window.location = '/dashboard';
            })
            .catch(function (error) {

                let error_messages_password = error.response.data.errors.password;
                if(error_messages_password) {
                    $('#reset_error_div').removeClass('hidden');
                    error_messages_password.forEach(function(error_message) {
                        $('#reset_error_div').append('<div><i class="fa fa-exclamation-triangle fa-xs mr-2"></i><span>'+error_message+'</span></div>');
                    });
                }

                let error_messages_email = error.response.data.errors.email;
                if(error_messages_email) {
                    $('#reset_error_div').removeClass('hidden');
                    error_messages_email.forEach(function(error_message) {

                        if(error_message.match('reset token is invalid')) {
                            error_message += '<br><div class="w-100 text-center mt-2">Click below to resend the password reset email<br><a href="javascript:void(0)" class="btn btn-sm btn-primary" id="forgot_password_button">Resend Email</a></div>';
                        }
                        $('#reset_error_div').append('<div><i class="fa fa-exclamation-triangle fa-xs mr-2"></i><span>'+error_message+'</span></div>');

                    });
                }

            });
        }


    });


})


