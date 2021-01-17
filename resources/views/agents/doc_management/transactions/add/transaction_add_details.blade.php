@extends('layouts.main')
@section('title', 'Add Transaction Details')

@section('content')

<div class="container page-container page-add-transaction-details">
    <div class="row">
        <div class="col-12">

            @php

            // set values
            $transaction_type = strtolower($property_details -> transaction_type);
            $property_type_val = $property_details -> PropertyType ?? null;
            $forsale = '';
            if($property_type_val) {
                if(stristr($property_type_val, 'lease')) {
                    $forsale = 'no';
                    $property_type_val = str_replace(' Lease', '', $property_type_val);
                } else {
                    $forsale = 'yes';
                }
            }

            $sale_type = $property_details -> SaleType ?? null;
            if($sale_type) {
                $end = strpos($sale_type, ',');
                if(!$end) {
                    $end = strlen($sale_type);
                }
                $sale_type = trim(substr($sale_type, 0, $end));

                if(preg_match('/(hud|reo)/i', $sale_type)) {
                    $sale_type = 'REO/Bank/HUD Owned';
                } else if(preg_match('/foreclosure/i', $sale_type)) {
                    $sale_type = 'Foreclosure';
                } else if(preg_match('/auction/i', $sale_type)) {
                    $sale_type = 'Auction';
                } else if(preg_match('/(short|third)/i', $sale_type)) {
                    $sale_type = 'Short Sale';
                } else if(preg_match('/standard/i', $sale_type)) {
                    $sale_type = 'Standard';
                } else {
                    $sale_type = '';
                }
                // if no results check new construction
                if($sale_type == '') {
                    if($property_details -> NewConstructionYN == 'Y') {
                        $sale_type = 'New Construction';
                    }
                }
            }

            $hoa_condo = 'none';
            $condo = $property_details -> CondoYN ?? null;
            if($condo && $condo == 'Y') {
                $hoa_condo = 'condo';
            }
            $hoa = $property_details -> AssociationYN ?? null;
            if($hoa && $hoa == 'Y') {
                if($property_details -> AssociationFee > 0) {
                    $hoa_condo = 'hoa';
                }
            }

            $YearBuilt = $property_details -> YearBuilt ?? null;

            @endphp

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $property_details -> FullStreetAddress }} {{ $property_details -> City.', '.$property_details -> StateOrProvince.' '.$property_details -> PostalCode }}</div>

            <div class="container-1000 mx-auto mt-3 mt-md-5">

                <div class="row">

                    <div class="col-12">

                        <div class="h4 text-primary mb-1 mt-3 mt-md-5 text-center">Please Enter and Verify the following details</div>

                            <form id="details_form">

                                <div class="row">

                                    <div class="col-12 col-md-6 col-lg-4 mt-5">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel show-hide required" name="listing_type" id="listing_type" data-label="Transaction Type">
                                            <option value=""></option>
                                            <option value="sale" @if($forsale == 'yes') selected @endif>Sale</option>
                                            <option value="rental" @if($forsale == 'no') selected @endif>Rental</option>
                                            @if($transaction_type == 'listing')
                                                <option value="both">Both</option>
                                            @endif

                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mt-5">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel show-hide required" name="property_type" id="property_type" data-label="Property Type">
                                            <option value=""></option>
                                            @foreach($property_types as $property_type)
                                                <option value="{{ $property_type -> resource_name }}" @if($property_type -> resource_name == $property_type_val) selected @endif>{{ $property_type -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mt-5 property-sub-type">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel show-hide required" name="property_sub_type" id="property_sub_type" data-label="Sale Type">
                                            <option value=""></option>
                                            @foreach($property_sub_types as $property_sub_type)
                                                @if($transaction_type == 'contract' || ($transaction_type == 'listing' && $property_sub_type -> resource_name != 'For Sale By Owner'))
                                                    <option value="{{ $property_sub_type -> resource_name }}" @if($property_sub_type -> resource_name == $sale_type) selected @endif>{{ $property_sub_type -> resource_name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-12 col-md-6 col-lg-4 mt-5 year-built">
                                        <input type="number" maxlength="4" min="1600" max="{{ date('Y') + 1 }}" class="custom-form-element form-input numbers-only required" name="year_built" id="year_built" value="{{ $YearBuilt }}" data-label="Year Built">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mt-5">
                                        @if($transaction_type == 'listing')
                                            <input type="text" class="custom-form-element form-input numbers-only required" name="list_price" id="list_price" value="{{ $property_details -> ListPrice ?? null }}" data-label="List Price">
                                        @else
                                            <input type="text" class="custom-form-element form-input numbers-only required" name="contract_price" id="contract_price" data-contract-price="{{ $property_details -> ContractPrice ?? null }}" data-close-price="{{ $property_details -> ClosePrice ?? null }}" data-label="Contract Price">
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 mt-5 hoa">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="hoa_condo" id="hoa_condo" data-label="HOA/Condo Fees">
                                            <option value="hoa" @if($hoa_condo == 'hoa') selected @endif>HOA Fees</option>
                                            <option value="condo" @if($hoa_condo == 'condo') selected @endif>Condo Fees</option>
                                            <option value="none" @if($hoa_condo == 'none') selected @endif>None</option>

                                        </select>
                                    </div>

                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-center w-100">
                                            <button id="submit_details_form_button" class="waves-effect waves-light btn btn-lg btn-primary">Continue <i class="fal fa-arrow-circle-right ml-3"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="transaction_type" id="transaction_type" value="{{ strtolower(str_replace('/Lease', '', $transaction_type)) }}">
                                <input type="hidden" name="Agent_ID" id="Agent_ID" value="{{ $Agent_ID }}">

                            </form>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection
