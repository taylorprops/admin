@extends('layouts.main')
@section('title', 'Add Listing')

@section('content')
<div class="container page-add-listing">
    <div class="row">

        <div class="col-12">

            <div class="row mb-5">
                <div class="col-12">
                    <div class="h1-responsive text-primary mt-4 mb-2">Add Listing</div>
                </div>
            </div>

            <div id="address_container" class="property-container collapse show mx-auto">
                <!-- address search container -->
                <div class="d-flex justify-content-center w-100">
                    <div id="address_search_container" class="address-container mls-container collapse show">
                        <div class="h3-responsive text-center text-orange mb-4">To Begin, Search By Property Address</div>
                        <div class="mt-5">
                            <div class="row">
                                <div class="col-sm-9 col-lg-10">
                                    <div class="h5-responsive text-gray">
                                        Enter Property Street Address
                                        <span class="text-orange font-normal">
                                            <a href=".mls-container" class="text-orange font-9" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".mls-container"> <i class="fad fa-arrows-alt-h mx-3"></i> or Use MLS ID Search</a>
                                        </span>
                                    </div>
                                    <input type="text" class="w-100" id="address_search_street">
                                </div>
                                <div class="col-sm-3 col-lg-2">
                                    <div class="h5-responsive text-gray">Unit</div>
                                    <input type="text" class="w-100" id="address_search_unit">
                                </div>
                            </div>
                        </div>
                        <div class="address-search-error hidden">
                            <div class="alert alert-danger text-danger w-50 my-3 mx-auto text-center" role="alert">
                                <i class="fad fa-exclamation-circle fa-lg mr-3"></i> Street Number not valid. Please enter the address manually
                            </div>
                        </div>
                        <div class="address-search-continue-div text-center my-4 hidden">
                            <a href=".property-container" class="btn btn-success btn-lg" id="address_search_continue" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".property-container">Continue <i class="fad fa-chevron-double-right ml-3"></i></a>
                        </div>
                        <div class="h5-responsive text-center mt-4">
                            <a href=".address-container" id="enter_manually_button" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Or Enter Manually</a>
                        </div>
                    </div>
                </div>
                <!-- end address search container -->

                <!-- address enter container -->
                <div id="address_enter_container" class="address-container collapse">
                    <div class="h3-responsive text-center text-orange mb-4">To Begin, Enter The Street Address</div>
                    <form id="enter_address_form">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-6 col-lg-2">
                                    <input type="text" id="enter_street_number" class="custom-form-element form-input required" data-label="Street Number">
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <select id="enter_street_dir" class="custom-form-element form-select form-select-no-search" data-label="Dir">
                                        <option value=""></option>
                                        <option value="N">N</option>
                                        <option value="S">S</option>
                                        <option value="E">E</option>
                                        <option value="W">W</option>
                                        <option value="NE">NE</option>
                                        <option value="SE">SE</option>
                                        <option value="NW">NW</option>
                                        <option value="SW">SW</option>
                                    </select>
                                </div>
                                <div class="col-sm-9 col-lg-6">
                                    <input type="text" id="enter_street_name" class="custom-form-element form-input required" data-label="Street Name">
                                </div>
                                <div class="col-sm-3 col-lg-2">
                                    <input type="text" id="enter_unit" class="custom-form-element form-input" data-label="Unit">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-lg-2">
                                    <input type="text" id="enter_zip" class="custom-form-element form-input numbers-only required" maxlength="5" data-label="Zip">
                                </div>
                                <div class="col-sm-9 col-lg-4">
                                    <input type="text" id="enter_city" class="custom-form-element form-input required" data-label="City">
                                </div>
                                <div class="col-sm-4 col-lg-2">
                                    <select id="enter_state" class="custom-form-element form-select form-select-no-search form-select-no-cancel required" data-label="Select State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            @if($state != 'All')
                                            <option value="{{ $state }}">{{ $state }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8 col-lg-4">
                                    <select id="enter_county" class="custom-form-element form-select form-select-no-cancel required" data-label="Select County" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="address-enter-continue-div text-center my-4">
                            <button id="address_enter_continue" class="btn btn-success btn-lg" type="button" data-toggle="collapse" data-target=".property-container" aria-expanded="false" aria-controls="address_search_container address_enter_container" disabled>
                                Continue <i class="fad fa-chevron-double-right ml-3"></i>
                            </button>
                        </div>
                    </form>
                    <div class="h5-responsive text-center mt-4">
                        <a href=".address-container" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Go Back To Address Search</a>
                    </div>
                </div>
                <!-- end address enter container -->

                <!-- mls search container -->
                <div id="mls_search_container" class="mls-container mx-auto collapse">
                    <div class="h3-responsive text-center text-orange mb-4">Search By Bright MLS ID</div>
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="h5-responsive text-gray">
                                    Enter MLS ID
                                    <span class="text-orange font-normal">
                                        <a href=".mls-container" class="text-orange font-9" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".mls-container"> <i class="fad fa-arrows-alt-h mx-3"></i> or Use Address Search</a>
                                    </span>
                                </div>
                                <input type="text" class="w-100" id="mls_search">
                            </div>
                        </div>
                    </div>
                    <div class="mls-search-error hidden">
                        <div class="alert alert-danger text-danger w-50 my-3 mx-auto text-center" role="alert">
                            <i class="fad fa-exclamation-circle fa-lg mr-3"></i> No Matching Results Found
                        </div>
                    </div>
                    <div class="mls-search-continue-div text-center my-4">
                        <a href=".property-container" class="btn btn-success btn-lg" id="mls_search_continue" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".property-container">Continue <i class="fad fa-chevron-double-right ml-3"></i></a>
                    </div>
                </div>
                <!-- end mls search container -->

            </div>

            <div id="mls_match_container" class="property-container collapse mx-auto">

                <a class="btn-floating btn-primary" data-toggle="collapse" href=".property-container" role="button" aria-expanded="false" aria-controls="#mls_match_container #address_container"><i class="fad fa-chevron-double-left"></i></a>

                <div class="property-loading-div"></div>

                <div class="property-results-container hidden">

                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="h2-responsive text-gray mb-3 text-center">We found the following matching property</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="border border-gray text-gray p-3 z-depth-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-center">
                                                <img class="image-fluid property-results-image" src="" id="property_details_photo">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="h4-responsive mt-3 mt-md-0" id="property_details_address"></div>
                                            <div class="row pt-2 mt-2 border-top property-details">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            List Date: <span id="property_details_list_date"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            List Price: <span id="property_details_list_price"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            Listing Agent: <span id="property_details_listing_agent"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            Listing Office: <span id="property_details_listing_office"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            Status: <span id="property_details_status"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            Mls Id: <span id="property_details_mls_id"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hidden">
                                                            Property Type: <span id="property_details_property_type"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            Year Built: <span id="property_details_year_built"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6 beds-baths-div hidden">
                                                            Beds: <span id="property_details_beds"></span>
                                                        </div>
                                                        <div class="col-sm-6 beds-baths-div hidden">
                                                            Baths: <span id="property_details_baths"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 owner-div hidden">
                                                            Owner 1: <span id="property_details_owner1"></span>
                                                        </div>
                                                        <div class="col-sm-6 owner-div hidden">
                                                            Owner 2: <span id="property_details_owner2"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mt-4 text-center w-100">
                                    <a type="button" class="btn btn-lg btn-success" id="found_property_submit_button">This is my Listing <i class="fad fa-check ml-3 fa-lg"></i></a>
                                    <br>
                                    <a id="not_my_listing_button" class="btn btn-sm btn-danger mt-5" data-toggle="collapse" href=".property-container" role="button" aria-expanded="false" aria-controls="#mls_match_container #address_container">This is not my listing</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="multiple_results_modal" tabindex="-1" role="dialog" aria-labelledby="multiple_results_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary draggable-handle">
                <h4 class="modal-title" id="multiple_results_title">Multiple Results Found</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center p-5">
                    The address you entered has multiple units. Please enter the unit number or enter the address manually.
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <a class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Close</a>
            </div>
        </div>
    </div>
</div>


@endsection
