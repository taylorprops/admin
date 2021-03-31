if(document.URL.match(/dashboard/)) {

    $(function() {

        $(document).on('click', '.view-alert-details-button', function() {
            show_alert_details($(this).data('type'), $(this).data('title'), $(this).data('details'));
        });

        data_table('0', $('#upcoming_closings_table'), [1, 'asc'], [3], [], false, false, false, false, false, false);

    });

    function show_alert_details(type, title, details) {

        $('.alert-details-item').hide();

        $('.'+type).show();

        $('#alert_details_modal').modal('show');

        $('#alert_details_modal_title').html(title);
        $('#alert_details_modal_details').html(details);


    }

}
