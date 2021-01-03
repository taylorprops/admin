if (document.URL.match(/transaction_details/)) {

    window.save_details = function() {

        if($('#MLSListDate').val() > $('#ExpirationDate').val()) {
            $('#modal_danger').modal().find('.modal-body').html('Expiration Date Must Be After List Date');
            return false;
        }
        let form = $('#transaction_details_form');
        let validate = validate_form(form);
        if(validate == 'yes') {

            let Listing_ID = $('#Listing_ID').val();
            let Contract_ID = $('#Contract_ID').val();
            let Referral_ID = $('#Referral_ID').val();
            let transaction_type = $('#transaction_type').val();

            let formData = new FormData(form[0]);
            formData.append('Listing_ID', Listing_ID);
            formData.append('Contract_ID', Contract_ID);
            formData.append('Referral_ID', Referral_ID);
            formData.append('transaction_type', transaction_type);

            axios.post('/agents/doc_management/transactions/save_details', formData, axios_options)
            .then(function (response) {
                if(response.data.success == 'ok') {
                    //load_tabs('details');
                    load_tabs('commission');
                    load_details_header();
                    toastr['success']('Transaction Details Saved!');
                }
            })
            .catch(function (error) {

            });
        }
    }

    window.save_search_mls = function() {

        $('#confirm_import_modal').modal('hide');

        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        let Contract_ID = $('#Contract_ID').val();
        let transaction_type = $('#transaction_type').val();

        axios.get('/agents/doc_management/transactions/save_mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID,
                Contract_ID: Contract_ID,
                transaction_type: transaction_type
            }
        })
        .then(function (response) {

            if(response.data.status == 'ok') {


                load_tabs('details');
                load_tabs('checklist');
                if(transaction_type == 'listing') {
                    load_details_header();
                } else {
                    load_details_header();
                }

                setTimeout(function() {
                    $('#modal_success').modal().find('.modal-body').html('BrightMLS Data Successfully Imported');
                }, 1000);

            }
        })
        .catch(function (error) {

        });
    }

    window.search_mls = function() {
        let ListingId = $('#ListingId').val();
        let Listing_ID = $('#Listing_ID').val();
        axios.get('/agents/doc_management/transactions/mls_search', {
            params: {
                ListingId: ListingId,
                Listing_ID: Listing_ID
            }
        })
        .then(function (response) {

            if(response.data.status == 'ok') {

                search_response = 'We have located the property in BrightMLS!<br><br><i class="fad fa-exclamation-triangle mr-2"></i> The checklist will be replaced to include the forms required for the new listing. Any relevant forms will be kept in the checklist but some may need to be added or replaced<hr>';

                let listing_details = ' \
                    <div class="row"> \
                        <div class="col-12 col-md-5"> \
                            <img src="'+response.data.picture_url+'" class="confirm-import-image"> \
                        </div> \
                        <div class="col-12 col-md-7"> \
                            Listed By '+response.data.list_company+'<br> \
                            '+response.data.address+'<br> \
                            '+response.data.city+', '+response.data.state+' '+response.data.zip+'<br> \
                        </div> \
                    </div> \
                ';

                $('#confirm_import_modal').modal().find('.modal-body').html(search_response + listing_details);

                $('#confirm_import_button').off('click').on('click', save_search_mls);

            } else if(response.data.status == 'not found') {

                $('#modal_danger').modal().find('.modal-body').html('<div class="h4 text-danger w-100 text-center"><i class="fad fa-exclamation-triangle mr-2"></i> Listing not found in BrightMLS</div>');

            }
        })
        .catch(function (error) {

        });
    }

    window.update_county_select = function() {
        let state = $('#StateOrProvince').val();
        axios.get('/agents/doc_management/transactions/update_county_select', {
            params: {
                state: state
            },
        })
            .then(function (response) {
                let counties = response.data;
                $('#County').html('').prop('disabled', false);
                $('#County').append('<option value=""></option>');
                $.each(counties, function (k, v) {
                    $('#County').append('<option value="' + v.county.toUpperCase() + '">' + v.county + '</option>');
                });
                setTimeout(function () {
                    if (state == 'DC') {
                        $('#County').val('DISTRICT OF COLUMBIA');
                    }
                }, 100);
            })
            .catch(function (error) {

            });
    }

}
