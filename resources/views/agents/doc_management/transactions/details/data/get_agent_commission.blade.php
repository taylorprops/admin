<div class="container pb-5">

    <div class="row">

        <div class="col-12 col-xl-5 mt-4 mx-auto">

            @if($breakdown -> status != 'complete')

                @if($breakdown -> status == 'submitted')

                    <div class="d-flex justify-content-start align-items-center bg-blue-light text-primary">
                        <div class="p-3">
                            <i class="fad fa-check-circle fa-2x"></i>
                        </div>
                        <div class="p-3">
                            Your commission breakdown has been submitted and is awaiting review. You can still make changes until it is reviewed and we will notify you once the review process is complete.
                        </div>
                    </div>

                @elseif($breakdown -> status == 'reviewed')

                    <div class="d-flex justify-content-start align-items-center bg-blue-light text-primary">
                        <div class="p-3">
                            <i class="fad fa-check-circle fa-2x"></i>
                        </div>
                        <div class="p-3">
                            Your commission breakdown has been begun the review process. We will notify you once the process is complete and your commission is ready.
                        </div>
                    </div>

                @endif

                <form id="commission_form">

                    <div class="text-gray pb-5 font-10">

                        <div class="font-13 text-primary mt-4 mb-3 border-bottom">Enter Your Commission Details</div>

                        <div class="row">
                            <div class="col-12">
                                <div class="font-12 text-orange">Income</div>
                            </div>
                        </div>

                        <div class="row d-flex align-items-center">
                            <div class="col-8">
                                Commission Check Amount @if($holding_earnest) <br><span class="small text-orange">(Do Not include escrow)</span> @endif
                            </div>
                            <div class="col-4 d-flex justify-content-end d-flex justify-content-end">
                                <div class="wpx-200">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-success @if(!$is_referral_company) total-trigger @endif" id="checks_in_total" name="checks_in_total" value="@if($is_referral) {{ $property -> AgentCommission }} @else {{ $breakdown -> checks_in_total ?? '$0.00' }} @endif">
                                </div>
                            </div>
                        </div>

                        @if($for_sale)

                            <div class="row d-flex align-items-center">
                                <div class="col-8">
                                    Admin Fee From Title Company
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="wpx-200">
                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-success admin-fee-in-total total-trigger" id="admin_fee_in_total" name="admin_fee_in_total" value="{{ $breakdown -> admin_fee_in_total ?? '$0.00' }}">
                                    </div>
                                </div>
                            </div>

                            @if($holding_earnest)
                                <div class="row d-flex align-items-center">
                                    <div class="col-8">
                                        Money In Escrow
                                    </div>
                                    <div class="col-4 d-flex justify-content-end">
                                        <div class="wpx-200">
                                            <input type="text" class="custom-form-element form-input money-decimal text-right text-success disabled" id="earnest_deposit_amount" name="earnest_deposit_amount" value="{{ $earnest_amount }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @else
                            <input type="hidden" id="admin_fee_in_total" name="admin_fee_in_total" value="0">
                            <input type="hidden" id="earnest_deposit_amount" name="earnest_deposit_amount" value="0">
                        @endif

                        <div class="row d-flex align-items-center">
                            <div class="col-8 font-weight-bold font-10">
                                Total In
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <div class="wpx-200">
                                    <input type="text" class="custom-form-element form-input money-decimal text-right font-weight-bold disabled" id="total_income" name="total_income" value="{{ $breakdown -> total_income ?? '$0.00' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>
                        </div>


                        {{-- <div class="font-weight-bold font-11 text-orange mt-4 mb-2 border-bottom">Commission Deductions</div> --}}

                        @if($for_sale || $is_rental)
                            <div class="row d-flex align-items-center client-paid-admin {{-- @if(!$is_rental) hidden @endif --}}">
                                <div class="col-8">
                                    Admin Fee Paid By Client
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="wpx-200">
                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger total-trigger" id="admin_fee_from_client" name="admin_fee_from_client" value="{{ $breakdown -> admin_fee_from_client ?? '$0.00' }}">
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="admin_fee_from_client" name="admin_fee_from_client" value="0">
                        @endif

                        <div class="row d-flex align-items-center client-paid-admin {{-- @if(!$is_rental) hidden @endif --}}">
                            <div class="col-8 text-success font-10">
                                Commission In Total
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <div class="wpx-200">
                                    <input type="text" class="custom-form-element form-input money-decimal text-right text-success font-weight-bold disabled" id="sub_total" name="sub_total" disabled>
                                </div>
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
                                    <input type="hidden" id="agent_commission_deduction_percent" value="{{ $agent_commission_deduction_percent }}">
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="wpx-200">
                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger disabled" id="agent_commission_deduction" name="agent_commission_deduction" value="{{ $breakdown -> agent_commission_deduction ?? '$0.00' }}" disabled>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="agent_commission_deduction" name="agent_commission_deduction" value="0">
                        @endif

                        @if(!$is_referral_company)
                            <div class="row d-flex align-items-center agent-paid-admin">
                                <div class="col-8">
                                    Admin Fee Paid By Agent
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="wpx-200">
                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger total-trigger disabled" id="admin_fee_from_agent" name="admin_fee_from_agent" value="{{ $admin_fee }}" data-default-value="{{ $admin_fee }}" disabled>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" id="admin_fee_from_agent" name="admin_fee_from_agent" value="0">
                        @endif

                        @if($is_referral_company)
                            {{-- 15% referral fee for referral company agents --}}
                            <div class="row d-flex align-items-center agent-paid-admin">
                                <div class="col-8">
                                    15% Transaction Fee
                                </div>
                                <div class="col-4 d-flex justify-content-end">
                                    <div class="wpx-200">
                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger total-trigger disabled" id="referral_company_deduction" name="referral_company_deduction" value="{{ $referral_company_deduction }}" disabled>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">

                            <div class="col-12">

                                <div class="row">
                                    <div class="col-12">
                                        <input type="checkbox" class="custom-form-element form-checkbox" id="add_fedex" name="add_fedex" data-label="Send Check by FedEx ($22.00)" @if($breakdown -> add_fedex == 'on') checked @endif>
                                    </div>
                                </div>


                                <div id="deduction_template" class="hidden">

                                    <div class="row d-flex align-items-center no-gutters template">
                                        <div class="col-1">
                                            <button type="button" class="btn btn-danger btn-sm delete-deduction-button"><i class="fal fa-trash"></i></button>
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="deduction-description" name="deduction_description[]" data-label="Description">
                                        </div>
                                        <div class="col-4">
                                            <div class="ml-2">
                                                <input type="text" class="money-decimal numbers-only text-right text-danger deduction-amount" name="deduction_amount[]" data-label="Amount">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div id="deduction_container">

                                    @if(count($deductions) > 0)

                                        @foreach($deductions as $deduction)

                                            <div class="row d-flex align-items-center no-gutters template">
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-danger btn-sm delete-deduction-button @if($deduction -> description == 'FedEx') fedex-delete @endif"><i class="fal fa-trash"></i></button>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" class="custom-form-element form-input deduction-description" name="deduction_description[]" value="{{ $deduction -> description }}" data-label="Description">
                                                </div>
                                                <div class="col-4">
                                                    <div class="ml-2">
                                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger deduction-amount" name="deduction_amount[]" value="{{ $deduction -> amount }}" data-label="Amount">
                                                    </div>
                                                </div>
                                        </div>

                                        @endforeach

                                    @else

                                        @if($from_rental_listing == 'yes')
                                            {{-- Owners and Renters agent (not our agent) info --}}
                                            <div class="row d-flex align-items-center no-gutters">
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-danger btn-sm delete-deduction-button"><i class="fal fa-trash"></i></button>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" class="custom-form-element form-input deduction-description"  name="deduction_description[]" data-label="Description" value="{{ $property -> BuyerAgentFirstName.' '.$property -> BuyerAgentLastName }} - Renter's Agent">
                                                </div>
                                                <div class="col-4">
                                                    <div class="ml-2">
                                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger deduction-amount" name="deduction_amount[]" data-label="Amount">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row d-flex align-items-center no-gutters">
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-danger btn-sm delete-deduction-button"><i class="fal fa-trash"></i></button>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" class="custom-form-element form-input deduction-description"  name="deduction_description[]" data-label="Description" value="{{ $property -> SellerOneFullName }} - Owner">
                                                </div>
                                                <div class="col-4">
                                                    <div class="ml-2">
                                                        <input type="text" class="custom-form-element form-input money-decimal numbers-only text-right text-danger deduction-amount" name="deduction_amount[]" data-label="Amount">
                                                    </div>
                                                </div>
                                            </div>

                                        @endif

                                    @endif

                                </div>

                                <div class="my-3">
                                    <button type="button" class="btn btn-primary btn-sm ml-0" id="add_deduction_button"><i class="fal fa-plus mr-2"></i> Add Deduction</button>
                                </div>

                            </div>

                        </div>

                        <div class="row d-flex align-items-center">
                            <div class="col-8">
                                <span class="text-danger font-10">Deductions Total</span>
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <div class="wpx-200">
                                    <input type="text" class="custom-form-element form-input money-decimal font-weight-bold text-danger text-right disabled" id="commission_deductions_total" name="commission_deductions_total" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                        </div>

                        <div class="row d-flex align-items-center border-top mt-3 bg-success text-white commission-total-row">
                            <div class="col-8">
                                <span class="font-weight-bold font-11">Total Commission To Agent</span>
                            </div>
                            <div class="col-4 d-flex justify-content-end">
                                <div class="wpx-200">
                                    <input type="text" class="custom-form-element form-input money-decimal font-weight-bold text-right bg-white text-success commission-total-input disabled" id="total_commission_to_agent" name="total_commission_to_agent" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-12">

                                <div class="font-12 text-orange mt-5 mb-4">Commission Check Details</div>

                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div>Make Check Payable To</div>
                                        <input type="text" class="custom-form-element form-input required" id="check_payable_to" name="check_payable_to" value="@if($breakdown -> check_payable_to != '') {{ $breakdown -> check_payable_to }} @elseif($agent -> llc_name != '') {{ $agent -> llc_name }} @else {{ $agent -> full_name }} @endif">
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div>How would you like to receive your commission check?</div>
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="delivery_method" id="delivery_method" data-label="Select Option">
                                            <option value=""></option>
                                            <option value="pickup" @if($breakdown -> delivery_method == 'pickup') selected @endif>Picking Up</option>
                                            <option value="mail" @if($breakdown -> delivery_method == 'mail') selected @endif>Mailed To You</option>
                                            <option value="fedex" @if($breakdown -> delivery_method == 'fedex') selected @endif>FedEx Overnight</option>
                                            <option value="settlement" @if($breakdown -> delivery_method == 'settlement') selected @endif>At Settlement</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3 mail-details hidden">
                                    <div class="col-12">
                                        <div>Confirm your mailing address</div>

                                        <div class="row">
                                            <div class="col-12">
                                                <input type="text" class="custom-form-element form-input address-input required" data-label="Street Address" id="check_mail_to_street" name="check_mail_to_street" value="@if($breakdown -> address_street != '') {{ $breakdown -> address_street }} @else {{ $agent -> address_street }} @endif">
                                            </div>
                                        </div>

                                        <div class="row mb-5">

                                            <div class="col-6">
                                                <input type="text" class="custom-form-element form-input address-input required" data-label="City" id="check_mail_to_city" name="check_mail_to_city" value="@if($breakdown -> address_city != '') {{ $breakdown -> address_city }} @else {{ $agent -> address_city }} @endif">
                                            </div>

                                            <div class="col-3">
                                                <select class="custom-form-element form-select form-select-no-cancel address-input required" id="check_mail_to_state" name="check_mail_to_state" data-label="State">
                                                    <option value=""></option>
                                                    @foreach($states as $state)
                                                        <option value="{{ $state -> state }}"
                                                            @if($breakdown -> address_state != '')
                                                                {{ $breakdown -> address_state }}
                                                            @else
                                                                @if($state -> state == $agent -> address_state)
                                                                    selected
                                                                @endif
                                                            @endif>{{ $state -> state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-3">
                                                <input type="text" class="custom-form-element form-input address-input required" data-label="Zip Code" id="check_mail_to_zip" name="check_mail_to_zip" value="@if($breakdown -> address_zip != '') {{ $breakdown -> address_zip }} @else {{ $agent -> address_zip }} @endif">
                                            </div>

                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-12 disclosure-div p-2">
                                                <div class="font-italic font-weight-bold mb-2 text-danger">Required Authorization</div>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="mx-3">
                                                        <input type="checkbox" class="custom-form-element form-checkbox" id="mail_disclosure" @if($breakdown -> status != 'not_submitted') checked @endif>
                                                    </div>
                                                    <label for="mail_disclosure" class="font-9 mb-0">
                                                        I authorize Anne Arundel Properties, Inc / Taylor Properties to mail this commission check to the address requested. If the check is lost in the mail, I agree to pay the “Stop Payment” bank fee of $35 to have a new commission check processed and mailed.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                Add Notes
                                <textarea class="custom-form-element form-textarea" id="notes" name="notes" rows="3">{{ $breakdown -> notes }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                        </div>

                        <div class="d-flex justify-content-around">
                            <button type="button" class="btn btn-primary btn-lg p-3" id="save_agent_commission_button"><i class="fad fa-save mr-2"></i> Save Details</button>
                        </div>

                    </div>

                </form>{{-- end #commission_form --}}



            @elseif($breakdown -> status == 'complete')

                <div class="d-flex justify-content-start align-items-center bg-green-lighter text-success">
                    <div class="p-3">
                        <i class="fad fa-check-circle fa-2x"></i>
                    </div>
                    <div class="p-3">
                        Your commission breakdown has been processed and is ready!
                    </div>

                    {{--
                    update status when admin reviews
                    reviewed - after first save
                    complete - total_in > 0 && total_left = 0
                    --}}
                    <div class="commission-details">

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>
