if (document.URL.match(/transaction_add_details_/)) {

    $(function() {

        // format list and contract price
        if($('#list_price').length > 0) {
            format_money($('#list_price'));
            $('#list_price').on('keyup', function() {
                format_money($(this));
            });
        } else {
            format_money($('#contract_price'));
            $('#contract_price').on('keyup', function() {
                format_money($(this));
            });
        }


        if($('#contract_price').val() == '$0') {
            $('#contract_price').val('');
        }


        hide_property_sub_type();
        hide_hoa();
        hide_year_built();

        show_hide();
        $('.show-hide').on('change', show_hide);

        //form_elements();

        $('#submit_details_form_button').off('click').on('click', function(e) {
            e.preventDefault();
            save_add_transaction();
        });


    });



    function save_add_transaction() {

        let form = $('#details_form');

        $('#list_price, #contract_price').each(function() {
            if($(this).val() == '$0') {
                $(this).val('');
            }
        });

        let validate = validate_form(form);

        if(validate == 'yes') {
            let transaction_type = $('#transaction_type').val();
            let formData = new FormData(form[0]);

            axios.post('/agents/doc_management/transactions/save_add_transaction', formData, axios_options)
            .then(function (response) {
                window.location = '/agents/doc_management/transactions/add/transaction_required_details/'+response.data.id+'/'+transaction_type;
            })
            .catch(function (error) {

            });
        }

    }

    function show_hide() {
        let listing_type = $('#listing_type').val();
        let property_type = $('#property_type').val();
        let property_sub_type = $('#property_sub_type').val();
        let contract_price = $('#contract_price').data('contract-price');
        let close_price = $('#contract_price').data('close-price');

        if(listing_type == 'sale' || listing_type == 'both') {

            show_all();

            if(property_type != 'Residential' && property_type != 'Multi-Family') {
                hide_year_built();
                hide_hoa();
            }

            if(property_sub_type != 'Standard' && property_sub_type != 'Short Sale' && property_sub_type != 'For Sale By Owner') {
                hide_year_built();
                hide_hoa();
            }

            $('#contract_price').data('label', 'Contract Price').val(contract_price);
            $('label[for="contract_price"]').text('Contract Price');
            $('label[for="list_price"]').text('List Price');

        } else if(listing_type == 'rental') {

            hide_all();

            $('.property-sub-type').hide().find('#property_sub_type').val('');
            $('#contract_price').data('label', 'Monthly Lease Amount').val(close_price);
            $('label[for="contract_price"], label[for="list_price"]').text('Monthly Lease Amount');

        }

        setTimeout(function() {
            //select_refresh();
        }, 100);

    }

    function show_property_sub_type() {
        $('.property-sub-type').show().find('.custom-form-element').addClass('required');
    }
    function show_hoa() {
        $('.hoa').show().find('.custom-form-element').addClass('required');
    }
    function show_year_built() {
        $('.year-built').show().find('.custom-form-element').addClass('required');
    }
    function show_all() {
        show_property_sub_type();
        show_hoa();
        show_year_built();
    }

    function hide_property_sub_type() {
        $('.property-sub-type').hide().find('.custom-form-element').removeClass('required');
    }
    function hide_hoa() {
        $('.hoa').hide().find('.custom-form-element').removeClass('required');
    }
    function hide_year_built() {
        $('.year-built').hide().find('.custom-form-element').removeClass('required');
    }
    function hide_all() {
        hide_property_sub_type();
        hide_hoa();
        hide_year_built();
    }


}
