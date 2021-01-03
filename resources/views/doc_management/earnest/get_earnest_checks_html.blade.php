@foreach($accounts as $account)

    @php
    $earnests = $account -> earnest() -> get();
    @endphp

    <div class="tab-pane fade pl-3 account-container @if($loop -> first) show active @endif"
        id="account_tab_content_{{ $account -> resource_id }}"
        role="tabpanel"
        aria-labelledby="account_tab_{{ $account -> resource_id }}">

        <h4 class="text-primary mb-3">{{ $account -> resource_state.' - '.$account -> resource_account_number.' - '.$account -> resource_name }}</h4>

        <h5 class="text-orange">Checks In</h5>

        <table class="earnest-checks-table-in table table-bordered table-sm">

            <thead>
                <tr>
                    <th></th>
                    <th>Deposited</th>
                    <th>Amount</th>
                    <th>Number</th>
                    <th>Name On Check</th>
                    <th>Property</th>
                    <th>Agent</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($earnests as $earnest)

                @php
                $checks = $earnest -> checks() -> where('check_status', 'pending') -> where('check_type', 'in') -> where('active', 'yes') -> orderBy('date_deposited', 'DESC') -> get();
                $agent_name = $earnest -> agent() -> first() -> full_name;
                $property = $earnest -> property() -> first();
                $address = $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                @endphp

                @foreach($checks as $check)

                    <tr class="check-row">
                        <td>
                            <div class="text-success">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="cleared"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Cleared">
                            </div>
                        </td>
                        <td>{{ $check -> date_deposited }}</td>
                        <td class="font-weight-bold text-orange">${{ number_format($check -> check_amount, 2) }}</td>
                        <td>{{ $check -> check_number }}</td>
                        <td>{{ $check -> check_name }}</td>
                        <td>{!! $address !!}</td>
                        <td>{{ $agent_name }}</td>
                        <td>
                            <a href="{{ $check -> file_location }}" target="_blank">View Check</a><br>
                            <a href="/agents/doc_management/transactions/transaction_details/{{ $earnest -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a>
                        </td>
                        <td>
                            <div class="text-danger">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="bounced"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Bounced">
                            </div>
                        </td>
                    </tr>

                @endforeach

            @endforeach

            </tbody>

        </table>


        <h5 class="text-orange mt-5">Checks Out</h5>

        <table class="earnest-checks-table-out table table-bordered table-sm">

            <thead>
                <tr>
                    <th></th>
                    <th>Sent</th>
                    <th>Amount</th>
                    <th>Number</th>
                    <th>Payable To</th>
                    <th>Property</th>
                    <th>Agent</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($earnests as $earnest)

                @php
                $checks = $earnest -> checks() -> where('check_status', 'pending') -> where('check_type', 'out') -> where('active', 'yes') -> orderBy('date_deposited', 'DESC') -> get();
                $agent_name = $earnest -> agent() -> first() -> full_name;
                $property = $earnest -> property() -> first();
                $address = $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                @endphp

                @foreach($checks as $check)

                    <tr class="check-row">
                        <td>
                            <div class="text-success">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="cleared"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Cleared">
                            </div>
                        </td>
                        <td>{{ $check -> date_sent }}</td>
                        <td class="font-weight-bold text-orange">${{ number_format($check -> check_amount, 2) }}</td>
                        <td class="font-weight-bold text-orange">{{ $check -> check_number }}</td>
                        <td>{{ $check -> payable_to }}</td>
                        <td>{!! $address !!}</td>
                        <td>{{ $agent_name }}</td>
                        <td>
                            <a href="{{ $check -> file_location }}" target="_blank">View Check</a><br>
                            <a href="/agents/doc_management/transactions/transaction_details/{{ $earnest -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a>
                        </td>
                    </tr>

                @endforeach

            @endforeach

            </tbody>

        </table>

        <hr class="bg-primary earnest-check-hr my-5">

        <h4 class="text-primary mb-3">Recently Cleared</h4>

        <h5 class="text-orange">Checks In</h5>

        <table class="earnest-checks-table-in-recent table table-bordered table-sm">

            <thead>
                <tr>
                    <th></th>
                    <th>Cleared</th>
                    <th>Amount</th>
                    <th>Number</th>
                    <th>Name On Check</th>
                    <th>Property</th>
                    <th>Agent</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($earnests as $earnest)

                @php
                $checks = $earnest -> checks() -> where('check_status', '!=', 'pending') -> where('check_type', 'in') -> where('active', 'yes') -> orderBy('date_cleared', 'DESC') -> get();
                $agent_name = $earnest -> agent() -> first() -> full_name;
                $property = $earnest -> property() -> first();
                $address = $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                @endphp

                @foreach($checks as $check)

                    <tr class="check-row {{ $check -> check_status }}">
                        <td>
                            <div class="text-success">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="cleared"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Cleared"
                                @if($check -> check_status == 'cleared') checked @endif>
                            </div>
                        </td>
                        <td>{{ $check -> date_cleared }}</td>
                        <td class="font-weight-bold">${{ number_format($check -> check_amount, 2) }}</td>
                        <td>{{ $check -> check_number }}</td>
                        <td>{{ $check -> check_name }}</td>
                        <td>{!! $address !!}</td>
                        <td>{{ $agent_name }}</td>
                        <td>
                            <a href="{{ $check -> file_location }}" target="_blank">View Check</a><br>
                            <a href="/agents/doc_management/transactions/transaction_details/{{ $earnest -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a>
                        </td>
                        <td>
                            <div class="text-danger">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="bounced"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Bounced"
                                @if($check -> check_status == 'bounced') checked @endif>
                            </div>
                        </td>
                    </tr>

                @endforeach

            @endforeach

            </tbody>

        </table>


        <h5 class="text-orange mt-5">Checks Out</h5>

        <table class="earnest-checks-table-out-recent table table-bordered table-sm">

            <thead>
                <tr>
                    <th></th>
                    <th>Cleared</th>
                    <th>Amount</th>
                    <th>Number</th>
                    <th>Payable To</th>
                    <th>Property</th>
                    <th>Agent</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($earnests as $earnest)

                @php
                $checks = $earnest -> checks() -> where('check_status', '!=', 'pending') -> where('check_type', 'out') -> where('active', 'yes') -> orderBy('date_cleared', 'DESC') -> get();
                $agent_name = $earnest -> agent() -> first() -> full_name;
                $property = $earnest -> property() -> first();
                $address = $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                @endphp

                @foreach($checks as $check)

                    <tr class="check-row {{ $check -> check_status }}">
                        <td>
                            <div class="text-success">
                                <input type="checkbox" class="custom-form-element form-checkbox cleared-checkbox"
                                value="cleared"
                                data-earnest-id="{{ $earnest -> id }}"
                                data-check-id="{{ $check -> id }}"
                                data-check-type="{{ $check -> check_type }}"
                                data-label="Cleared"
                                @if($check -> check_status == 'cleared') checked @endif>
                            </div>
                        </td>
                        <td>{{ $check -> date_cleared }}</td>
                        <td class="font-weight-bold">${{ number_format($check -> check_amount, 2) }}</td>
                        <td>{{ $check -> check_number }}</td>
                        <td>{{ $check -> payable_to }}</td>
                        <td>{!! $address !!}</td>
                        <td>{{ $agent_name }}</td>
                        <td>
                            <a href="{{ $check -> file_location }}" target="_blank">View Check</a><br>
                            <a href="/agents/doc_management/transactions/transaction_details/{{ $earnest -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a>
                        </td>
                    </tr>

                @endforeach

            @endforeach

            </tbody>

        </table>

    </div>

@endforeach
