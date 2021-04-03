<div class="no-wrap">

    <table id="commissions_table" class="table table-hover table-bordered table-sm" width="100%">

        <thead>
            <tr>
                <th>Address</th>
                <th>Close Date</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody>

            @foreach($contracts as $contract)
                @php
                $commission = $contract -> commission;
                $breakdown = $contract -> commission_breakdown;
                $status = 'Breakdown Not Submitted';
                if($breakdown -> submitted == 'yes') {
                    $status = 'Breakdown Submitted';
                }
                if($breakdown -> status == 'reviewed') {
                    $status = 'In Review';
                } else if($breakdown -> status == 'complete') {
                    $status = 'Complete';
                }

                if($commission -> amount_to_agent > 0) {
                    $amount_to_agent = '$'.number_format($commission) -> amount_to_agent;
                } else {
                    if($breakdown -> total_commission_to_agent > 0) {
                        $amount_to_agent = '$'.number_format($breakdown -> total_commission_to_agent);
                    } else {
                        $amount_to_agent = 'N/A';
                    }
                }

                @endphp
                <tr>
                    <td>
                        <a href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract" class="d-block h-100">
                            {{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}
                        </a>
                    </td>
                    <td>{{ $contract -> CloseDate }}</td>
                    <td>{!! $status !!}</td>
                    <td>{{ $amount_to_agent }}</td>
                </tr>

            @endforeach

        </body>

    </table>

</div>
