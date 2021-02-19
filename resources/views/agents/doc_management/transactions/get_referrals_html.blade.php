<table class="table table-bordered table-sm" id="referrals_table">
    <thead>
        <th></th>
        <th>Address</th>
        <th>Clients</th>
        <th></th>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Referral_ID }}/referral" class="btn btn-primary btn-sm">View</a></td>
                <td>{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
