if(document.URL.match(/balance_earnest/)) {

    $(function() {

        get_earnest_totals();
        get_earnest_checks();

        $(document).on('click', '.refresh-accounts-button', function() {
            refresh();
        });

        $('#earnest_search_input').on('keyup', function() {
            search_earnest_checks($(this).val());
        });

        let options = {
            menubar: false,
            statusbar: false,
            toolbar: true,
            selector: '#bounced_check_message',
            height: 450,
            relative_urls : false,
            remove_script_host : true,
            document_base_url: location.hostname
        }
        text_editor(options);

        //////////// functions ////////////

        function refresh() {

            let active_index = $('.earnest-totals-tab.active').index();
            $('.earnest-checks-container').hide();
            $('.earnest-totals-container').html('<div class="list-group-item p-5 text-center text-gray"><i class="fas fa-spinner fa-pulse fa-2x"></i></div>');
            get_earnest_totals(active_index);
            get_earnest_checks();
            $('.earnest-checks-container').show();

        }

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
                    $('.earnest-totals-tab').eq(active_index).trigger('click');
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
                data_table('10', $('.earnest-checks-table-in'), [1, 'desc'], [0, 7, 8], [], true, true, true, false);
                data_table('10', $('.earnest-checks-table-out'), [1, 'desc'], [0, 7], [], true, true, true, false);
                data_table('10', $('.earnest-checks-table-in-recent'), [1, 'desc'], [0, 7, 8], [], true, true, true, true);
                data_table('10', $('.earnest-checks-table-out-recent'), [1, 'desc'], [0, 7], [], true, true, true, true);
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

            $('.earnest-totals-tab.active').find('.account-total-amount').html('<i class="fas fa-spinner fa-pulse"></i>');

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

                if(status == 'bounced') {

                    let check = response.data.check;

                    let property = check.property;
                    let agent = check.agent;
                    let agent_email = agent.email;
                    let link = response.data.link;

                    let buyers = property.BuyerOneFirstName+' '+property.BuyerOneLastName;
                    if(property.BuyerTwoFirstName != '') {
                        buyers += 'and '+property.BuyerTwoFirstName+' '+property.BuyerTwoFirstName;
                    }

                    let property_address = property.FullStreetAddress+' '+property.City+', '+property.StateOrProvince+' '+property.PostalCode;

                    let Contract_ID = property.Contract_ID;

                    let message = ' \
                    Hello '+agent.first_name+',<br><br> \
                        The earnest deposit for the following property has bounced.<br><br> \
                        '+property_address+'<br><br> \
                        Buyers: '+buyers+'<br> \
                        Check Amount: '+global_format_number_with_decimals(check.check_amount)+'<br><br> \
                        <span style="color: #900">Please call the office immediately to resolve this issue.</span><br><br> \
                        <a href="'+link+'" target="_blank">View Transaction</a> \
                    ';
                    $('#bounced_check_notification_modal').modal('show');

                    let signature = $('#bounced_check_message').val();

                    tinymce.get('bounced_check_message').setContent(message+signature);

                    $('#send_bounced_check_notification_button').on('click', function() {
                        $(this).html('<span class="spinner-border spinner-border-sm mr-2"></span> Sending Email...');

                        let bounced_check_message = tinymce.get('bounced_check_message').getContent();

                        let formData = new FormData();
                        formData.append('agent_email', agent_email);
                        formData.append('bounced_check_message', bounced_check_message);
                        formData.append('property_address', property_address);
                        formData.append('Contract_ID', Contract_ID);
                        axios.post('/agents/doc_management/transactions/notify_agent_bounced_earnest', formData, axios_options)
                        .then(function (response) {
                            $('#bounced_check_notification_modal').modal('hide');
                            toastr['success']('Agent Successfully Notified');
                            $('#send_bounced_check_notification_button').html('<i class="fad fa-share mr-2"></i> Send Message');
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                    });

                }


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
