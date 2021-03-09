@if(count($listings) > 0 || count($contracts) > 0 || count($referrals) > 0)

    <div class="row search-results-row">

        @if(count($listings) > 0)

            <div class="col-12 col-md-6 col-xl-4 px-2">

                <div class="text-orange font-11 mb-1 ml-2 mt-2">Listings</div>

                <div class="list-group">

                    @foreach($listings as $property)

                        @php
                        $agent = $property -> agent -> full_name;
                        $status = $property -> status -> resource_name;
                        $status_color = $property -> status -> resource_color;
                        $transaction_coordinator = $property -> transaction_coordinator;
                        @endphp

                        <div class="list-group-item search-item px-2 my-1 @if(!$loop -> first) border-top @endif" data-href="/agents/doc_management/transactions/transaction_details/{{ $property -> Contract_ID }}/contract">

                                <div class="row">

                                    <div class="col-5 col-md-8 col-lg-5"">

                                        <div class="font-10">
                                            <a href="/agents/doc_management/transactions/transaction_details/{{ $property -> Listing_ID }}/listing">
                                                {!! $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode !!}
                                            </a>
                                        </div>

                                    </div>

                                    <div class="col-4 font-10" style="color: {{ $status_color }}">
                                        {{ $status }}
                                        @if(stristr($status, 'Under Contract') || stristr($status, 'Closed'))
                                            <br>
                                            <a href="/agents/doc_management/transactions/transaction_details/{{ $property -> Contract_ID }}/contract" class="font-8">View Contract</a>
                                        @endif
                                    </div>



                                    <div class="col-3 d-md-none d-lg-inline-block">

                                        <div class="overflow-hidden text-right font-10">
                                            @if($property -> ListPictureURL)
                                                <img src="{{ $property -> ListPictureURL }}" class="search-result-image">
                                            @else
                                                <i class="fad fa-home fa-3x text-primary"></i>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="row mt-3">

                                    <div class="col-12">

                                        <div class="d-flex justify-content-between align-items-center flex-wrap no-wrap">

                                            @if(Cookie::get('user_group') == 'admin')
                                                <div>
                                                    <span class="text-gray">{{ $agent }}</span>
                                                </div>
                                            <span class="font-12 text-primary">|</span>
                                            @endif
                                            <div>
                                                {{ ucwords($property -> SaleRent) }}
                                            </div>
                                            <span class="font-12 text-primary">|</span>
                                            <div>
                                                <span class="text-gray">${{ number_format($property -> ListPrice) }}</span>
                                            </div>
                                            <span class="font-12 text-primary">|</span>
                                            <div title="List Date" data-toggle="tooltip">
                                                LD - <span class="text-gray">{{ date_mdy($property -> MLSListDate) }}</span>
                                            </div>
                                            <span class="font-12 text-primary">|</span>
                                            <div title="Expiration Date" data-toggle="tooltip">
                                                EX - <span class="text-gray">{{ date_mdy($property -> ExpirationDate) }}</span>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                @if($transaction_coordinator)

                                    <div class="row">
                                        <div class="col-12">
                                            <hr>
                                            Transaction Coordinator -
                                            <span class="text-gray">{{ $transaction_coordinator -> first_name.' '.$transaction_coordinator -> last_name }}</span>
                                        </div>
                                    </div>
                                @endif


                        </div>

                    @endforeach

                </div>

            </div>

        @endif


        @if(count($contracts) > 0)

            <div class="col-12 col-md-6 col-xl-4 px-2">

                <div class="text-orange font-11 mb-1 ml-2 mt-2">Contracts/Leases</div>

                <div class="list-group">

                    @foreach($contracts as $property)

                        @php
                        $agent = $property -> agent -> full_name;
                        $status = $property -> status -> resource_name;
                        $status_color = $property -> status -> resource_color;
                        $earnest = $property -> earnest;
                        $transaction_coordinator = $property -> transaction_coordinator;
                        @endphp

                        <div class="list-group-item search-item px-2 my-1 @if(!$loop -> first) border-top @endif" data-href="/agents/doc_management/transactions/transaction_details/{{ $property -> Contract_ID }}/contract">

                            <div class="row">

                                <div class="col-5 col-md-8 col-lg-5"">

                                    <div class="font-10">
                                        <a href="/agents/doc_management/transactions/transaction_details/{{ $property -> Contract_ID }}/contract">
                                            {!! $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode !!}
                                        </a>
                                    </div>

                                </div>

                                <div class="col-4 font-10" style="color: {{ $status_color }}">
                                    {{ $status }}
                                </div>


                                <div class="col-3 d-md-none d-lg-inline-block">

                                    <div class="overflow-hidden text-right font-10">
                                        @if($property -> ListPictureURL)
                                            <img src="{{ $property -> ListPictureURL }}" class="search-result-image">
                                        @else
                                            <i class="fad fa-home fa-3x text-primary"></i>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-3">

                                <div class="col-12">

                                    <div class="d-flex justify-content-between align-items-center  flex-wrap no-wrap">
                                        @if(Cookie::get('user_group') == 'admin')
                                            <div>
                                                <span class="text-gray">{{ $agent }}</span>
                                            </div>
                                        <span class="font-12 text-primary">|</span>
                                        @endif
                                        <div>
                                            {{ ucwords($property -> SaleRent) }}
                                        </div>
                                        <span class="font-12 text-primary">|</span>
                                        <div>
                                            <span class="text-gray">${{ number_format($property -> ContractPrice) }}</span>
                                        </div>
                                        <span class="font-12 text-primary">|</span>
                                        <div title="Contract Date" data-toggle="tooltip">
                                            CD - <span class="text-gray">{{ date_mdy($property -> ContractDate) }}</span>
                                        </div>
                                        <span class="font-12 text-primary">|</span>
                                        <div title="Settle Date" data-toggle="tooltip">
                                            SD - <span class="text-gray">{{ date_mdy($property -> CloseDate) }}</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            @if($transaction_coordinator)

                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        Transaction Coordinator -
                                        <span class="text-gray">{{ $transaction_coordinator -> first_name.' '.$transaction_coordinator -> last_name }}</span>
                                    </div>
                                </div>
                            @endif

                            @if(Cookie::get('user_group') == 'admin')
                                @php
                                if($property -> EarnestHeldBy == 'us') {
                                    $earnest_html = '$'.number_format($earnest -> amount_total);
                                    if($earnest -> amount_received > 0 && $earnest -> amount_total == 0) {
                                        $earnest_html .= ' <span class="float-right"><i class="fal fa-check mr-2"></i> Released</span>';
                                    } else if($earnest -> amount_total == 0) {
                                        $earnest_html .= ' <span class="text-danger float-right"><i class="fal fa-exclamation-circle mr-2"></i> Not Received Yet</span>';
                                    }
                                } else {
                                    $earnest_html = 'No';
                                }
                                @endphp
                                <div class="row">
                                    <div class="col-12 @if($property -> EarnestHeldBy == 'us') text-success @else text-danger @endif">
                                        <hr>
                                        Holding Earnest - {!! $earnest_html !!}
                                    </div>
                                </div>
                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

        @if(count($referrals) > 0)

            <div class="col-12 col-md-6 col-xl-4 px-2">

                <div class="text-orange font-11 mb-1 ml-2 mt-2">Referrals</div>

                <div class="list-group">

                    @foreach($referrals as $property)

                        @php
                        $agent = $property -> agent -> full_name;
                        $status = $property -> status -> resource_name;
                        $status_color = $property -> status -> resource_color;
                        $transaction_coordinator = $property -> transaction_coordinator;
                        @endphp

                        <div class="list-group-item search-item px-2 my-1 @if(!$loop -> first) border-top @endif" data-href="/agents/doc_management/transactions/transaction_details/{{ $property -> Referral_ID }}/referral">

                            <div class="row">

                                <div class="col-5 col-md-8 col-lg-5">

                                    <div class="font-10 text-primary">
                                        <a href="/agents/doc_management/transactions/transaction_details/{{ $property -> Referral_ID }}/referral">
                                            {!! $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode !!}
                                        </a>
                                    </div>

                                </div>

                                <div class="col-4 font-10" style="color: {{ $status_color }}">
                                    {{ $status }}
                                </div>


                                <div class="col-3 d-md-none d-lg-inline-block">

                                    <div class="overflow-hidden text-right font-10">
                                        <i class="fad fa-home fa-3x text-primary"></i>
                                    </div>

                                </div>

                            </div>

                            <div class="row mt-3">

                                <div class="col-12">

                                    <div class="d-flex justify-content-between align-items-center  flex-wrap no-wrap">
                                        @if(Cookie::get('user_group') == 'admin')
                                            <div>
                                                <span class="text-gray">{{ $agent }}</span>
                                            </div>
                                        <span class="font-12 text-primary">|</span>
                                        @endif
                                        <div>
                                            {{ $property -> ClientFirstName. ' '.$property -> ClientLastName }}
                                        </div>
                                        <span class="font-12 text-primary">|</span>

                                        <div title="Close Date" data-toggle="tooltip">
                                            CD - <span class="text-gray">{{ date_mdy($property -> CloseDate) }}</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            @if($transaction_coordinator)

                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        Transaction Coordinator -
                                        <span class="text-gray">{{ $transaction_coordinator -> first_name.' '.$transaction_coordinator -> last_name }}</span>
                                    </div>
                                </div>
                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    </div>

@else

    <div class="row p-5">
        <div class="col-12">
            <div class="d-flex justify-content-around">
                <div class="d-flex justify-content-start align-items-center font-12 text-gray mx-auto">
                    <div>
                        <i class="fad fa-frown fa-2x mr-3"></i>
                    </div>
                    <div>
                        Sorry, no results match your criteria
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
