@extends('layouts.main')
@section('title', 'Transactions')

@section('content')
<div class="container page-container page-transactions">
    <div class="row my-3">

        <div class="col-12 col-sm-6">

            <div class="card p-2">

                <div class="h4 text-orange"><i class="fad fa-sign mr-2"></i> Active Listings</div>

                <!-- Card content -->
                <div class="card-body">

                    <div class="row">

                        @foreach($listings as $listing)

                            <div class="col-12 col-md-6">
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $listing -> Listing_ID }}/listing">

                                    <div class="bg-primary p-2 m-2 shadow text-white">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="h5 text-center">{!! $listing -> FullStreetAddress. '<br>' . $listing -> City . ' ' . $listing -> StateOrProvince .' '.$listing -> PostalCode !!}</div>
                                            </div>
                                        </div>
                                        <hr class="bg-white">

                                        <div class="d-flex justify-content-around align-items-center">
                                            <div class="mr-3"><i class="fad fa-sign mx-2 fa-3x"></i></div>
                                            <div>
                                                @if($listing -> ListPictureURL)
                                                <img src="{{ $listing -> ListPictureURL }}" class="property-image image-fluid mr-2 shadow">
                                                @else
                                                <i class="fad fa-home-alt fa-5x"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <hr class="bg-white">
                                        <div class="d-flex justify-content-around">
                                            <div class="h4">{{ $resource_items -> GetResourceName($listing -> Status) }}</div>
                                            <div class="h4">${{ number_format($listing -> ListPrice) }}</div>
                                        </div>

                                        <hr class="bg-white">

                                        <div class="row">
                                            <div class="col-5 pr-0 text-right">List Date</div>
                                            <div class="col-7 text-left">{{ date('n/j/Y', strtotime($listing -> MLSListDate)) }}</div>
                                            <div class="col-5 pr-0 text-right">Expire Date</div>
                                            <div class="col-7 text-left">{{ date('n/j/Y', strtotime($listing -> ExpirationDate)) }}</div>

                                            <div class="col-12">
                                                <hr class="bg-white">
                                            </div>

                                            <div class="col-5 pr-0 text-right">Seller</div>
                                            <div class="col-7 text-left">{{ $listing -> SellerOneFirstName.' '.$listing -> SellerOneLastName }}</div>
                                            @if($listing -> SellerTwoFirstName)
                                            <div class="col-5 pr-0 text-right">Seller</div>
                                            <div class="col-7 text-left">{{ $listing -> SellerTwoFirstName.' '.$listing -> SellerTwoLastName }}</div>
                                            @else
                                            <div class="col-12">&nbsp;</div>
                                            @endif

                                        </div>
                                    </div>

                                </a>
                            </div>

                        @endforeach

                    </div>

                </div>

            </div>
        </div>

        <div class="col-12 col-sm-6">

            <div class="card p-2">

                <div class="h4 text-orange"><i class="fad fa-file-signature mr-2"></i> Active Contracts</div>

                <!-- Card content -->
                <div class="card-body">

                    <div class="row">

                        @foreach($contracts as $contract)

                            <div class="col-12 col-md-6">
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract">

                                    <div class="bg-primary p-2 m-2 shadow text-white">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="h5 text-center">{!! $contract -> FullStreetAddress. '<br>' . $contract -> City . ' ' . $contract -> StateOrProvince .' '.$contract -> PostalCode !!}</div>
                                            </div>
                                        </div>
                                        <hr class="bg-white">

                                        <div class="d-flex justify-content-around align-items-center">
                                            <div class="mr-3"><i class="fad fa-file-signature mx-2 fa-3x"></i></div>
                                            <div>
                                                @if($contract -> ListPictureURL)
                                                <img src="{{ $contract -> ListPictureURL }}" class="property-image image-fluid mr-2 shadow">
                                                @else
                                                <i class="fad fa-home-alt fa-5x"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <hr class="bg-white">
                                        <div class="d-flex justify-content-around">
                                            <div class="h4">{{ $resource_items -> GetResourceName($contract -> Status) }}</div>
                                            <div class="h4">${{ number_format($contract -> ContractPrice) }}</div>
                                        </div>

                                        <hr class="bg-white">

                                        <div class="row">
                                            <div class="col-5 pr-0 text-right">Contact Date</div>
                                            <div class="col-7 text-left">{{ date('n/j/Y', strtotime($contract -> ContractDate)) }}</div>
                                            <div class="col-5 pr-0 text-right">Settle Date</div>
                                            <div class="col-7 text-left">{{ date('n/j/Y', strtotime($contract -> CloseDate)) }}</div>

                                            <div class="col-12">
                                                <hr class="bg-white">
                                            </div>

                                            <div class="col-5 pr-0 text-right">Buyer</div>
                                            <div class="col-7 text-left">{{ $contract -> BuyerOneFirstName.' '.$contract -> BuyerOneLastName }}</div>
                                            @if($contract -> BuyerTwoFirstName)
                                            <div class="col-5 pr-0 text-right">Buyer</div>
                                            <div class="col-7 text-left">{{ $contract -> BuyerTwoFirstName.' '.$contract -> BuyerTwoLastName }}</div>
                                            @else
                                            <div class="col-12">&nbsp;</div>
                                            @endif

                                        </div>
                                    </div>

                                </a>
                            </div>

                        @endforeach

                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-12 col-sm-6">

            <div class="card p-2">

                <div class="h4 text-orange"><i class="fad fa-sign mr-2"></i> Active Referrals</div>

                <!-- Card content -->
                <div class="card-body">

                    <div class="row">

                        @foreach($referrals as $referral)

                            <div class="col-12 col-md-6">
                                <a href="/agents/doc_management/transactions/transaction_details/{{ $referral -> Referral_ID }}/referral">

                                    <div class="bg-primary p-2 m-2 shadow text-white">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="h5 text-center">{!! $referral -> FullStreetAddress. '<br>' . $referral -> City . ' ' . $referral -> StateOrProvince .' '.$referral -> PostalCode !!}</div>
                                            </div>
                                        </div>
                                        <hr class="bg-white">

                                        <div class="d-flex justify-content-around align-items-center">
                                            <div><i class="fad fa-handshake fa-3x"></i></div>
                                            <div class="h4">{{ $resource_items -> GetResourceName($referral -> Status) }}</div>

                                        </div>
                                        <hr class="bg-white">

                                        <div class="row">
                                            <div class="col-5 pr-0 text-right">Date Added</div>
                                            <div class="col-7 text-left">{{ date('n/j/Y', strtotime($referral -> created_at)) }}</div>
                                            <div class="col-5 pr-0 text-right">Client </div>
                                            <div class="col-7 text-left">{{ $referral -> ClientFirstName.' '.$referral -> ClientLastName }}</div>
                                            <div class="col-5 pr-0 text-right">Agent </div>
                                            <div class="col-7 text-left">{{ $referral -> ReceivingAgentFirstName.' '.$referral -> ReceivingAgentLastName }}</div>
                                            <div class="col-5 pr-0 text-right">Company </div>
                                            <div class="col-7 text-left">{{ $referral -> ReceivingAgentOfficeName }}</div>


                                        </div>
                                    </div>

                                </a>
                            </div>

                        @endforeach

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
