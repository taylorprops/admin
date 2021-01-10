@extends('layouts.main')
@section('title', 'Document Review')

@section('content')

<div class="container-fluid page-container page-document-review px-0">

    <div class="row no-gutters vh-100">

        <div class="col-3">

            <div class="transactions-container border-right h-100">

                <div class="properties-container w-100 pb-5">

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-start align-items-center w-100 pl-3">
                                <input type="text" class="custom-form-element form-input" id="search_properties" data-label="Search">
                                <a href="javascript: void(0)" class="btn btn-danger ml-2 mt-2" id="cancel_search_properties"><i class="fa fa-times mr-2"></i> Clear</a>
                            </div>
                        </div>
                    </div>

                    <div class="list-group" id="properties_list_group">

                        @if(count($listings_with_notes) > 0 || count($contracts_with_notes) > 0 || count($referrals_with_notes) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-top-0 border-left-0 border-right-0" data-cat="notes">
                                <div class="h4 text-orange my-0 ml-2"><i class="fad fa-comments mr-2"></i> New Comments</div>
                            </a>

                            @foreach($listings_with_notes as $listing_with_notes)

                                @php
                                $address = ucwords(strtolower($listing_with_notes -> FullStreetAddress.' '.$listing_with_notes -> City)).', '.$listing_with_notes -> StateOrProvince.', '.$listing_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Listing_ID', $listing_with_notes -> Listing_ID) -> where('note_status', 'unread') -> where('Agent_ID', '>', '0') -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item comments border-right-0" data-id="{{ $listing_with_notes -> Listing_ID }}" data-type="listing" data-cat="notes">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                            @foreach($contracts_with_notes as $contract_with_notes)

                                @php
                                $address = ucwords(strtolower($contract_with_notes -> FullStreetAddress.' '.$contract_with_notes -> City)).', '.$contract_with_notes -> StateOrProvince.', '.$contract_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Contract_ID', $contract_with_notes -> Contract_ID) -> where('note_status', 'unread') -> where('Agent_ID', '>', '0') -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item comments border-right-0" data-id="{{ $contract_with_notes -> Contract_ID }}" data-type="contract" data-cat="notes">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                            @foreach($referrals_with_notes as $referral_with_notes)

                                @php
                                $address = ucwords(strtolower($referral_with_notes -> FullStreetAddress.' '.$referral_with_notes -> City)).', '.$referral_with_notes -> StateOrProvince.', '.$referral_with_notes -> PostalCode;
                                $comments_to_review_count = $checklist_item_notes -> where('Referral_ID', $referral_with_notes -> Referral_ID) -> where('note_status', 'unread') -> where('Agent_ID', '>', '0') -> count();
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item comments border-right-0" data-id="{{ $referral_with_notes -> Referral_ID }}" data-type="referral" data-cat="notes">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $comments_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($listings) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-top-0 border-left-0 border-right-0" data-cat="listings">
                                <div class="h4 text-orange my-0 ml-2"><i class="fad fa-sign mr-2"></i> Listing Documents</div>
                            </a>

                            @foreach($listings as $listing)

                                @php
                                $address = ucwords(strtolower($listing -> FullStreetAddress.' '.$listing -> City)).', '.$listing -> StateOrProvince.', '.$listing -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($listing -> Listing_ID, 'listing');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item border-right-0" data-id="{{ $listing -> Listing_ID }}" data-type="listing" data-cat="listings">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($contracts) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-top-0 border-left-0 border-right-0" data-cat="contracts">
                                <div class="h4 text-orange my-0 ml-2"><i class="fad fa-file-contract mr-2"></i> Contract/Lease Documents</div>
                            </a>

                            @foreach($contracts as $contract)

                                @php
                                $address = ucwords(strtolower($contract -> FullStreetAddress.' '.$contract -> City)).', '.$contract -> StateOrProvince.', '.$contract -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($contract -> Contract_ID, 'contract');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item border-right-0" data-id="{{ $contract -> Contract_ID }}" data-type="contract" data-cat="contracts">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif

                        @if(count($referrals) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-top-0 border-left-0 border-right-0" data-cat="referrals">
                                <div class="h4 text-orange my-0 ml-2"><i class="fad fa-handshake mr-2"></i> Referral Documents</div>
                            </a>

                            @foreach($referrals as $referral)

                                @php
                                $address = ucwords(strtolower($referral -> FullStreetAddress.' '.$referral -> City)).', '.$referral -> StateOrProvince.', '.$referral -> PostalCode;
                                $docs_to_review = $checklist_item_docs -> GetDocsToReviewCount($referral -> Referral_ID, 'referral');
                                $docs_to_review_count = count($docs_to_review);
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item border-right-0" data-id="{{ $referral -> Referral_ID }}" data-type="referral" data-cat="referrals">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                        <div>
                                            <span class="badge bg-primary rounded text-white todo-count">{{ $docs_to_review_count }}</span>
                                        </div>
                                    </div>
                                </a>

                            @endforeach

                        @endif


                        @if(count($cancel_requests) > 0)

                            <a href="javascript: void(0)" class="list-group-item property-list-header border-top-0 border-left-0 border-right-0" data-cat="cancels">
                                <div class="h4 text-orange my-0 ml-2"><i class="fad fa-minus-circle mr-2"></i> Contract Cancellation Requests</div>
                            </a>

                            @foreach($cancel_requests as $cancel_request)

                                @php
                                $address = ucwords(strtolower($cancel_request -> FullStreetAddress.' '.$cancel_request -> City)).', '.$cancel_request -> StateOrProvince.', '.$cancel_request -> PostalCode;
                                @endphp

                                <a href="javascript: void(0)" class="list-group-item list-group-item-action property-item cancellation border-right-0" data-id="{{ $cancel_request -> Contract_ID }}" data-type="contract" data-cat="cancels">
                                    <div class="d-flex justify-content-between align-items-center property-item-div">
                                        <div class="address-div">
                                            {{ $address }}
                                        </div>
                                    </div>
                                </a>

                            @endforeach
                        @endif
                    </div>

                    <div class="py-5"></div>
                </div>

                <div class="checklist-items-container w-100">

                    <div class="sticky bg-white py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="javascript: void(0);" id="close_checklist_button" class="btn btn-sm btn-primary"><i class="fal fa-chevron-double-left mr-2"></i> Back</a>
                            <a href="javascript: void(0);" class="btn btn-sm btn-primary next-button">Next <i class="fal fa-chevron-double-right ml-2"></i></a>
                        </div>
                    </div>

                    <div class="checklist-items">

                        <div class="checklist-items-div pb-5 mb-5"> </div>

                    </div>

                    <div class="py-5"></div>

                </div>

            </div>

        </div>

        <div class="col-6">

            <div class="documents-container">

                <div class="documents-div">
                    <div class="h1 text-primary w-100 text-center mt-5 pt-5"><i class="fa fa-arrow-left mr-2"></i> To Begin Select A Property</div>
                </div>

            </div>

        </div>

        <div class="col-3">

            <div class="details-container border-left">

                <div class="details-div"> </div>

            </div>

        </div>

    </div>

    <div class="bottom-line"></div>

</div>

<input type="hidden" id="review_contract_id" value="{{ $Contract_ID }}">

@endsection
