@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')

<div class="container-full page-container page-dashboard">

    <div class="row mt-4">

        <div class="col-12 col-lg-4">

            <div class="row">

                <div class="col-12 col-sm-6 col-lg-12">

                    <div class="bg-primary p-1 mb-3 rounded">

                        <div class="d-flex justify-content-between bg-primary text-white px-3 py-2 font-12">
                            <div>
                                <i class="fad fa-bell mr-2"></i> Notifications
                            </div>
                            <div>
                                <span class="badge bg-orange text-white notifications-unread-count"></span>
                            </div>
                        </div>

                        <div class="notifications-container">
                            <div class="global-notifications-div bg-white p-2 rounded"></div>
                        </div>

                    </div>

                </div>

                <div class="col-12 col-sm-6 col-lg-12">

                    <div class="bg-danger p-1 mb-3 rounded">

                        <div class="bg-danger text-white p-2 font-12">
                            <i class="fad fa-exclamation-triangle mr-2"></i> Transaction Alerts
                        </div>

                        <div class="bg-white p-2 rounded alerts-container">

                            @if(!$show_alerts)

                                <div class="text-gray font-10 text-center p-2">
                                    <i class="fal fa-check mr-2"></i> No Transaction Alerts
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

                                            <div class="list-group-item p-1 border-left-0 border-right-0">

                                                <div class="d-flex justify-content-between align-items-center font-9">

                                                    <div class="d-flex justify-content-start align-items-center">

                                                        <div class="d-flex justify-content-around align-items-center bg-danger text-white p-1 wpx-60 rounded">
                                                            {{ $count }}
                                                        </div>

                                                        <div class="text-gray ml-3">
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

            </div>






        </div>

        <div class="col-12 col-lg-8">

            <div class="bg-blue-light p-3 rounded">

                <div class="row">

                    <div class="col-12">

                        <div id="transactions_mod"></div>

                    </div>

                </div>

                @if(auth() -> user() -> group == 'admin')

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="bg-white p-2 rounded">

                                <div class="font-11 text-orange">Admin ToDo</div>

                                <div id="admin_todo_mod"></div>

                            </div>

                        </div>

                    </div>

                @endif

                @if(stristr(auth() -> user() -> group, 'agent'))

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="bg-white text-gray p-3 rounded">

                                <div class="font-11 text-orange">Commissions Status</div>

                                <div id="commissions_mod"></div>

                            </div>

                        </div>

                    </div>

                @endif

                @if(auth() -> user() -> group != 'agent_referral')

                    <div class="row mt-3">

                        <div class="col-12">

                            <div class="bg-white text-gray p-3 rounded">

                                <div class="font-11 text-orange">Upcoming Closings</div>

                                <div id="upcoming_closings_mod"></div>

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
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-12">

                        <div class="text-gray font-10 mb-3" id="alert_details_modal_details"></div>

                        <div class="list-group border-top alert-details-container">

                            @foreach($alerts as $alert)

                                @php $agent = $alert -> agent -> full_name; @endphp

                                <div class="list-group-item alert-details-item p-1 font-9 {{ $alert -> alert_type }}">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="d-flex justify-content-start align-items-center text-gray">
                                            {{-- <div>
                                                <a href="/agents/doc_management/transactions/transaction_details/{{ $alert -> id }}/{{ $alert -> transaction_type }}" class="btn btn-primary" target="_blank">View {{ ucwords($alert -> transaction_type) }}</a>
                                            </div> --}}
                                            <div class="ml-3">
                                                <a href="/agents/doc_management/transactions/transaction_details/{{ $alert -> id }}/{{ $alert -> transaction_type }}" target="_blank">
                                                    <span class="text-primary">{{ $alert -> FullStreetAddress.' '.$alert -> City.', '.$alert -> StateOrProvince.' '.$alert -> PostalCode }}</span>
                                                </a>
                                                <br>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    @if(auth() -> user() -> group == 'admin')
                                                        <div class="text-dark font-weight-bold mr-2">
                                                            {{ $agent }}
                                                        </div>
                                                    @endif
                                                    @if($alert -> transaction_type == 'listing')
                                                        <div title="List Date" data-toggle="tooltip">
                                                            LD - {{ date_mdy($alert -> MLSListDate) }}
                                                        </div>
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Expiration Date" data-toggle="tooltip">
                                                            EX - {{ date_mdy($alert -> ExpirationDate) }}
                                                        </div>
                                                    @elseif($alert -> transaction_type == 'contract')
                                                        <div title="Contract Date" data-toggle="tooltip">
                                                            CD - {{ date_mdy($alert -> ContractDate) }}
                                                        </div>
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Settle Date" data-toggle="tooltip">
                                                            SD - {{ date_mdy($alert -> CloseDate) }}
                                                        </div>
                                                    @elseif($alert -> transaction_type == 'referral')
                                                        <span class="font-12 text-primary mx-3">|</span>
                                                        <div title="Settle Date" data-toggle="tooltip">
                                                            SD - {{ date_mdy($alert -> CloseDate) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pr-2">
                                            @if($alert -> DocsMissingCount > 0)
                                                <span class="text-danger">Missing Docs - {{ $alert -> DocsMissingCount }}</span>
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


