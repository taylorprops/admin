<div class="row mt-5">
    <div class="col-12">

        <div class="d-flex justify-content-start">
            <div class="mr-2">
                <h4 class="text-orange">Pending Commission Breakdowns</h4>
            </div>
            <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Pending Commission Breakdowns" data-content="These are commissions that:<br><br>A: The Agent has submitted a commission breakdown<br>B: We have received commission checks<br><br>This includes all commissions for all checks received whether they are sales commission, rental commission, referral commission, BPO payments, etc."><i class="fad fa-question-circle ml-2"></i></a>
        </div>
        <table class="table table-hover table-bordered table-sm commissions-pending-table">
            <thead>
                <tr>
                    <th class="wpx-125 text-primary">View</th>
                    <th class="wpx-100">Settle Date</th>
                    <th>Agent</th>
                    <th>Property Address/Client</th>
                    <th>CB Submitted</th>
                    <th class="wpx-120">Amount Received</th>
                    <th class="wpx-100">In Earnest</th>
                    <th class="wpx-100">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commissions as $commission)

                    @php
                    $type = $commission -> Contract_ID > 0 ? 'contract' : 'referral';
                    $id = $commission -> Contract_ID > 0 ? $commission -> Contract_ID : $commission -> Referral_ID;
                    $link = $commission -> commission_type == 'other' ? '/doc_management/commission_other/'.$commission -> id : '/agents/doc_management/transactions/transaction_details/'.$id.'/'.$type.'?tab=commission';
                    if($type == 'contract') {
                        $property = $commission -> property_contract;
                        $details = ucwords(strtolower($property -> FullStreetAddress)).' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                        $close_date = $commission -> close_date;
                    } else {
                        $property = $commission -> property_referral;
                        $details = $property -> ClientFirstName.' '.$property -> ClientLastName.' - '.ucwords(strtolower($property -> FullStreetAddress)).' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                        $close_date = date('Y-m-d', strtotime($property -> created_at));
                    }
                    @endphp

                    <tr>
                        <td>
                            <a href="{{ $link }}" class="btn btn-primary btn-sm btn-block m-0"><i class="fad fa-sack-dollar mr-2"></i> Breakdown</a>
                        </td>
                        <td class="text-center">{{ $close_date }}</td>
                        <td>{{ $commission -> agent -> full_name }}</td>
                        <td>{{ $details }}</td>
                        <td class="text-center">@if($commission -> breakdown -> submitted == 'yes') <i class="fal fa-check text-success"></i> @else <i class="fal fa-minus text-danger"></i> @endif</td>
                        <td class="text-right">${{ number_format($commission -> checks_in_total, 2) }}</td>
                        <td class="text-right">@if($commission -> Contract_ID > 0) {{ number_format($commission -> earnest_deposit_amount, 2) }} @else -Referral @endif</td>
                        <td class="text-right">${{ number_format($commission -> checks_out_total, 2) }}</td>
                    </tr>

                @endforeach
            </tbody>
        </table>

    </div>
</div>
