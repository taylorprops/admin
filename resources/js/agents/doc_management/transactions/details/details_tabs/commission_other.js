if (document.URL.match(/commission_other/)) {

    $(function() {

        get_commission_other();

        function get_commission_other() {

            let Commission_ID = $('#Commission_Other_ID').val();

            axios.get('/doc_management/commission_other/commission_other_details/'+Commission_ID, {
                headers: {
                    'Accept-Version': 1,
                    'Accept': 'text/html',
                    'Content-Type': 'text/html'
                }
            })
            .then(function (response) {
                //console.log(response.data);
                $('#commission_other_div').html(response.data);

                $('.popout').eq(0).show();
                get_checks_in(Commission_ID);
                get_checks_out(Commission_ID);
                get_commission_notes(Commission_ID);
                get_income_deductions(Commission_ID);
                get_commission_deductions(Commission_ID);
                get_agent_details($('#Agent_ID').val());

                $('#Agent_ID').on('change', function () {
                    get_agent_details($(this).val());
                });

                save_commission('no');

                $('.add-check-in-form-div').show();

                $('.add-check-in-button').off('click').on('click', show_add_check_in);
                $('.add-check-out-button').off('click').on('click', show_add_check_out);

                $('#save_add_check_in_button').off('click').on('click', save_add_check_in);
                $('#save_add_check_out_button').off('click').on('click', save_add_check_out);

                $('#save_add_income_deduction_button').off('click').on('click', function() {
                    save_add_income_deduction();
                });

                $('#add_income_deduction_div').on('hidden.bs.collapse', function () {
                    $('#income_deduction_description, #income_deduction_amount').val('');
                });

                $('#save_add_commission_deduction_button').off('click').on('click', function() {
                    save_add_commission_deduction();
                });

                $('#add_commission_deduction_div').on('hidden.bs.collapse', function () {
                    $('#commission_deduction_description, #commission_deduction_amount').val('');
                });

                $('.save-commission-notes-button').off('click').on('click', add_commission_notes)

                $('.total').each(function() {
                    if($(this).val() == '') {
                        $(this).val('0');
                    }
                });

                $('.show-view-add-button').on('click', popout_row);

                global_format_money();
                form_elements();
                show_fields();

            })
            .catch(function (error) {

            });

        }

    });

}
