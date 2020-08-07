<div class="row animated fadeIn">
    <div class="col-12 pr-0">

        <div class="ml-3 pt-3">

            <div class="d-flex justify-content-between align-items-center pr-2">

                <div>
                    <div class="h5-responsive text-primary">{!! $address !!}</div>
                </div>

                <div class="">
                    <a href="/agents/doc_management/transactions/transaction_details/{{ $id }}/{{ $transaction_type }}" class="btn btn-primary" target="_blank">View {{ ucwords($transaction_type) }}</a>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-around align-items-center transaction-details p-2">
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

            <div class="details-content pr-2">
                @if($transaction_type == 'referral')
                <span class="text-gray">Agent:</span> <span class="font-weight-bold pl-2">{{ $agent_details -> first_name. ' ' . $agent_details -> last_name }}</span>
                @else
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
                            @if($property -> TransCoordinator_ID > 0)
                            <tr>
                                <td class="text-gray text-right">Trans Coord.</td>
                                <td class="font-weight-bold pl-2">{{ $property -> TransCoordinator_ID }}</td>
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
                            <tr>
                                <td class="text-gray text-right">Bright MLS ID</td>
                                <td class="font-weight-bold pl-2">{{ $property -> ListingId ?? null }}</td>
                            </tr>



                            @if($transaction_type == 'listing')
                                <tr>
                                    <td class="text-gray text-right">List Price</td>
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
                                    <td class="font-weight-bold pl-2">{{ $title_company }}</td>
                                </tr>

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
</div>
