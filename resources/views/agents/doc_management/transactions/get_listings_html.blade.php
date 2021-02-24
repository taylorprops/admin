<table class="table table-bordered table-sm" id="listings_table">
    <thead>
        <th></th>
        <th></th>
        <th>List Date</th>
        <th>Expiration Date</th>
        <th>Status</th>
        <th>Address</th>
        <th>Clients</th>
        <th></th>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)

            @php
            $status = $transaction -> status -> resource_name;
            $color = $transaction -> status -> resource_color;
            $contract = $transaction -> contract;
            if($contract) {
                $close_date = $contract -> CloseDate;
                if($status == 'Under Contract') {
                    $status .= '<br><span class="text-gray font-8">Closes: '.date_mdy($close_date).'</span>';
                }
            }
            @endphp
            <tr>
                <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Listing_ID }}/listing" class="btn btn-primary btn-sm"><i class="fad fa-eye mr-2"></i> View</a></td>
                <td>
                    <div class="d-flex justify-content-around">
                        @if($transaction -> ListPictureURL)
                            <img src="{{ $transaction -> ListPictureURL }}" height="50" class="img-responsive">
                        @else
                            <i class="fad fa-home fa-3x text-primary"></i>
                        @endif
                    </div>
                </td>
                <td>{{ date_mdy($transaction -> MlsListDate) }}</td>
                <td>{{ date_mdy($transaction -> ExpirationDate) }}</td>
                <td style="color: {{ $color }}">{!! $status !!}</td>
                <td>{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</td>
                <td>{{ $transaction -> SellerOneFullName }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
