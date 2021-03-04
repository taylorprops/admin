<div class="table-responsive text-nowrap">

    <table class="table table-bordered table-sm earnest-table" width="100%">

        <thead>
            <tr>
                <th width="100"></th>
                <th>Account</th>
                <th>Agent</th>
                <th>Property</th>
                <th>Received</th>
                <th>Contract Date</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @foreach($contracts as $contract)

                @php
                $agent = $contract -> agent -> full_name;
                $status = $contract -> status -> resource_name;
                $earnest = $contract -> earnest;
                @endphp

                @if($earnest)

                    @php $earnest_account = $earnest -> earnest_account; @endphp

                    {{-- @if($earnest -> amount_total > 0) --}}

                        <tr>
                            <td><a class="btn btn-primary" href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract?tab=earnest"><i class="fal fa-eye mr-2"></i> View</a></td>
                            <td>{{ $earnest_account -> resource_state.' - '.$earnest_account -> resource_name }}</td>
                            <td>{{ $agent }}</td>
                            <td>{{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}</td>
                            <td>${{ number_format($earnest -> amount_received) }}</td>
                            <td>{{ date_mdy($contract -> ContractDate) }}</td>
                            <td>{{ $status }}</td>
                        </tr>

                   {{--  @endif --}}

                @endif

            @endforeach

        </body>

    </table>

</div>
