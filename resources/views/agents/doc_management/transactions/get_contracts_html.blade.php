<div class="no-wrap">

    <table class="table table-hover table-bordered table-sm" style="width: 100%;" id="contracts_table">
        <thead>
            <th>Address</th>
            <th>Status</th>
            <th>Agent</th>
            <th>Contract Date</th>
            <th>Settle Date</th>
            <th>Clients</th>
            <th>Checklist Status</th>
            <th><span class="hidden">Image</span></th>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)

                @php
                $status = '<span class="font-10">'.$transaction -> status -> resource_name.'</span>';
                $color = $transaction -> status -> resource_color;
                $checklist = $transaction -> checklist;
                //$checklist_items = $checklist -> items;

                if($transaction -> DocsMissingCount > 0) {
                    $checklist_status = '<span class="text-danger"><i class="fal fa-exclamation-circle mr-2"></i> Missing Items</span>';
                } else {
                    $checklist_status = '<span class="text-success"><i class="fal fa-check mr-2"></i> Complete</span>';
                }

                $clients = $transaction -> BuyerOneFullName;
                if($transaction -> BuyerTwoFullName) {
                    $clients .= '<br>'.$transaction -> BuyerTwoFullName;
                }
                // if our listing our clients are the sellers
                $listing = $transaction -> listing;
                $our_agent = $transaction -> BuyerAgentFullName;

                if($listing) {
                    $clients = $listing -> SellerOneFullName;
                    if($listing -> SellerTwoFullName) {
                        $clients .= '<br>'.$listing -> SellerTwoFullName;
                    }
                    $our_agent = $transaction -> ListAgentFullName;
                }


                $past_settle_date = $transaction -> CloseDate < date('Y-m-d') && $transaction -> Status != $contract_closed_status ? 'text-danger' : '';

                @endphp
                <tr>
                    <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Contract_ID }}/contract" class="d-block h-100 line-height-px-50">{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</a></td>
                    <td><span style="color: {{ $color }}">{!! $status !!}</span>
                        <br>{{ ucwords($transaction -> SaleRent) }}</td>
                    <td>{{ $our_agent }}</td>
                    <td>{{ date_mdy($transaction -> ContractDate) }}</td>
                    <td class="{{ $past_settle_date }}">{{ date_mdy($transaction -> CloseDate) }}</td>
                    <td>{!! $clients !!}</td>
                    <td>{!! $checklist_status !!}</td>
                    <td>
                        <div class="d-flex justify-content-around">
                            @if($transaction -> ListPictureURL)
                                <img src="{{ $transaction -> ListPictureURL }}" height="50" class="rounded">
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

