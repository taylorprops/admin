<div class="table-responsive">
    <table class="table table-bordered table-sm" id="listings_table">
        <thead>
            <th width="100"></th>
            <th>Status</th>
            <th>Agent</th>
            <th>Address</th>
            <th>List Date</th>
            <th>Expiration Date</th>
            <th>Clients</th>
            <th>Checklist Status</th>
            <th></th>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)

                @php
                $status = '<span class="font-10">'.$transaction -> status -> resource_name.'</span>';
                $color = $transaction -> status -> resource_color;
                $contract = $transaction -> contract;
                $checklist = $transaction -> checklist;
                //$checklist_items = $checklist -> items;

                if($transaction -> DocsMissingCount > 0) {
                    $checklist_status = '<span class="text-danger"><i class="fal fa-exclamation-circle mr-2"></i> Missing Items</span>';
                } else {
                    $checklist_status = '<span class="text-success"><i class="fal fa-check mr-2"></i> Complete</span>';
                }

                if($contract) {
                    $close_date = $contract -> CloseDate;
                    if(stristr($status, 'Under Contract') || stristr($status, 'Closed')) {
                        $status .= ' - <a href="/agents/doc_management/transactions/transaction_details/'.$contract -> Contract_ID.'/contract">View</a><br><span class="text-gray font-8">Close Date: '.date_mdy($close_date).'</span>';
                    }
                }

                $listing_expired = $transaction -> ExpirationDate < date('Y-m-d') && $transaction -> Contract_ID == 0 ? 'text-danger' : '';

                @endphp
                <tr>
                    <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Listing_ID }}/listing" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View</a></td>
                    <td style="color: {{ $color }}">{!! $status !!}</td>
                    <td>{{ $transaction -> ListAgentFullName }}</td>
                    <td>{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</td>
                    <td>{{ date_mdy($transaction -> MlsListDate) }}</td>
                    <td class="{{ $listing_expired }}">{{ date_mdy($transaction -> ExpirationDate) }}</td>
                    <td>{{ $transaction -> SellerOneFullName }} @if($transaction -> SellerTwoFullName) <br>{{ $transaction -> SellerTwoFullName }} @endif</td>
                    <td>{!! $checklist_status !!}</td>
                    <td>
                        <div class="d-flex justify-content-around">
                            @if($transaction -> ListPictureURL)
                                <img src="{{ $transaction -> ListPictureURL }}" height="50">
                            @else
                                <i class="fad fa-home fa-3x text-primary"></i>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
