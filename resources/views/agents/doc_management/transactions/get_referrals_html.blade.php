<div class="no-wrap">

    <table class="table table-hover table-bordered table-sm" id="referrals_table">
        <thead>
            <th>Address</th>
            <th>Status</th>
            <th>Agent</th>
            <th>Close Date</th>
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
                    <td><a href="/agents/doc_management/transactions/transaction_details/{{ $transaction -> Referral_ID }}/referral" class="d-block h-100 line-height-px-50">{{ $transaction -> FullStreetAddress.' '.$transaction -> City.', '.$transaction -> StateOrProvince.' '.$transaction -> PostalCode }}</a></td>
                    <td style="color: {{ $color }}">{!! $status !!}</td>
                    <td>{{ $our_agent }}</td>
                    <td>{{ date_mdy($transaction -> CloseDate) }}</td>
                    <td>{{ $transaction -> ClientFirstName.' '.$transaction -> ClientLastName }}</td>
                    <td>{{ $transaction -> ReceivingAgentFirstName.' '.$transaction -> ReceivingAgentLastName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
