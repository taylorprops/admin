if(document.URL.match(/active_earnest/)) {

    $(function() {

        get_earnest('all');

        $('.earnest-deposit-account').on('change', function() {
            get_earnest($(this).val());
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
                let dt = data_table('25', $('.earnest-table'), [2, 'desc'], [0], [], true, true, true, true, true);

            })
            .catch(function (error) {
                console.log(error);
            });

        });

    }

}
