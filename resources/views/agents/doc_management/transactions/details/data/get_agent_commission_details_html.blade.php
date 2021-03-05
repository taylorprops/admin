@if($breakdown -> submitted == 'yes')

    <div class="row">
        <div class="col-12">
            <div class="font-12 text-success">Income</div>
        </div>
    </div>

    <div class="row d-flex align-items-center">
        <div class="col-8">
            Commission Check Amount
        </div>
        <div class="col-4 d-flex justify-content-end d-flex justify-content-end">
            ${{ number_format($breakdown -> checks_in_total, 2) ?? '0.00' }}
        </div>
    </div>

    @if($for_sale)

        <div class="row d-flex align-items-center">
            <div class="col-8">
                Admin Fee From Title Company
            </div>
            <div class="col-4 d-flex justify-content-end">
                ${{ number_format($breakdown -> admin_fee_in_total, 2) ?? '$0.00' }}
            </div>
        </div>

        @if($holding_earnest)
            <div class="row d-flex align-items-center">
                <div class="col-8">
                    Money In Escrow
                </div>
                <div class="col-4 d-flex justify-content-end">
                    ${{ number_format($breakdown -> earnest_deposit_amount, 2) }}
                </div>
            </div>
        @endif

    @endif

    <div class="row d-flex align-items-center">
        <div class="col-8 font-weight-bold font-10">
            Total In
        </div>
        <div class="col-4 d-flex justify-content-end font-weight-bold">
            ${{ number_format($breakdown -> total_income, 2) ?? '0.00' }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <hr>
        </div>
    </div>

    @if($for_sale || $is_rental)
        <div class="row d-flex align-items-center client-paid-admin">
            <div class="col-8">
                Admin Fee Paid By Client
            </div>
            <div class="col-4 d-flex justify-content-end">
                ${{ number_format($breakdown -> admin_fee_from_client, 2) ?? '0.00' }}
            </div>
        </div>

    @endif

    <div class="row d-flex align-items-center client-paid-admin mt-3">
        <div class="col-8 text-success font-10">
            Commission In Total
        </div>
        <div class="col-4 d-flex justify-content-end font-weight-bold">
            ${{ number_format($breakdown -> sub_total, 2) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="font-12 text-danger">Deductions</div>
        </div>
    </div>

    @if($agent_commission_deduction_percent > 0)
        <div class="row d-flex align-items-center">
            <div class="col-8">
                Agent Commission Deduction - {{ $agent_commission_deduction_percent }}%
            </div>
            <div class="col-4 d-flex justify-content-end">
                ${{ number_format($breakdown -> agent_commission_deduction, 2) ?? '0.00' }}
            </div>
        </div>

    @endif

    @if(!$is_referral_company)
        <div class="row d-flex align-items-center">
            <div class="col-8">
                Admin Fee Paid By Agent
            </div>
            <div class="col-4 d-flex justify-content-end">
                ${{ number_format($breakdown -> admin_fee_from_agent, 2) }}
            </div>
        </div>

    @endif

    @if($is_referral_company)
        {{-- 15% referral fee for referral company agents --}}
        <div class="row d-flex align-items-center">
            <div class="col-8">
                15% Transaction Fee
            </div>
            <div class="col-4 d-flex justify-content-end">
                ${{ number_format($referral_company_deduction, 2) }}
            </div>
        </div>
    @endif

    <div class="row">

        <div class="col-12">

            <div class="border-top border-bottom py-2 mb-3">

                @php
                $deductions = $breakdown -> deductions;
                @endphp

                @if(count($deductions) > 0)

                    @foreach($deductions as $deduction)

                        <div class="row d-flex align-items-center no-gutters deduction-row">
                            <div class="col-7 deduction-description">
                                {{ $deduction -> description }}
                            </div>
                            <div class="col-5 d-flex justify-content-end deduction-amount">
                                ${{ number_format($deduction -> amount, 2) }}
                            </div>
                        </div>

                    @endforeach

                @endif

                <a href="javascript:void(0)" class="export-deductions-button"><i class="fal fa-plus mr-2"></i> Add Deductions To Breakdown</a>

            </div>

        </div>

    </div>

    <div class="row d-flex align-items-center">
        <div class="col-8">
            <span class="text-danger font-10">Deductions Total</span>
        </div>
        <div class="col-4 d-flex justify-content-end font-weight-bold">
            ${{ number_format($breakdown -> commission_deductions_total, 2) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <hr class="my-3">
        </div>
    </div>

    <div class="row d-flex align-items-center border-top mt-3 p-4 bg-success text-white ">
        <div class="col-8">
            <span class="font-weight-bold font-11">Total Commission To Agent</span>
        </div>
        <div class="col-4 d-flex justify-content-end font-weight-bold">
            ${{ number_format($breakdown -> total_commission_to_agent, 2) }}
        </div>
    </div>

    <div class="row">

        <div class="col-12">

            <div class="font-12 text-orange mt-4 mb-2">Commission Check Details</div>

            <div class="row d-flex align-items-center">
                <div class="col-8">
                    Make Check Payable To
                </div>
                <div class="col-4 d-flex justify-content-end">
                    {{ $breakdown -> check_payable_to }}
                </div>
            </div>

            <div class="row d-flex align-items-center">
                <div class="col-8">
                    Check Delivery Method
                </div>
                <div class="col-4 d-flex justify-content-end">
                    {{ ucwords($breakdown -> delivery_method) }}
                </div>
            </div>

            @if($breakdown -> delivery_method == 'mail' || $breakdown -> delivery_method == 'fedex')

                <div class="row">
                    <div class="col-4">
                        Mail To
                    </div>
                    <div class="col-8 d-flex justify-content-end">
                        {{ $breakdown -> check_mail_to_street }} {{ $breakdown -> check_mail_to_city }}, {{ $breakdown -> check_mail_to_state }} {{ $breakdown -> check_mail_to_zip }}
                    </div>
                </div>

            @endif

        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <hr class="my-3">
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            Notes
            <br>
            {!! nl2br($breakdown -> notes) !!}
        </div>
    </div>

@else

    <div class="row">
        <div class="col-12 my-5">
            <div class="font-10 text-orange"><i class="fal fa-exclamation-triangle mr-2"></i> Commission Breakdown Not Submitted</div>
        </div>
    </div>

@endif
