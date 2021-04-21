if(document.URL.match(/bug_reports/)) {

    $(function() {

        data_table(10, $('#bug_report_table'), [3, 'desc'], [0], [], true, true, true, true, true);

        $('.mark-resolved-button').off('click').on('click', function() {

            let id = $(this).data('id');
            let action = $(this).data('action');

            let form = $('#steps_form');
            let formData = new FormData(form[0]);
            formData.append('id', id);
            formData.append('action', action);
            axios.post('/bug_reports/mark_resolved', formData, axios_options)
            .then(function (response) {
                $('.active-option, .not-active-option').removeClass('hidden');
                if(action == 'no') {
                    $('.active-option').addClass('hidden');
                    toastr['success']('Report Marked Resolved');
                } else {
                    $('.not-active-option').addClass('hidden');
                    toastr['success']('Report Marked Not Resolved');
                }

            })
            .catch(function (error) {
                console.log(error);
            });
        });

        $('#email_response_button').off('click').on('click', function() {

            $('#email_general_modal').modal('show');

            $('#email_general_to').val($(this).data('user-name')+' <'+$(this).data('user-email')+'>');
            $('#email_general_subject').val('Bug Report Response');
            $('#email_general_message').prepend('Hello '+$(this).data('user-first-name'));
            $('#email_general_message').append('<hr>Replying to:<br>'+$(this).data('message'));

            let options = {
                selector: '#email_general_message',
                menubar: 'edit format table',
                statusbar: false,
                plugins: 'image table',
                toolbar: 'image | undo redo | styleselect | bold italic | forecolor backcolor | align outdent indent |',
                images_upload_url: '/text_editor/file_upload',
                table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                height: '300',
                relative_urls : false,
                //remove_script_host : true,
                document_base_url: location.hostname
            }
            text_editor(options);

            $('#send_email_general_button').off('click').on('click', send_email_general);


        });



    });

}
