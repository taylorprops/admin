<div class="bg-white p-3" id="earnest_search_results_div">

        <div class="row">

            @if(count($checks_in) > 0)

                <div class="col-12">

                    <div class="earnest-search-results-container">

                        <h4 class="text-orange">Checks In</h4>

                        <div class="table-responsive-lg font-8">

                            <table class="table table-bordered table-sm">

                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Account</th>
                                        <th>Status</th>
                                        <th>Deposited</th>
                                        <th>Amount</th>
                                        <th>Number</th>
                                        <th>Name On Check</th>
                                        <th>Property</th>
                                        <th>Agent</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($checks_in as $check)

                                        @php
                                        $agent_name = $check -> agent -> full_name;
                                        $property = $check -> property;
                                        $earnest = $check -> earnest;
                                        $address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                                        @endphp
                                        <tr>
                                            <td><a href="/agents/doc_management/transactions/transaction_details/{{ $check -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a></td>
                                            <td><a href="{{ $check -> file_location }}" target="_blank">View Check</a></td>
                                            <td>{{ get_initials($earnest -> earnest_account -> resource_name).' - '.$earnest -> earnest_account -> resource_state }}</td>
                                            <td>{{ ucwords($check -> check_status) }}</td>
                                            <td>{{ $check -> date_deposited }}</td>
                                            <td class="font-weight-bold text-orange">${{ number_format($check -> check_amount, 2) }}</td>
                                            <td>{{ $check -> check_number }}</td>
                                            <td>{{ $check -> check_name }}</td>
                                            <td>{!! $address !!}</td>
                                            <td>{{ $agent_name }}</td>
                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @endif

            @if(count($checks_out) > 0)

                <div class="col-12">

                    <div class="earnest-search-results-container">

                        <h4 class="text-orange">Checks Out</h4>

                        <div class="table-responsive-lg font-8">

                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Account</th>
                                        <th>Status</th>
                                        <th>Sent</th>
                                        <th>Amount</th>
                                        <th>Number</th>
                                        <th>Payable To</th>
                                        <th>Property</th>
                                        <th>Agent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($checks_out as $check)

                                        @php
                                        $agent_name = $check -> agent -> full_name;
                                        $property = $check -> property;
                                        $earnest = $check -> earnest;
                                        $address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                                        @endphp
                                        <tr>
                                            <td><a href="/agents/doc_management/transactions/transaction_details/{{ $check -> Contract_ID }}/contract?tab=earnest" target="_blank">View Details</a></td>
                                            <td><a href="{{ $check -> file_location }}" target="_blank">View Check</a></td>
                                            <td>{{ get_initials($earnest -> earnest_account -> resource_name).' - '.$earnest -> earnest_account -> resource_state }}</td>
                                            <td>{{ ucwords($check -> check_status) }}</td>
                                            <td>{{ $check -> date_sent }}</td>
                                            <td class="font-weight-bold text-orange">${{ number_format($check -> check_amount, 2) }}</td>
                                            <td>{{ $check -> check_number }}</td>
                                            <td>{{ $check -> payable_to }}</td>
                                            <td>{!! $address !!}</td>
                                            <td>{{ $agent_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>

            @endif

        </div>

</div>
