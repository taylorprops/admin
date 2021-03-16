<div class="row mt-5">
    <div class="col-12">

        <div class="d-flex justify-content-start">
            <div class="mr-2">
                <h4 class="text-orange">Commission Checks Queue</h4>
            </div>
            <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Commission Checks" data-content="These are checks for commissions paid on Sales Contracts, Lease Agreements and Referral Agreements.<br><br>Checks in this list are waiting for a property to be added to the system. Once the property has been added you can easily import this check by adding a check in the Commission tab."><i class="fad fa-question-circle ml-2"></i></a>
        </div>
        <table class="table table-hover table-bordered table-sm checks-queue-table">
            <thead>
                <tr>
                    <th class="wpx-80 text-primary">Edit</th>
                    <th>Received</th>
                    <th>Agent</th>
                    <th>Property Address</th>
                    <th>Check #</th>
                    <th>Check Date</th>
                    <th>Amount</th>
                    <th class="wpx-80"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($checks as $check)
                    @if($check -> active == 'yes')
                        <tr>
                            <td><a href="javascript: void(0)" class="btn btn-primary btn-sm btn-block m-0 edit-queue-check-button sale"
                                data-check-id="{{ $check -> id }}"
                                data-check-agent-id="{{ $check -> Agent_ID }}"
                                data-date-received="{{ $check -> date_received }}"
                                data-date-deposited="{{ $check -> date_deposited }}"
                                data-check-date="{{ $check -> check_date }}"
                                data-check-number="{{ $check -> check_number }}"
                                data-check-amount="{{ $check -> check_amount }}"
                                data-image-location="{{ $check -> image_location }}"
                                data-street="{{ $check -> street }}"
                                data-city="{{ $check -> city }}"
                                data-state="{{ $check -> state }}"
                                data-zip="{{ $check -> zip }}"
                                >
                                    <i class="fad fa-edit mr-2"></i> Edit
                                </a>
                            </td>
                            <td class="text-center">{{ $check -> date_received }}</td>
                            <td>@if($check -> agent){{ $check -> agent -> first_name.' '.$check -> agent -> last_name }}@endif</td>
                            <td>{{ $check -> street.' '.$check -> city.', '.$check -> state.' '.$check -> zip }}</td>
                            <td class="text-right">{{ $check -> check_number }}</td>
                            <td class="text-center">{{ $check -> check_date }}</td>
                            <td class="text-right">${{ number_format($check -> check_amount, 2) }}</td>
                            <td><a href="javascript: void(0)" class="btn btn-danger btn-sm btn-block m-0 delete-check-button" data-check-id="{{ $check -> id }}" data-type="sale"><i class="fal fa-times mr-2"></i> Delete</a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>
</div>


<div class="row mt-5">
    <div class="col-12">

        <div class="d-flex justify-content-start">
            <div class="mr-2">
                <h4 class="text-orange">BPO, Agent Fee and Other Checks Queue</h4>
            </div>
            <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Other Checks" data-content="These are checks for anything not involving commission for a property that was for sale or lease.<br><br>Once the Agent has been added an option to open the Commission Breakdown will be available.<br><br>Once the checks out have been written and the remainder of this check is $0, it will no longer appear in this list."><i class="fad fa-question-circle ml-2"></i></a>
        </div>
        <table class="table table-hover table-bordered table-sm checks-queue-table">
            <thead>
                <tr>
                    <th class="wpx-100 text-primary">View</th>
                    <th>Received</th>
                    <th>Agent</th>
                    <th>Client</th>
                    <th>Property Address</th>
                    <th>Check #</th>
                    <th>Check Date</th>
                    <th>Amount</th>
                    <th class="wpx-80"></th>
                </tr>
            </thead>
            <tbody>
                @if(count($checks_other) > 0)
                    @foreach($checks_other as $check_other)
                        @if($check_other -> other_checks[0] -> active == 'yes')
                            <tr>
                                <td>
                                    @if($check_other -> agent)
                                        <a href="/doc_management/commission_other/{{ $check_other -> id }}" class="btn btn-primary btn-sm btn-block m-0"><i class="fad fa-sack-dollar mr-2"></i> Breakdown</a>
                                    @else
                                    <a href="javascript: void(0)" class="btn btn-primary btn-sm btn-block m-0 edit-queue-check-button other"
                                        data-commission-id="{{ $check_other -> other_checks[0] -> Commission_ID }}"
                                        data-check-id="{{ $check_other -> other_checks[0] -> id }}"
                                        data-check-agent-id="{{ $check_other -> other_checks[0] -> Agent_ID }}"
                                        data-date-received="{{ $check_other -> other_checks[0] -> date_received }}"
                                        data-date-deposited="{{ $check_other -> other_checks[0] -> date_deposited }}"
                                        data-check-date="{{ $check_other -> other_checks[0] -> check_date }}"
                                        data-check-number="{{ $check_other -> other_checks[0] -> check_number }}"
                                        data-check-amount="{{ $check_other -> other_checks[0] -> check_amount }}"
                                        data-image-location="{{ $check_other -> other_checks[0] -> image_location }}"
                                        data-street="{{ $check_other -> other_checks[0] -> street }}"
                                        data-city="{{ $check_other -> other_checks[0] -> city }}"
                                        data-state="{{ $check_other -> other_checks[0] -> state }}"
                                        data-zip="{{ $check_other -> other_checks[0] -> zip }}"
                                        data-client-name="{{ $check_other -> other_checks[0] -> client_name }}">
                                        <i class="fad fa-edit mr-2"></i> Edit
                                    </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @foreach($check_other -> other_checks as $other_check)
                                        {{ $other_check -> date_received }}
                                        @if(!$loop -> last) <br> @endif
                                    @endforeach
                                </td>
                                <td>@if($check_other -> agent){{ $check_other -> agent -> first_name.' '.$check_other -> agent -> last_name }}@endif</td>
                                <td>
                                    @foreach($check_other -> other_checks as $other_check)
                                        {{ $other_check -> client_name }}
                                        @if(!$loop -> last && $other_check -> client_name != '') <br> @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($check_other -> other_checks as $other_check)
                                        {{ $other_check -> street.' '.$other_check -> city.', '.$other_check -> state.' '.$other_check -> zip }}
                                        @if(!$loop -> last) <br> @endif
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    @foreach($check_other -> other_checks as $other_check)
                                        {{ $other_check -> check_number }}
                                        @if(!$loop -> last) <br> @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach($check_other -> other_checks as $other_check)
                                        {{ $other_check -> check_date }}
                                        @if(!$loop -> last) <br> @endif
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    @foreach($check_other -> other_checks as $other_check)
                                        ${{ number_format($other_check -> check_amount, 2) }}
                                        @if(!$loop -> last) <br> @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(!$check_other -> agent)
                                        <a href="javascript: void(0)" class="btn btn-danger btn-sm btn-block m-0 delete-check-button" data-check-id="{{ $check_other -> other_checks[0] -> id }}" data-type="other"><i class="fal fa-times mr-2"></i> Delete</a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </tbody>
        </table>

    </div>
</div>


