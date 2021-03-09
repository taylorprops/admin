@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')

<div class="container page-container page-dashboard-agent-container pt-5">

    <div class="row mt-5">

        <div class="col-12 col-lg-4">

            <div class="bg-red-light p-2 rounded">

                <div class="bg-danger text-white p-3 font-12">
                    <i class="fad fa-exclamation-triangle mr-2"></i> Alerts
                </div>

                <div class="bg-white p-2 rounded">

                    @if(!$show_alerts)

                        <div class="text-gray font-13 text-center p-4">
                            <i class="fal fa-check mr-2"></i> No Alerts
                        </div>

                    @else

                        <div class="list-group mt-3">

                            @if(count($alerts) > 0)

                                @foreach($alert_types as $alert_type)

                                    @php
                                    $alerts_by_type = $alerts -> where('alert_type', $alert_type);
                                    $count = count($alerts_by_type);
                                    $title = $alerts_by_type -> first() -> title;
                                    $details = $alerts_by_type -> first() -> details;
                                    @endphp

                                    <div class="list-group-item p-1">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div class="d-flex justify-content-start align-items-center">

                                                <div class="d-flex justify-content-around align-items-center bg-danger text-white font-12 py-2 px-3 rounded">
                                                    {{ $count }}
                                                </div>

                                                <div class="text-gray font-11 ml-3">
                                                    {{ $title }}
                                                </div>

                                            </div>

                                            <div>
                                                <button class="btn btn-primary view-alert-details-button" data-type="{{ $alert_type }}" data-title="{!! $title !!}" data-details="{!! $details !!}">View</button>
                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            @endif

                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div class="col-12 col-lg-8">

            <div class="bg-blue-light p-3 rounded">

                <div class="row">

                    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4">

                        <div class="bg-white text-gray p-3 rounded">

                            <div class="d-flex justify-content-start align-items-center">
                                <div class="w-80 pl-2">
                                    <div class="text-orange font-13">Active Listings</div>
                                </div>
                                <div class="d-flex justify-content-around align-items-center font-14 bg-orange text-white w-20 mb-2 p-2 rounded">
                                    {{ $active_listings_count }}
                                </div>
                            </div>

                            <div class="d-flex justify-content-around align-items-center">
                                <a href="/agents/doc_management/transactions?tab=listings" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                                <a href="/agents/doc_management/transactions/add/listing" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
                            </div>


                        </div>

                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 mt-3 mt-sm-0">

                        <div class="bg-white text-gray p-3 rounded">

                            <div class="d-flex justify-content-start align-items-center">
                                <div class="w-80 pl-2">
                                    <div class="text-orange font-13">Active Contracts</div>
                                </div>
                                <div class="d-flex justify-content-around align-items-center font-14 bg-orange text-white w-20 mb-2 p-2 rounded">
                                    {{ $active_contracts_count }}
                                </div>
                            </div>

                            <div class="d-flex justify-content-around align-items-center">
                                <a href="/agents/doc_management/transactions?tab=contracts" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                                <a href="/agents/doc_management/transactions/add/contract" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
                            </div>


                        </div>

                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 mt-3 mt-md-0 mt-lg-3 mt-xl-0">

                        <div class="bg-white text-gray p-3 rounded">

                            <div class="d-flex justify-content-start align-items-center">
                                <div class="w-80 pl-2">
                                    <div class="text-orange font-13">Pending Referrals</div>
                                </div>
                                <div class="d-flex justify-content-around align-items-center font-14 bg-orange text-white w-20 mb-2 p-2 rounded">
                                    {{ $active_referrals_count }}
                                </div>
                            </div>

                            <div class="d-flex justify-content-around align-items-center">
                                <a href="/agents/doc_management/transactions?tab=referrals" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View All</a>
                                <a href="/agents/doc_management/transactions/add/referral" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add New</a>
                            </div>


                        </div>

                    </div>

                </div>

                @if(count($contracts_closing_this_month) > 0)
                    <div class="row">

                        <div class="col-12">

                            <div class="bg-white text-gray p-3 rounded">

                                <div class="font-11 text-orange">Upcoming Closings</div>

                                <div class="list-group">

                                    @foreach($contracts_closing_this_month as $contract)

                                        @php
                                        if($contract -> DocsMissingCount > 0) {
                                            $checklist_status = '<span class="text-danger"><i class="fal fa-exclamation-circle mr-2"></i> Missing Items</span>';
                                        } else {
                                            $checklist_status = '<span class="text-success"><i class="fal fa-check mr-2"></i> Complete</span>';
                                        }
                                        @endphp

                                        <div class="list-group-item">

                                            <div class="d-flex justify-content-between align-items-center">

                                                <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View</a>

                                                {{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}

                                                {{ date_mdy($contract -> CloseDate) }}

                                                {!! $checklist_status !!}

                                                <div class="">
                                                    @if($contract -> ListPictureURL)
                                                        <img src="{{ $contract -> ListPictureURL }}" height="50" class="img-responsive">
                                                    @else
                                                        <i class="fad fa-home fa-3x text-primary"></i>
                                                    @endif
                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="alert_details_modal" tabindex="-1" role="dialog" aria-labelledby="alert_details_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="alert_details_modal_title"></h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-12">

                        <div class="text-gray font-10 mb-3" id="alert_details_modal_details"></div>

                        <div class="list-group border-top">

                            @foreach($alerts as $alert)

                                <div class="list-group-item alert-details-item {{ $alert -> alert_type }}">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="d-flex justify-content-start align-items-center text-gray">
                                            <div>
                                                <a href="/agents/doc_management/transactions/transaction_details/{{ $alert -> id }}/{{ $alert -> transaction_type }}" class="btn btn-primary" target="_blank">View {{ ucwords($alert -> transaction_type) }}</a>
                                            </div>
                                            <div class="ml-3">
                                                <span class="font-11">{{ $alert -> FullStreetAddress.' '.$alert -> City.', '.$alert -> StateOrProvince.' '.$alert -> PostalCode }}</span>
                                                <br>
                                                @if($alert -> transaction_type == 'listing')
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div title="List Date" data-toggle="tooltip">
                                                            LD - {{ date_mdy($alert -> MLSListDate) }}
                                                        </div>
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Expiration Date" data-toggle="tooltip">
                                                            EX - {{ date_mdy($alert -> ExpirationDate) }}
                                                        </div>
                                                    </div>
                                                @elseif($alert -> transaction_type == 'contract')
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div title="Contract Date" data-toggle="tooltip">
                                                            CD - {{ date_mdy($alert -> ContractDate) }}
                                                        </div>
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Settle Date" data-toggle="tooltip">
                                                            SD - {{ date_mdy($alert -> CloseDate) }}
                                                        </div>
                                                    </div>
                                                @elseif($alert -> transaction_type == 'referral')
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Settle Date" data-toggle="tooltip">
                                                            SD - {{ date_mdy($alert -> CloseDate) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            @if($alert -> DocsMissingCount > 0)
                                                <span class="text-danger">{{ $alert -> DocsMissingCount }} Missing Docs</span>
                                            @else
                                                <span class="text-success"><i class="fal fa-check mr-2"></i> No Missing Docs</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Close Window</button>
            </div>
        </div>
    </div>
</div>


@endsection
