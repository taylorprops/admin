if(document.URL.match(/active_earnest/)) {

    $(function() {

        get_earnest('all');

        $('.earnest-deposit-account').on('change', function() {
            get_earnest($(this).val());
        });

        $(document).on('change', '.check-all', function() {
            if($(this).is(':checked')) {
                $('.deposit-input').prop('checked', true);
            } else {
                $('.deposit-input').prop('checked', false);
            }
        });

        $(document).on('change', '.deposit-input, .check-all', function() {
            if($('.deposit-input:checked').length > 0) {
                email_button('show');
                $('button.email-agent').prop('disabled', true);
            } else {
                email_button('hide');
                $('button.email-agent').prop('disabled', false);
            }
        });

    });

    function get_earnest(account_id) {

        $.each(['active', 'missing', 'waiting'], function(index, tab) {

            axios.get('/doc_management/get_earnest_deposits', {
                params: {
                    account_id: account_id,
                    tab: tab
                },
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                $('#'+tab+'_content').html(response.data);
                console.log(tab);
                if(tab == 'missing') {
                    let dt = data_table('25', $('.earnest-table'), [7, 'desc'], [0,1,2], [], true, true, true, true, true);
                } else {
                    let dt = data_table('25', $('.earnest-table'), [5, 'desc'], [0], [], true, true, true, true, true);
                }

            })
            .catch(function (error) {
                console.log(error);
            });

        });

    }

    function email_button(action) {

        $('.email-agents-div').addClass('d-none');
        if(action == 'show') {
            $('.email-agents-div').removeClass('d-none');
        }

    }

}
