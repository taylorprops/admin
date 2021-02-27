<div class="row">
    <div class="col-12">
        <div class="font-12 text-orange">Income</div>
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
    <div class="col-4 d-flex justify-content-end">
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

<div class="row d-flex align-items-center client-paid-admin {{-- @if(!$is_rental) hidden @endif --}}">
    <div class="col-8 text-success font-10">
        Commission In Total
    </div>
    <div class="col-4 d-flex justify-content-end">
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
        <div class="font-12 text-orange">Deductions</div>
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
    <div class="row d-flex align-items-center agent-paid-admin">
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
    <div class="row d-flex align-items-center agent-paid-admin">
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

        <div class="row">
            <div class="col-12">
                <input type="checkbox" class="custom-form-element form-checkbox" disabled data-label="Send Check by FedEx ($22.00)" @if($breakdown -> add_fedex == 'on') checked @endif>
            </div>
        </div>

        <div id="deduction_container">

            @php
            $deductions = $breakdown -> deductions;
            @endphp

            @if(count($deductions) > 0)

                @foreach($deductions as $deduction)

                    <div class="row d-flex align-items-center no-gutters template">
                        <div class="col-7">
                            {{ $deduction -> description }}
                        </div>
                        <div class="col-5 d-flex justify-content-end">
                            ${{ number_format($deduction -> amount, 2) }}
                        </div>
                </div>

                @endforeach

            @endif

        </div>

    </div>

</div>

<div class="row d-flex align-items-center">
    <div class="col-8">
        <span class="text-danger font-10">Deductions Total</span>
    </div>
    <div class="col-4 d-flex justify-content-end">
        ${{ number_format($breakdown -> commission_deductions_total, 2) }}
    </div>
</div>

<div class="row">
    <div class="col-12">
        <hr class="my-3">
    </div>
</div>

<div class="row d-flex align-items-center border-top mt-3 p-4 bg-success text-white commission-total-row">
    <div class="col-8">
        <span class="font-weight-bold font-11">Total Commission To Agent</span>
    </div>
    <div class="col-4 d-flex justify-content-end">
        ${{ number_format($breakdown -> total_commission_to_agent, 2) }}
    </div>
</div>

<div class="row">

    <div class="col-12">

        <div class="font-12 text-orange mt-5 mb-4">Commission Check Details</div>

        <div class="row">
            <div class="col-12">
                <div>Make Check Payable To</div>
                {{ $breakdown -> check_payable_to }}
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div>How would you like to receive your commission check?</div>
                {{ ucwords($breakdown -> delivery_method) }}
            </div>
        </div>

        @if($breakdown -> delivery_method == 'mail' || $breakdown -> delivery_method == 'fedex')

            <div class="row">
                <div class="col-12">
                    <div>Mailing address</div>

                    <div class="row">
                        <div class="col-12">
                            {{ $breakdown -> check_mail_to_street }}
                            <br>
                            {{ $breakdown -> check_mail_to_city }}, {{ $breakdown -> check_mail_to_state }} {{ $breakdown -> check_mail_to_zip }}
                        </div>
                    </div>

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
