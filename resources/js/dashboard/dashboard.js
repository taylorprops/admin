if(document.URL.match(/dashboard/)) {

    $(function() {

        $(document).on('click', '.view-alert-details-button', function() {
            show_alert_details($(this).data('type'), $(this).data('title'), $(this).data('details'));
        });

        get_transactions();

        get_upcoming_closings();

        get_admin_todo();

        get_upcoming_events();

        if(global_user.group == 'agent') {
            get_commissions();
        }

    });



    function get_upcoming_events() {

        axios.get('/dashboard/get_upcoming_events')
        .then(function (response) {
            $('#upcoming_events_mod').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_admin_todo() {

        axios.get('/dashboard/get_admin_todo')
        .then(function (response) {
            $('#admin_todo_mod').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_transactions() {

        axios.get('/dashboard/get_transactions')
        .then(function (response) {
            $('#transactions_mod').html(response.data);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_upcoming_closings() {

        axios.get('/dashboard/get_upcoming_closings')
        .then(function (response) {
            $('#upcoming_closings_mod').html(response.data);
            data_table('0', $('#upcoming_closings_table'), [1, 'asc'], [3], [], false, false, false, false, false, false);
        })
        .catch(function (error) {
            console.log(error);
        });

    }

    function get_commissions() {

        axios.get('/dashboard/get_commissions')
        .then(function (response) {
            $('#commissions_mod').html(response.data);
            data_table('0', $('#commissions_table'), [1, 'desc'], [], [], false, false, false, false, false, false);
        })
        .catch(function (error) {
            console.log(error);
        });
    }


    function show_alert_details(type, title, details) {

        $('.alert-details-item').hide();

        $('.'+type).show();

        $('#alert_details_modal').modal('show');

        $('#alert_details_modal_title').html(title);
        $('#alert_details_modal_details').html(details);


    }

}
