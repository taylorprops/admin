<div class="row animate__animated animate__fadeIn no-gutters">
    <div class="col-12">

        <div class="d-flex justify-content-between align-items-center pt-3 px-3">

            <div>
                <div class="h5 text-primary">{!! $address !!}</div>
            </div>

            <div class="">
                <a href="/agents/doc_management/transactions/transaction_details/{{ $id }}/{{ $transaction_type }}" class="btn btn-sm btn-primary" target="_blank">View {{ ucwords($transaction_type) }}</a>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-around align-items-center transaction-details p-2 pb-3">
            @if($transaction_type != 'referral')
                <div>
                    @if($transaction_type == 'listing')
                    <i class="fad fa-sign fa-3x text-orange"></i>
                    @elseif($transaction_type =='contract')
                    <i class="fad fa-file-signature fa-3x text-orange"></i>
                    @endif
                </div>
                <span class="badge bg-primary"><span class="transaction-sub-type text-white">{{ $sale_rent }}</span></span>
                <span class="badge bg-primary"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertyType) }}</span></span>
                @if($sale_rent != 'Rental' && $property -> PropertySubType > '0')
                    <span class="badge bg-primary"><span class="transaction-sub-type text-white">{{ $resource_items -> GetResourceName($property -> PropertySubType) }}</span></span>
                @endif
            @endif
        </div>

        <div class="details-content p-2">

            @if($transaction_type == 'referral')
                <div class="divider"></div>
                <div class="row my-3">
                    <div class="col-12">
                        <span class="text-gray">Agent:</span>
                        <span class="font-weight-bold pl-2">{{ $agent_details -> first_name. ' ' . $agent_details -> last_name }}</span>
                    </div>
                </div>
                <div class="divider"></div>
            @else

                <div class="row">
                    <div class="col-12">

                        @if($property -> Status == $cancel_pending_status_id)
                            <div class="divider"></div>
                            <div class="m-1 p-3 bg-danger rounded animate__animated animate__shakeX animate__delay-2s cancel-status">
                                <div class="h4 text-white d-flex justify-content-start align-items-center">
                                    <i class="fad fa-exclamation-circle mr-2"></i>
                                    <span>
                                        Cancellation Pending
                                    </span>
                                </div>
                            </div>
                        @elseif($property -> Status == $canceled_status_id)
                            <div class="divider"></div>
                            <div class="m-1 p-3 bg-success rounded cancel-status">
                                <div class="h4 text-white d-flex justify-content-start align-items-center">
                                    <i class="fal fa-check-circle mr-2"></i>
                                    <span>
                                        Cancellation Complete
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <table class="table property-details-table">
                    <tbody>
                        <tr>
                            <td colspan="2" class="divider"></td>
                        </tr>
                        <tr>
                            <td class="text-gray text-right">Agent</td>
                            <td class="font-weight-bold pl-2">{{ $agent_details -> first_name. ' ' . $agent_details -> last_name }}</td>
                        </tr>
                        @if($co_agent_details)
                        <tr>
                            <td class="text-gray text-right">Co Agent</td>
                            <td class="font-weight-bold pl-2">{{ $co_agent_details -> first_name. ' ' . $co_agent_details -> last_name }}</td>
                        </tr>
                        @endif
                        @if($property -> TransactionCoordinator_ID > 0)
                        <tr>
                            <td class="text-gray text-right">Trans Coord.</td>
                            <td class="font-weight-bold pl-2">{{ $property -> TransactionCoordinator_ID }}</td>
                        </tr>
                        @endif
                        @if($property -> Team_ID > 0)
                        <tr>
                            <td class="text-gray text-right">Team</td>
                            <td class="font-weight-bold pl-2">{{ $property -> Team_ID }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" class="divider"></td>
                        </tr>
                        <tr>
                            <td class="text-gray text-right">Status</td>
                            <td class="font-weight-bold pl-2">{{ $resource_items -> GetResourceName($property -> Status) }}</td>
                        </tr>
                        @if($property -> ListingId)
                        <tr>
                            <td class="text-gray text-right">Bright MLS ID</td>
                            <td class="font-weight-bold pl-2">{{ $property -> ListingId }}</td>
                        </tr>
                        @endif

                        @if($transaction_type == 'listing')
                            <tr>
                                <td class="text-gray text-right">{{ $for_sale ? 'List Price' : 'Lease Amount' }}</td>
                                <td class="font-weight-bold pl-2">${{ number_format($property -> ListPrice) }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray text-right">List Date</td>
                                <td class="font-weight-bold pl-2">{{ date('n/j/Y', strtotime($property -> MLSListDate)) }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray text-right">Expiration Date</td>
                                <td class="font-weight-bold pl-2">{{ date('n/j/Y', strtotime($property -> ExpirationDate)) }}</td>
                            </tr>


                        @elseif($transaction_type == 'contract')
                            @if($for_sale)
                                <tr>
                                    <td class="text-gray text-right">Contract Date</td>
                                    <td class="font-weight-bold pl-2">{{ date('n/j/Y', strtotime($property -> ContractDate)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray text-right">Settle Date</td>
                                    <td class="font-weight-bold pl-2">{{ date('n/j/Y', strtotime($property -> CloseDate)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray text-right">Sale Price</td>
                                    <td class="font-weight-bold pl-2">${{ number_format($property -> ContractPrice) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray text-right">Earnest Held By</td>
                                    <td class="font-weight-bold pl-2">{{ $earnest_held_by }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray text-right">Title company</td>
                                    <td class="font-weight-bold pl-2">{{ $title_company ?? '' }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-gray text-right">Lease Date</td>
                                    <td class="font-weight-bold pl-2">{{ date('n/j/Y', strtotime($property -> CloseDate)) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-gray text-right">Lease Amount</td>
                                    <td class="font-weight-bold pl-2">${{ number_format($property -> LeaseAmount) }}</td>
                                </tr>
                            @endif


                        @endif

                        <tr>
                            <td class="text-gray text-right">Year Built</td>
                            <td class="font-weight-bold pl-2">{{ $property -> YearBuilt }}</td>
                        </tr>

                        <tr>
                            <td colspan="2" class="divider"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="h5 text-primary">Transaction Members</td>
                        </tr>
                        @foreach($members as $member)
                            @if(stristr($resource_items -> GetResourceName($member -> member_type_id) , 'agent'))
                                <tr>
                                    <td class="text-gray text-right">{{ $resource_items -> GetResourceName($member -> member_type_id) }}</td>
                                    <td class="font-weight-bold pl-2">
                                        @if($member -> company) {{ $member -> company }}<br> @endif
                                        {{ $member -> first_name.' '.$member -> last_name }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach($members as $member)
                            @if(stristr($resource_items -> GetResourceName($member -> member_type_id) , 'seller') && !stristr($resource_items -> GetResourceName($member -> member_type_id) , 'agent'))
                                <tr>
                                    <td class="text-gray text-right">{{ $resource_items -> GetResourceName($member -> member_type_id) }}</td>
                                    <td class="font-weight-bold pl-2">
                                        @if($member -> company) {{ $member -> company }}<br> @endif
                                        {{ $member -> first_name.' '.$member -> last_name }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach($members as $member)
                            @if(stristr($resource_items -> GetResourceName($member -> member_type_id) , 'buyer') && !stristr($resource_items -> GetResourceName($member -> member_type_id) , 'agent'))
                                <tr>
                                    <td class="text-gray text-right">{{ $resource_items -> GetResourceName($member -> member_type_id) }}</td>
                                    <td class="font-weight-bold pl-2">
                                        @if($member -> company) {{ $member -> company }}<br> @endif
                                        {{ $member -> first_name.' '.$member -> last_name }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach($members as $member)
                            @if(!preg_match('/(buyer|seller|agent)/i', $resource_items -> GetResourceName($member -> member_type_id)))
                                <tr>
                                    <td class="text-gray text-right">{{ $resource_items -> GetResourceName($member -> member_type_id) }}</td>
                                    <td class="font-weight-bold pl-2">
                                        @if($member -> company) {{ $member -> company }}<br> @endif
                                        {{ $member -> first_name.' '.$member -> last_name }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>



    </div>
</div>
