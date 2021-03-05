<div class="table-responsive text-nowrap">

    <table class="table table-bordered table-sm earnest-table {{ $tab }}" width="100%">

        <thead>
            <tr>
                @if($tab == 'missing')
                    <th class="text-center pl-0">
                        <input type="checkbox" class="custom-form-element form-checkbox check-all" data-label="">
                    </th>
                @endif
                <th></th>
                <th>Account</th>
                <th>Agent</th>
                <th>Property</th>
                <th>Received</th>
                <th>Contract Date</th>
                <th>CloseDate</th>
                <th>Status</th>
                @if($tab == 'missing')
                    <th>Last Emailed</th>
                    <th></th>
                @endif
            </tr>
        </thead>

        <tbody>

            @foreach($contracts as $contract)

                @php
                $agent = $contract -> agent -> full_name;
                $status = $contract -> status -> resource_name;
                $earnest = $contract -> earnest;
                $earnest_account = $earnest -> earnest_account;
                @endphp

                <tr>
                    @if($tab == 'missing')
                        <td class="text-center">
                            <input type="checkbox" class="custom-form-element form-checkbox deposit-input" data-contract-id="{{ $contract -> Contract_ID }}" data-label="">
                        </td>
                    @endif
                    <td>
                        <a class="btn btn-primary" href="/agents/doc_management/transactions/transaction_details/{{ $contract -> Contract_ID }}/contract?tab=earnest" target="_blank"><i class="fal fa-eye mr-2"></i> View</a>
                    </td>
                    <td>{{ $earnest_account -> resource_state.' - '.$earnest_account -> resource_name }}</td>
                    <td>{{ $agent }}</td>
                    <td>{{ $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode }}</td>
                    <td>${{ number_format($earnest -> amount_received) }}</td>
                    <td>{{ date_mdy($contract -> ContractDate) }}</td>
                    <td>{{ date_mdy($contract -> CloseDate) }}</td>
                    <td>{{ $status }}</td>
                    @if($tab == 'missing')
                        <td>
                            @if($earnest -> last_emailed_date != '' && $earnest -> last_emailed_date != '0000-00-00') {{ date_mdy($earnest -> last_emailed_date) }} @endif
                        </td>
                        <td>
                            <button class="btn btn-primary email-agent single"><i class="fal fa-envelope mr-2"></i> Email Agent</button>
                        </td>
                    @endif
                </tr>

            @endforeach

        </tbody>

    </table>

</div>
