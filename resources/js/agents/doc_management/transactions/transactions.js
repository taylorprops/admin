if (document.URL.match(/transactions$/)) {

    $(function() {

        if($('#agent_referral').val() == '') {
            get_transactions('listings');
            get_transactions('contracts');
        }
        get_transactions('referrals');
    });

    function get_transactions(type) {

        axios.get('/agents/doc_management/transactions/get_transactions', {
            params: {
                type: type
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {
            $('#'+type+'_div').html(response.data);
            data_table($('#'+type+'_div table'), [1, 'asc'], [0], true, true, true, true);
        })
        .catch(function (error) {
            console.log(error);
        });
    }

}
