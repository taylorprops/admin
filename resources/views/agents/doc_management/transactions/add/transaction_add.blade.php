@extends('layouts.main')
@section('title', 'Add '.$transaction_type_header)

@section('content')
@php
$Agent_ID = null;
if(auth() -> user() -> group == 'agent') {
    $Agent_ID = auth() -> user() -> user_id;
}
@endphp
<input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
<input type="hidden" id="Agent_ID" value="{{ $Agent_ID }}">
<div class="container-1000 page-container page-add-transaction mx-auto p-3">
    <div class="row">

        <div class="col-12">

            <div class="row mb-3 mb-lg-5">
                <div class="col-12">
                    <div class="h1 text-primary mt-4">Add {{ $transaction_type_header }}</div>
                </div>
            </div>

            <div id="address_container" class="property-container collapse show mx-auto">
                <!-- address search container -->
                <div class="d-flex justify-content-center w-100">
                    <div id="address_search_container" class="address-container mls-container collapse show">
                        <div class="h3 text-center text-orange mb-4">Search By Address</div>

                        <div class="mt-5">
                            <div class="row">
                                <div class="col-sm-9 col-lg-10">
                                    <div class="font-10 text-gray">
                                        Enter Property Address
                                        @if($transaction_type != 'referral')
                                        <span class="text-orange font-normal">
                                            <a href=".mls-container" class="text-orange font-9" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".mls-container"> <i class="fad fa-arrows-alt-h mx-3"></i> or Use MLS ID Search</a>
                                        </span>
                                        @endif
                                    </div>
                                    <input type="text" class="w-100" id="address_search_street">
                                </div>
                                <div class="col-4 col-sm-3 col-lg-2">
                                    <div class="font-10 text-gray mt-4 mt-sm-0">Unit</div>
                                    <input type="text" class="w-100" id="address_search_unit">
                                </div>
                            </div>
                        </div>

                        <div class="address-search-error hide">
                            <div class="alert alert-danger text-danger w-50 my-3 mx-auto text-center" role="alert">
                                <i class="fad fa-exclamation-circle fa-lg mr-3"></i> Street Number not valid. Please enter the address manually
                            </div>
                        </div>
                        <div class="address-search-continue-div text-center my-4 hide">
                            <a class="btn btn-success btn-lg py-3 px-5" id="address_search_continue" @if($transaction_type != 'referral') href=".property-container" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".property-container" @else  href="javascript: void(0)" @endif>Continue <i class="fa fa-arrow-circle-right ml-3"></i></a>
                        </div>
                        <div class="h5 text-center mt-4">
                            <a href=".address-container" id="enter_manually_button" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Or Enter Manually</a>
                        </div>
                    </div>
                </div>
                <!-- end address search container -->

                <!-- address enter container -->
                <div id="address_enter_container" class="address-container collapse">
                    <div class="h3 text-center text-orange mb-4">To Begin, Enter The Address Details</div>
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
                            <button id="address_enter_continue" class="btn btn-success btn-lg py-3 px-5" type="button" @if($transaction_type != 'referral') data-toggle="collapse" data-target=".property-container" aria-expanded="false" aria-controls="address_search_container address_enter_container" @endif disabled>
                                Continue <i class="fa fa-arrow-circle-right ml-3"></i>
                            </button>
                        </div>
                    </form>
                    <div class="h5 text-center mt-4">
                        <a href=".address-container" class="btn btn-sm btn-secondary" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".address-container">Go Back To Address Search</a>
                    </div>
                </div>
                <!-- end address enter container -->

                <!-- mls search container -->
                <div id="mls_search_container" class="mls-container mx-auto collapse">
                    <div class="h3 text-center text-orange mb-4">Search By Bright MLS ID</div>
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-gray">
                                    Enter MLS ID
                                    <span class="text-orange font-normal">
                                        <a href=".mls-container" class="text-orange font-9" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".mls-container"> <i class="fad fa-arrows-alt-h mx-3"></i> or Use Address Search</a>
                                    </span>
                                </div>
                                <input type="text" class="w-100" id="mls_search">
                            </div>
                        </div>
                    </div>
                    <div class="mls-search-error hide">
                        <div class="alert alert-danger text-danger w-50 my-3 mx-auto text-center" role="alert">
                            <i class="fad fa-exclamation-circle fa-lg mr-3"></i> No Matching Results Found
                        </div>
                    </div>
                    <div class="mls-search-continue-div text-center my-4">
                        <a href=".property-container" class="btn btn-success btn-lg" id="mls_search_continue" data-toggle="collapse" role="button" aria-expanded="false" aria-controls=".property-container">Continue <i class="fa fa-arrow-circle-right ml-3"></i></a>
                    </div>
                </div>
                <!-- end mls search container -->

            </div>

            <div id="mls_match_container" class="property-container collapse mx-auto">

                <a class="btn btn-floating btn-primary" data-toggle="collapse" href=".property-container" role="button" aria-expanded="false" aria-controls="#mls_match_container #address_container"><i class="fad fa-chevron-double-left"></i></a>

                <div class="property-loading-div"></div>

                <div class="property-results-container hide">

                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="h2 text-gray mb-3 text-center">We found the following matching property</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="border border-gray text-gray p-3 shadow">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-center">
                                                <img class="image-fluid property-results-image" src="" id="property_details_photo">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="h5 mt-3 mt-md-0" id="property_details_address"></div>
                                            <div class="row pt-2 mt-2 border-top property-details">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            List Date: <span id="property_details_list_date"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            List Price: <span id="property_details_list_price"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            Listing Agent: <span id="property_details_listing_agent"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            Listing Office: <span id="property_details_listing_office"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            Status: <span id="property_details_status"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            Mls Id: <span id="property_details_mls_id"></span>
                                                        </div>
                                                        <div class="col-sm-6 active-listing-div hide">
                                                            Property Type: <span id="property_details_property_type"></span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            Year Built: <span id="property_details_year_built"></span>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6 beds-baths-div hide">
                                                            Beds: <span id="property_details_beds"></span>
                                                        </div>
                                                        <div class="col-sm-6 beds-baths-div hide">
                                                            Baths: <span id="property_details_baths"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6 owner-div hide">
                                                            Owner 1: <span id="property_details_owner1"></span>
                                                        </div>
                                                        <div class="col-sm-6 owner-div hide">
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
                                    <a type="button" class="btn btn-lg btn-success" id="found_property_submit_button">Yes, this is the property <i class="fad fa-check ml-3 fa-lg"></i></a>
                                    <br>
                                    <a id="not_my_listing_button" class="btn btn-sm btn-danger mt-5" data-toggle="collapse" href=".property-container" role="button" aria-expanded="false" aria-controls="#mls_match_container #address_container">No, this is not the property</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="add_agent_id_modal" tabindex="-1" role="dialog" aria-labelledby="add_agent_id_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_agent_id_modal_title">Select Agent</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="p-2">
                            <select class="custom-form-element form-select form-select-no-cancel" id="add_agent_id" data-label="Select Agent">
                                <option value=""></option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent -> id }}">{{ $agent -> first_name.' '.$agent -> last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-success btn-lg" id="save_add_agent_id_button">Continue <i class="fa fa-arrow-circle-right ml-3"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="multiple_results_modal" tabindex="-1" role="dialog" aria-labelledby="multiple_results_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="multiple_results_title">Multiple Results Found</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
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
