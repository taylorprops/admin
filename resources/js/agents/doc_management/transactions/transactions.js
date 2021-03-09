if (document.URL.match(/transactions$/) || document.URL.match(/transactions\?tab=[a-z]+/)) {

    $(function() {

        if($('#agent_referral').val() == '') {
            get_transactions('listings', 'active');
            get_transactions('contracts', 'active');
        }
        get_transactions('referrals', 'active');

        let tab = global_get_url_parameters('tab');
        if(tab != '') {
            $('[data-tab="' + tab + '"]').trigger('click');
        }

        $('#transactions_tabs .nav-link').on('click', function() {
            $('.view-option').first().trigger('click');
        });

        $('.view-option').on('click', function() {
            $('.view-option').removeClass('active');
            $(this).addClass('active');
            let type = $('#transactions_tabs .nav-link.active').data('tab');
            get_transactions(type, $(this).data('status'));
        });

    });


    function get_transactions(type, status) {

        let group = getCookie('user_group');

        axios.get('/agents/doc_management/transactions/get_transactions', {
            params: {
                type: type,
                status: status
            },
            headers: {
                'Accept-Version': 1,
                'Accept': 'text/html',
                'Content-Type': 'text/html'
            }
        })
        .then(function (response) {

            $('#'+type+'_div').html(response.data);
            let hidden_cols = [];
            if(group == 'agent') {
                hidden_cols = [2];
            }

            if(type == 'listings') {
                data_table('10', $('#'+type+'_div table'), [3, 'desc'], [0, 7], hidden_cols, false, true, true, true);
            } else if(type == 'contracts') {
                data_table('10', $('#'+type+'_div table'), [3, 'desc'], [0, 7], hidden_cols, false, true, true, true);
            } else if(type == 'referrals') {
                data_table('10', $('#'+type+'_div table'), [1, 'asc'], [0], hidden_cols, false, true, true, true);
            }

        })
        .catch(function (error) {
            console.log(error);
        });
    }

}
