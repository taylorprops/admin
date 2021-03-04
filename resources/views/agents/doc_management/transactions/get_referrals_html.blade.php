<div class="table-responsive text-nowrap">

    <table class="table table-bordered table-sm" id="referrals_table">
        <thead>
            <th width="100"></th>
            <th>Status</th>
            <th>Agent</th>
            <th>Close Date</th>
            <th>Address</th>
            <th>Clients</th>
            <th>Receiving Agent</th>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                @php
                $status = '<span class="font-10">'.$transaction -> status -> resource_name.'</span>';
                $color = $transaction -> status -> resource_color;
                $our_agent = $transaction -> agent -> full_name;
                @endphp
                <tr>
                    <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Referral_ID }}/referral" class="btn btn-primary"><i class="fad fa-eye mr-2"></i> View</a></td>
                    <td style="color: {{ $color }}">{!! $status !!}</td>
                    <td>{{ $our_agent }}</td>
                    <td>{{ date_mdy($transaction -> CloseDate) }}</td>
                    <td>{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</td>
                    <td>{{ $transaction -> ClientFirstName.' '.$transaction -> ClientLastName }}</td>
                    <td>{{ $transaction -> ReceivingAgentFirstName.' '.$transaction -> ReceivingAgentLastName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
