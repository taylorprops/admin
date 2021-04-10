<div class="no-wrap">
    <table class="table table-hover table-bordered table-sm" id="listings_table">
        <thead>
            <th>Address</th>
            <th>Status</th>
            <th>Agent</th>
            <th>List Date</th>
            <th>Expiration Date</th>
            <th>Clients</th>
            <th>Checklist Status</th>
            <th><span class="hidden">Image</span></th>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)

                @php
                $status = $transaction -> status -> resource_name;
                $status_html = '<span class="font-10">'.$transaction -> status -> resource_name.'</span>';
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
                    if(stristr($status_html, 'Under Contract') || stristr($status_html, 'Closed')) {
                        $status_html = '<a href="/agents/doc_management/transactions/transaction_details/'.$contract -> Contract_ID.'/contract" class="text-success">'.$status.'</a><br><span class="text-gray font-8">Close Date: '.date_mdy($close_date).'</span>';
                    }
                }

                $listing_expired = $transaction -> ExpirationDate < date('Y-m-d') && $transaction -> Contract_ID == 0 ? 'text-danger' : '';

                $sale_rent = ucwords($transaction -> SaleRent);
                if($transaction -> SaleRent == 'both') {
                    $sale_rent = 'For Sale and Rent';
                } else if($transaction -> SaleRent == 'sale') {
                    $sale_rent = 'For Sale';
                }

                @endphp
                <tr>
                    <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Listing_ID }}/listing" class="d-block h-100 line-height-px-50">{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</a></td>
                    <td><span style="color: {{ $color }}">{!! $status_html !!}</span>
                    <br>{{ $sale_rent }}</td>
                    <td>{{ $transaction -> ListAgentFullName }}</td>
                    <td>{{ date_mdy($transaction -> MlsListDate) }}</td>
                    <td class="{{ $listing_expired }}">{{ date_mdy($transaction -> ExpirationDate) }}</td>
                    <td>{{ $transaction -> SellerOneFullName }} @if($transaction -> SellerTwoFullName) <br>{{ $transaction -> SellerTwoFullName }} @endif</td>
                    <td>{!! $checklist_status !!}</td>
                    <td>
                        <div class="d-flex justify-content-around">
                            @if($transaction -> ListPictureURL)
                                <img src="{{ $transaction -> ListPictureURL }}" class="rounded" height="50">
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
