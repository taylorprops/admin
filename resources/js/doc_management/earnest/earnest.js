if(document.URL.match(/balance_earnest/)) {

    $(function() {

        get_earnest_totals();
        get_earnest_checks();

        $('#earnest_search_input').on('keyup', function() {
            search_earnest_checks($(this).val());
        });

        //////////// functions ////////////

        function get_earnest_totals(active_index = null) {

            axios.get('/doc_management/get_earnest_totals', {
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {
                $('.earnest-totals-container').html(response.data);
                if(active_index) {
                    $('.earnest-totals-tab.active').removeClass('active');
                    $('.earnest-totals-tab').eq(active_index).addClass('active');
                }
                $('.earnest-totals-tab').on('click', function() {
                    $('html, body').animate({scrollTop:0}, 200, 'swing');
                });
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function get_earnest_checks() {

            axios.get('/doc_management/get_earnest_checks', {
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {

                $('.earnest-checks-container').html(response.data);
                data_table($('.earnest-checks-table-in'), [1, 'desc'], [0, 7, 8], [], true, true, true, false);
                data_table($('.earnest-checks-table-out'), [1, 'desc'], [0, 7], [], true, true, true, false);
                data_table($('.earnest-checks-table-in-recent'), [1, 'desc'], [0, 7, 8], [], true, true, true, true);
                data_table($('.earnest-checks-table-out-recent'), [1, 'desc'], [0, 7], [], true, true, true, true);
                //form_elements();

                $('.cleared-checkbox').on('change', function() {
                    cleared_bounced($(this));
                });

            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function cleared_bounced(checkbox) {

            let other_checkbox = checkbox.closest('.check-row').find('.cleared-checkbox').not(checkbox);
            if(other_checkbox) {
                other_checkbox.prop('checked', false);
            }

            let formData = new FormData();
            let check_id = checkbox.data('check-id');
            let check_type = checkbox.data('check-type');
            let Earnest_ID = checkbox.data('earnest-id');
            let status = '';
            checkbox.closest('.check-row').removeClass('cleared bounced');
            if(checkbox.is(':checked')) {
                status = checkbox.val();
                checkbox.closest('.check-row').addClass(status);
            }

            formData.append('check_id', check_id);
            formData.append('status', status);
            formData.append('check_type', check_type);

            axios.post('/agents/doc_management/transactions/clear_bounce_earnest_check', formData, axios_options)
            .then(function (response) {

                let formData = new FormData();
                formData.append('Earnest_ID', Earnest_ID);

                axios.post('/agents/doc_management/transactions/save_earnest_amounts', formData, axios_options)
                .then(function (response) {

                    toastr['success']('Check Status Updated');

                    setTimeout(function() {
                        let active_index = $('.earnest-totals-tab.active').index();
                        get_earnest_totals(active_index);
                    }, 100);

                })
                .catch(function (error) {

                });

            })
            .catch(function (error) {

            });

        }

        function search_earnest_checks(value) {

            if(value.length > 2) {

                axios.get('/doc_management/search_earnest_checks', {
                    params: {
                        value: value
                    },
                    headers: {
                        'Accept-Version': 1,
                        'Accept': 'text/html',
                        'Content-Type': 'text/html'
                    }
                })
                .then(function (response) {

                    $('#earnest_search_results').hide().html('');

                    if(response.data.match(/earnest-search-results/)) {
                        $('#earnest_search_results').show().html(response.data);
                        $(document).on('mouseup', function (e) {

                            let container = $('.earnest-search-div');
                            if (!container.is(e.target) && container.has(e.target).length === 0) {
                                $('#earnest_search_results').hide().html('');
                                $('#earnest_search_input').val('');
                            }

                        });
                    }

                })
                .catch(function (error) {
                    console.log(error);
                });

            } else {
                // hide results
                $('#earnest_search_results').hide().html('');
            }

        }

    });

}
