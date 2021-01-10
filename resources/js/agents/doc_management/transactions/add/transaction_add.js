

if (document.URL.match(/transactions\/add\/(contract|listing|referral)/)) {

    $(function() {

        //form_elements();

        // search address in google
        search_address_continue();

        $('#enter_state').on('change', function () {
            update_county_select($(this).val());
        });

        $('#enter_zip').on('keyup', function() {
            fill_location($(this).val());
        });

        $('.required').on('change keyup', function () {
            setTimeout(function () {
                enter_address_form_check();
            }, 100);
        });

        $('#mls_search_continue').off('click').on('click', function() {
            search_mls_continue();
        });

        $('#address_enter_continue').off('click').on('click', function () {
            enter_address_continue();
        });

        if($('#Agent_ID').val() == '' || $('#Agent_ID').val() == '0') {
            show_add_agent();
        }
        $('#add_agent_id_modal').on('hidden.bs.modal', function () {
            if($('#Agent_ID').val() == '' || $('#Agent_ID').val() == '0') {
                show_add_agent();
            }
        });



    });

    function show_add_agent() {
        $('#add_agent_id_modal').modal('show');

        $(document).on('keyup', function (e) {
            if(e.key == 'Enter') {
                $('#save_add_agent_id_button').trigger('click');
            }
        });

        $('#save_add_agent_id_button').off('click').on('click', function() {
            $('#Agent_ID').val($('#add_agent_id').val());
            $('#add_agent_id_modal').modal('hide');
            toastr['success']('Agent Successfully Added')
        });
        setTimeout(function() {
            $('.form-select-label').trigger('click');
        }, 500);
    }

    function create_transaction() {

        let transaction_type = $('#transaction_type').val();
        let street_number = $('#enter_street_number').val();
        let street_name = $('#enter_street_name').val();
        let street_dir = $('#enter_street_dir').val();
        let unit_number = $('#enter_unit').val();
        let city = $('#enter_city').val();
        let state = $('#enter_state').val();
        let zip = $('#enter_zip').val();
        let county = $('#enter_county').val();
        let Agent_ID = $('#Agent_ID').val();
        let params = encodeURI(Agent_ID + '/' + transaction_type + '/' +street_number + '/' + street_name + '/' + city + '/' + state + '/' + zip + '/' + county + '/' + street_dir + '/' + unit_number);
        window.location.href = '/agents/doc_management/transactions/add/transaction_add_details_new/' + params;
    }

    function found_transaction(bright_type, bright_id, tax_id, state) {
        let Agent_ID = $('#Agent_ID').val();
        if(tax_id == '' || tax_id == 'undefined') {
            tax_id = 0;
        }
        let transaction_type = $('#transaction_type').val();
        let params = encodeURI(Agent_ID + '/' + transaction_type + '/' + state + '/' + tax_id + '/' + bright_type + '/' + bright_id);
        global_loading_off();
        window.location.href = '/agents/doc_management/transactions/add/transaction_add_details_existing/' + params;
    }

    function show_property(response, type, street_number, street_name, zip) {

        // results_bright_type = db_active|db_closed|bright, results_bright_id, results_tax_id
        // if multiple - require unit number be entered and search again
        if(response.data != '' && response.data.multiple == true) {

            $('.property-loading-div').hide();
            $('#mls_match_container').collapse('hide');
            $('#address_container').collapse('show');
            $('#address_search_container').collapse('show');
            $('#address_enter_container').collapse('hide');
            // show multiple options to choose from
            $('#multiple_results_modal').modal();

        // single result returned
        } else if(response.data != '' && response.data.multiple == false) {
            // clear fields that may not contain data
            $('#property_details_owner1, #property_details_owner2, #property_details_beds, #property_details_baths').text('');
            $('.owner-div, .beds-baths-div, .active-listing-div').hide();

            // show always
            let ListPictureURL = response.data.ListPictureURL;
            if(ListPictureURL == undefined || ListPictureURL == '') {
                ListPictureURL = '/images/agents/doc_management/add_transaction/house.png';
            }
            let FullStreetAddress = response.data.FullStreetAddress;
            let StreetNumber = response.data.StreetNumber;
            let StreetName = response.data.StreetName;
            let City = response.data.City;
            let StateOrProvince = response.data.StateOrProvince;
            let County = '';
            if(response.data.County) {
                County = response.data.County;
            }
            if(StateOrProvince == 'MD' && County != '') {
                County += ' County';
            }
            let PostalCode = response.data.PostalCode;
            let YearBuilt = response.data.YearBuilt;
            let Owner1 = response.data.Owner1;
            let Owner2 = response.data.Owner2;
            let BathroomsTotalInteger = response.data.BathroomsTotalInteger;
            let BedroomsTotal = response.data.BedroomsTotal;


            $('#property_details_photo').prop('src', ListPictureURL);
            $('#property_details_address').html(FullStreetAddress+'<br>'+City+', '+StateOrProvince+' '+PostalCode+'<br><span class="h5 mt-1 text-secondary">'+County.toUpperCase()+'</span>');
            $('#property_details_year_built').text(YearBuilt);
            if(Owner1) {
                $('.owner-div').show();
                $('#property_details_owner1').text(Owner1);
                $('#property_details_owner2').text(Owner2);
            }
            if(BedroomsTotal) {
                $('.beds-baths').show();
                $('#property_details_beds').text(BedroomsTotal);
                $('#property_details_baths').text(BathroomsTotalInteger);
            }


            // show only if active in mls
            let MlsStatus = response.data.MlsStatus;
            let ListingId = response.data.ListingId;
            let CloseDate = response.data.CloseDate;
            let ListPrice = response.data.ListPrice;
            let PropertyType = response.data.PropertyType;
            let ListOfficeName = response.data.ListOfficeName;
            let MLSListDate = response.data.MLSListDate;
            let ListAgentFirstName = response.data.ListAgentFirstName;
            let ListAgentLastName = response.data.ListAgentLastName;

            // show certain details only if active listing in mls
            let Today = new Date();
            CloseDate = new Date(CloseDate);
            // get all active and include listings that have closed in the past 180 days
            if(
                (response.data.results_bright_type == 'db_active')
                || (
                    (/* response.data.results_bright_type == 'db_closed' ||  */response.data.results_bright_type == 'bright')
                    && MlsStatus.match(/(CLOSED)/) && global_date_diff(CloseDate, Today) < 180
                )
            ) {
                $('.active-listing-div').show();
                $('#property_details_status').text(MlsStatus);
                $('#property_details_mls_id').text(ListingId);
                if(ListPrice) {
                    $('#property_details_list_price').text('$'+global_format_number(ListPrice));
                }
                $('#property_details_property_type').text(PropertyType);
                $('#property_details_listing_office').text(ListOfficeName);
                $('#property_details_list_date').text(MLSListDate);
                $('#property_details_listing_agent').text(ListAgentFirstName + ' ' + ListAgentLastName);
            }

            $('.property-loading-div').hide();
            $('.property-results-container').fadeIn();

            $('#found_property_submit_button').off('click').on('click', function() {
                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2"></span> Getting Details');
                // send directly to add details page
                let results_bright_type = results_bright_id = results_tax_id = '';

                if(response.data.results_bright_type) {
                    results_bright_type = response.data.results_bright_type;
                }
                if(response.data.results_bright_id) {
                    results_bright_id = response.data.results_bright_id;
                }
                if(response.data.results_tax_id) {
                    results_tax_id = response.data.results_tax_id;
                }

                found_transaction(results_bright_type, results_bright_id, results_tax_id, StateOrProvince);
                //results_bright_type = db_active|db_closed|bright
            });
            $('#not_my_listing_button').off('click').on('click', function() {
                $('#address_enter_continue').data('go-to-details', 'yes');
            });

        // no results
        } else {
            $('.property-loading-div').hide();

            if(type == 'search' || type == 'mls') {
                // redirect to manually enter data
                $('#modal_danger').modal().find('.modal-body').html('No matches found. Please enter the property information manually.');
                $('#address_container, #address_enter_container').collapse('show');
                $('#mls_match_container, #mls_search_container, #address_search_container').collapse('hide');

                autofill_manual_entry(street_number, street_name, zip);

                // next time address_enter_continue clicked go straight to enter details page.
                $('#address_enter_continue').data('go-to-details', 'yes');
            } else if(type == 'enter') {
                // if no results from manual entry go right to details page
                create_transaction();
            }
        }
    }

    function fill_location(zip) {
        if(zip.length == 5) {
            axios.get('/agents/doc_management/global_functions/get_location_details', {
                params: {
                    zip: zip
                },
            })
            .then(function (response) {
                let data = response.data;
                $('#enter_city').val(data.city);
                $('#enter_state').val(data.state);
                update_county_select(data.state);
                setTimeout(function() {
                    $('#enter_county').val(data.county);
                    //select_refresh();
                    enter_address_form_check();
                }, 500);
            })
            .catch(function (error) {

            });
        }
    }

    function show_loader() {
        // clear current data and show loader
        $('.property-loading-div').fadeIn();
        global_loading_on('.property-loading-div', '<div class="h3 text-primary"><i class="fad fa-home-lg-alt mr-3"></i> Searching Properties</div>');
        $('.property-results-container').hide();
        $('#property_details_photo').prop('src', '');
        $('#property_details_address').text('');
    }

    function search_mls_continue() {
        show_loader();
        let mls = $('#mls_search').val();
        axios.get('/agents/doc_management/transactions/get_property_info', {
            params: {
                mls: mls,
            }
        })
        .then(function (response) {
            show_property(response, 'mls', '', '', '');
        })
        .catch(function (error) {

        });
    }

    function enter_address_form_check() {
        let form = $('#enter_address_form');
        let cont = 'yes';
        form.find('.required').each(function () {
            if ($(this).val() == '') {
                cont = 'no';
            }
        });
        if (cont == 'yes') {
            $('#address_enter_continue').prop('disabled', false);

        } else {
            $('#address_enter_continue').prop('disabled', true);
        }
    }

    function enter_address_continue() {

        let Agent_ID = $('#Agent_ID').val();
        let street_number = $('#enter_street_number').val();
        let street_name = $('#enter_street_name').val();
        let street_dir = $('#enter_street_dir').val();
        let unit_number = $('#enter_unit').val();
        let city = $('#enter_city').val();
        let state = $('#enter_state').val();
        let zip = $('#enter_zip').val();
        let county = $('#enter_county').val();

        let go_to_details = $('#address_enter_continue').data('go-to-details');

        if(go_to_details == 'yes') {

            // go to enter details page
            create_transaction();

        } else {

            if($('#transaction_type').val() == 'referral') {

                let formData = new FormData();
                formData.append('street_number', street_number);
                formData.append('street_name', street_name);
                formData.append('unit_number', unit_number);
                formData.append('city', city);
                formData.append('state', state);
                formData.append('zip', zip);
                formData.append('county', county);
                formData.append('Agent_ID', Agent_ID);
                axios.post('/agents/doc_management/transactions/add/transaction_add_details_referral', formData, axios_options)
                .then(function (response) {
                    let Referral_ID = response.data.Referral_ID;
                    window.location = '/agents/doc_management/transactions/add/transaction_required_details_referral/'+Referral_ID;
                })
                .catch(function (error) {

                });

            } else {

                // search mls and tax records
                show_loader();

                axios.get('/agents/doc_management/transactions/get_property_info', {
                    params: {
                        street_number: street_number,
                        street_name: street_name,
                        street_dir: street_dir,
                        unit: unit_number,
                        city: city,
                        state: state,
                        zip: zip,
                        county: county
                    }
                })
                .then(function (response) {
                    // using 'enter' so if no results from search will go directly to next step
                    show_property(response, 'enter', '', '', '');
                })
                .catch(function (error) {

                });

            }

        }
    }

    function search_address_continue() {

        // search input
        let address_search_street = document.getElementById('address_search_street');

        // select all text on focus
        $(address_search_street).on('focus', function () { $(this).select(); });
        // google address search
        let places = new google.maps.places.Autocomplete(address_search_street);
        google.maps.event.addListener(places, 'place_changed', function () {

            //$('#address_search_unit').val('');
            let address_details = places.getPlace();
            let street_number = street_name = city = county = state = zip = '';
            let county = '';
            address_details.address_components.forEach(function (address) {
                if (address.types.includes('street_number')) {
                    street_number = address.long_name;
                } else if (address.types.includes('route')) {
                    street_name = address.long_name;
                } else if (address.types.includes('locality')) {
                    city = address.long_name;
                } else if (address.types.includes('administrative_area_level_2')) {
                    county = address.long_name.replace(/'/, '');
                    county = county.replace(/\sCounty/, '');
                } else if (address.types.includes('administrative_area_level_1')) {
                    state = address.short_name;
                } else if (address.types.includes('postal_code')) {
                    zip = address.long_name;
                }
            });

            let unit_number = $('#address_search_unit').val();


            $('#enter_manually_button').off('click').on('click', function () {
                autofill_manual_entry(street_number, street_name, zip);
            });

            // show continue button once address selected
            $('.address-search-error').hide();
            if (street_number != '') {
                $('.address-search-continue-div').show();
            } else {
                $('.address-search-continue-div').hide();
                $('.address-search-error').show();
                let search_val = $(address_search_street).val().replace(/^[0-9]+\s/, '');
                $(address_search_street).val(search_val)
            }

            $('#address_search_street').on('keyup change', function () {
                if ($(this).val() == '') {
                    $('.address-search-continue-div').hide();
                }
            });
            // show results container or send to referral details page
            $('#address_search_continue').off('click').on('click', function () {

                let transaction_type = $('#transaction_type').val();

                if(transaction_type == 'referral') {

                    let Agent_ID = $('#Agent_ID').val();
                    let formData = new FormData();
                    formData.append('street_number', street_number);
                    formData.append('street_name', street_name);
                    formData.append('unit_number', unit_number);
                    formData.append('city', city);
                    formData.append('state', state);
                    formData.append('zip', zip);
                    formData.append('county', county);
                    formData.append('Agent_ID', Agent_ID);
                    axios.post('/agents/doc_management/transactions/add/transaction_add_details_referral', formData, axios_options)
                    .then(function (response) {
                        let Referral_ID = response.data.Referral_ID;
                        window.location = '/agents/doc_management/transactions/add/transaction_required_details_referral/'+Referral_ID;
                    })
                    .catch(function (error) {

                    });

                } else {

                    show_loader();
                    //$('.address-container').collapse('hide');

                    axios.get('/agents/doc_management/transactions/get_property_info', {
                        params: {
                            street_number: street_number,
                            street_name: street_name,
                            unit: unit_number,
                            city: city,
                            state: state,
                            zip: zip,
                            county: county
                        }
                    })
                    .then(function (response) {
                        show_property(response, 'search', street_number, street_name, zip);
                    })
                    .catch(function (error) {

                    });

                }

            });

        });

    }

    function update_county_select(state) {
        axios.get('/agents/doc_management/transactions/update_county_select', {
            params: {
                state: state
            },
        })
        .then(function (response) {
            let counties = response.data;
            $('#enter_county').html('').prop('disabled', false);
            $('#enter_county').append('<option value=""></option>');
            $.each(counties, function (k, v) {
                $('#enter_county').append('<option value="' + v.county.toUpperCase() + '">' + v.county + '</option>');
            });
            setTimeout(function () {
                //select_refresh();
            }, 500);
        })
        .catch(function (error) {

        });
    }

    function autofill_manual_entry(street_number, street_name, zip) {
        let unit = $('#address_search_unit').val();
        $('#enter_street_number').val(street_number);
        $('#enter_street_name').val(street_name);
        $('#enter_zip').val(zip);
        $('#enter_unit').val(unit);
        setTimeout(function() {
            fill_location($('#enter_zip').val());
        }, 500);
    }

}


