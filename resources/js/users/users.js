if (document.URL.match(/users/)) {

    $(function () {

        data_table(10, $('#users_table'), [2, 'asc'], [0, 1, 5], [], true, true, true, true, true, false);

        $(document).on('click', '.send-password-reset-button', function() {
            let ele = $(this);
            $('#confirm_modal').modal().find('.modal-body').html('Email reset password link to user?');
            $('#confirm_modal').modal().find('.modal-title').html('Send Password Reset Email');
            $('#confirm_button').on('click', function() {
                send_password_reset(ele);
            });
        });

        $(document).on('click', '.send-register-email-button', function() {
            let ele = $(this);
            $('#confirm_modal').modal().find('.modal-body').html('Send registration email to user?');
            $('#confirm_modal').modal().find('.modal-title').html('Send Registration Email');
            $('#confirm_button').on('click', function() {
                send_registration_email(ele);
            });
        });

        $('.users-table-wrapper').removeClass('hidden');

    });

    function send_registration_email(ele) {

        ele.html('<span class="spinner-border spinner-border-sm mr-2"></span> Sending...');
        let email = ele.data('email');

        axios.get('/register_employee/'+email, {
            params: {
                'email': email,
                '_token': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(function (response) {
            if(response.status == 200) {
                toastr['success']('Registration Email Sent');
                ele.html('Registration Email');
            } else {
                alert('error');
            }
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function send_password_reset(ele) {

        ele.html('<span class="spinner-border spinner-border-sm mr-2"></span> Sending...');
        let email = ele.data('email');
        let formData = new FormData();
        formData.append('email', email);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        axios.post('/password/email', formData, axios_options)
            .then(function (response) {
                let message = response.data.message;
                if(message.match(/password\sreset/)) {
                    toastr['success']('Reset Password Email Sent')
                } else {
                    alert('error');
                }
                ele.html('Send Reset Password Email');
            })
            .catch(function (error) {
                let error_message = error.response.data.errors.email[0];
                alert(error_message);
            });

    }

}




