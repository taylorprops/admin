<div class="container commission-container mx-auto p-1 pb-5 mb-5">
{{-- TODO:
    hide fields when rental/referral
    add referral commission deduction for referral company agents
    --}}
    @php

    $divider = '
        <div class="row">
            <div class="col-5">
                <hr>
            </div>
        </div>
    ';
    $divider_full = '
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>
    ';
    @endphp

    <form id="commission_form">

        <div class="row">

            <div class="col-5">

                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5 text-orange mb-2 mt-3 w-100 border-bottom">@if($type == 'referral') Receiving Agent @else Commission @endif Details </div>
                    </div>
                </div>

                @if($type == 'sale')

                    {{-- Close Price --}}
                    <div class="row">

                        <div class="col-5">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div class="text-gray">Close Price</div>
                                <div>
                                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Close Price" data-content="This is the final close price on the ALTA. This is not the same as the Sales Price."><i class="fad fa-question-circle ml-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="pr-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only pr-2 form-value" name="close_price" id="close_price" value="${{ number_format($property -> ClosePrice, 0) }}">
                            </div>
                        </div>

                    </div>

                    {{-- Close Date --}}
                    <div class="row">

                        <div class="col-5">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div class="text-gray">Close Date</div>
                                <div>
                                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Close Date" data-content="This is the final close date on the ALTA. This is not the same as the Settle Date."><i class="fad fa-question-circle ml-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="pr-4">
                                <input type="text" class="custom-form-element form-input datepicker pr-2 form-value" name="close_date" id="close_date" value="{{ $property -> CloseDate }}">
                            </div>
                        </div>

                    </div>

                    @if($rep_both_sides)
                    {{-- Work With Both Sides --}}
                    <div class="row">

                        <div class="col-5">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div class="text-gray">Work With Both Sides</div>
                                <div>
                                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Work With Both Sides" data-content="This is when an agent works with both parties even if they don't represent both parties."><i class="fad fa-question-circle ml-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="pr-4">
                                <select class="custom-form-element form-select form-select-no-search form-value" id="both_sides" name="both_sides">
                                    <option value=""></option>
                                    <option value="yes"
                                    @if($commission -> both_sides != '')
                                        @if($commission -> both_sides == 'yes')
                                            selected
                                        @endif
                                    @else
                                        selected
                                    @endif
                                    >Yes</option>
                                    <option value="no" @if($commission -> both_sides == 'no') selected @endif>No</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    @else
                    <input type="hidden" class="form-value" id="both_sides" name="both_sides" value="no">
                    @endif

                    @if($for_sale == 'yes')

                        {{-- Using Heritage Title --}}
                        <div class="row">

                            <div class="col-5">
                                <div class="h-100 d-flex justify-content-end align-items-center">
                                    <div class="text-gray">Using Heritage Title</div>
                                    <div>
                                        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Using Heritage Title" data-content="Did the property sale settle at Heritage Title?"><i class="fad fa-question-circle ml-2"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="pr-4">
                                    <select class="custom-form-element form-select form-select-no-search form-value" id="using_heritage" name="using_heritage">
                                        <option value=""></option>
                                        <option value="yes" @if($property -> UsingHeritage == 'yes') selected @endif>Yes</option>
                                        <option value="no" @if($property -> UsingHeritage == 'no') selected @endif>No</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        {{-- Title Company --}}
                        <div class="row" id="title_company_row">

                            <div class="col-5">
                                <div class="h-100 d-flex justify-content-end align-items-center">
                                    <div class="text-gray">Title Company</div>
                                    <div>
                                        <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Title Company" data-content="Enter the Title Company used"><i class="fad fa-question-circle ml-2"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="pr-4">
                                    <input type="text" class="custom-form-element form-input form-value" id="title_company" name="title_company" data-label="Title Company" value="{{ $property -> TitleCompany }}">
                                </div>
                            </div>

                        </div>

                    @else
                        <input type="hidden" class="form-value" id="using_heritage" name="using_heritage" value="no">
                        <input type="hidden" class="form-value" id="title_company" name="title_company" value="">
                    @endif

                @elseif($type == 'referral')

                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="text-gray-dark font-weight-bold">Office</div>
                        <div class="text-gray">
                            <strong>{{ $property -> ReceivingAgentOfficeName }}</strong><br>
                            {{ $property -> ReceivingAgentOfficeStreet }}<br>
                            {{ $property -> ReceivingAgentOfficeCity }}, {{ $property -> ReceivingAgentOfficeState }} {{ $property -> ReceivingAgentOfficeZip }}<br>
                            {{ format_phone($property -> ReceivingAgentOfficePhone) }}
                        </div>

                        <div class="text-gray-dark font-weight-bold mt-3">Agent</div>
                        <div class="text-gray">
                            <strong>{{ $property -> ReceivingAgentFirstName }} {{ $property -> ReceivingAgentLastName }}</strong><br>
                            {{ format_phone($property -> ReceivingAgentPreferredPhone) }}<br>
                            <a href="mailto:{{ $property -> ReceivingAgentEmail }}">{{ $property -> ReceivingAgentEmail }}</a>
                        </div>
                    </div>
                </div>



                @else

                    <div class="row">

                        <div class="col-5"></div>

                        <div class="col-7">

                            <div class="text-gray">

                                <div class="row">

                                    <div class="col-12">
                                        <select class="custom-form-element form-select form-select-no-cancel form-value" id="Agent_ID" name="Agent_ID" data-label="Agent">
                                            <option value=""></option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent -> id }}" @if($commission -> Agent_ID == $agent -> id) selected @endif>{{ $agent -> first_name.' '.$agent -> last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input form-value" id="other_client_name" name="other_client_name" data-label="Client's Name" value="{{ $commission -> other_client_name }}">
                                    </div>

                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input address required form-value" id="other_street" name="other_street" data-label="Street Address" value="{{ $commission -> other_street }}">
                                    </div>

                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input address required form-value" id="other_city" name="other_city" data-label="City" value="{{ $commission -> other_city }}">
                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-6">
                                        <select class="custom-form-element form-select form-select-no-cancel address required form-value" id="other_state" name="other_state" data-label="State">
                                            <option value=""></option>
                                            @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($commission -> other_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="custom-form-element form-input address required form-value" id="other_zip" name="other_zip" data-label="Zip Code" value="{{ $commission -> other_zip }}">
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <input type="hidden"class="form-value" id="close_price" name="close_price" value="0">
                    <input type="hidden"class="form-value" id="close_date" name="close_date" value="{{ date('Y-m-d') }}">
                    <input type="hidden"class="form-value" id="both_sides" name="both_sides" value="no">
                    <input type="hidden"class="form-value" id="using_heritage" name="using_heritage" value="no">
                    <input type="hidden"class="form-value" id="title_company" name="title_company" value="">

                @endif

            </div>

            <div class="col-7 relative">

                <div class="commission-details-tabs pl-md-5">

                    <div class="ml-5 p-3">

                        <ul class="nav nav-tabs" id="options_tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link options-tab active" id="agent_commission_tab" data-toggle="tab" href="#agent_commission_div" role="tab" aria-controls="agent_commission_div" aria-selected="true">Agent Commission Breakdown</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link options-tab" id="agent_details_tab" data-toggle="tab" href="#agent_details_div" role="tab" aria-controls="agent_details_div" aria-selected="true">Agent Details</a>
                            </li>
                            @if(!stristr($agent_details -> company, 'referral'))
                            <li class="nav-item">
                                <a class="nav-link options-tab" id="agent_billing_details_tab" data-toggle="tab" href="#agent_billing_details_div" role="tab" aria-controls="agent_billing_details_div" aria-selected="true">Agent Billing Details</a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link options-tab" id="notes_tab" data-toggle="tab" href="#notes_div" role="tab" aria-controls="notes_div" aria-selected="false">Notes</a>
                            </li>
                        </ul>

                        <div class="tab-content border-left border-bottom border-right p-3" id="options_tab_content">

                            <div class="tab-pane fade show active" id="agent_commission_div" role="tabpanel" aria-labelledby="agent_commission_tab">

                                <div class="agent-commission-div mx-auto">  </div>

                            </div>

                            <div class="tab-pane fade" id="agent_details_div" role="tabpanel" aria-labelledby="agent_details_tab">

                                <div class="agent-details-div">  </div>

                            </div>

                            @if(!stristr($agent_details -> company, 'referral'))

                                <div class="tab-pane fade" id="agent_billing_details_div" role="tabpanel" aria-labelledby="agent_billing_details_tab">

                                    <div class="agent-billing-details-div">

                                        <div class="row">

                                            <div class="col-12 col-md-6">

                                                @if($type == 'sale' || $type == 'referral')
                                                    <div class="d-flex justify-content-between">
                                                        <div>Admin Fee Amount</div>
                                                        <div>${{ $for_sale ? $agent_details -> admin_fee : $agent_details -> admin_fee_rentals }}</div>
                                                    </div>
                                                @endif

                                                <div class="d-flex justify-content-between @if($agent_details -> balance > 0) text-danger @endif">
                                                    <div>Balance Dues</div>
                                                    <div>${{ number_format($agent_details -> balance, 2) ?? '0.00' }}</div>
                                                </div>

                                                <div class="d-flex justify-content-between @if($agent_details -> balance_eno > 0) text-danger @endif">
                                                    <div>Balance E&O</div>
                                                    <div>${{ number_format($agent_details -> balance_eno, 2) ?? '0.00' }}</div>
                                                </div>

                                                @if($agent_details -> office_rent_amount > 0 || $agent_details -> balance_rent != 0)
                                                    <div class="d-flex justify-content-between @if($agent_details -> balance_rent > 0) text-danger @endif">
                                                        <div>Balance Rent</div>
                                                        <div>${{ number_format($agent_details -> balance_rent, 2) ?? '0.00' }}</div>
                                                    </div>
                                                @endif

                                                <hr class="my-1">

                                                <div class="d-flex justify-content-between">
                                                    <div>Auto Billed</div>
                                                    <div>{{ $agent_details -> auto_bill == 'on' ? 'Yes' : 'No' }}</div>
                                                </div>

                                                <div class="d-flex justify-content-between">
                                                    <div>Commission</div>
                                                    <div>{{ ucwords($agent_details -> commission_percent) }}% - Plan {{ ucwords($agent_details -> commission_plan) }}</div>
                                                </div>

                                            </div>

                                            <div class="col-12 col-md-6">

                                                @if($agent_details -> owe_other == 'yes')
                                                    <div class="wage-garnishments p-1 mt-1 bg-orange-light text-danger rounded">{!! nl2br($agent_details -> owe_other_notes) !!}</div>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endif

                            <div class="tab-pane fade" id="notes_div" role="tabpanel" aria-labelledby="notes_tab">

                                <div class="px-5">

                                    <div class="commission-notes-div border-bottom">
                                        <ul class="list-group notes-list-group"></ul>
                                    </div>

                                    <div class="row no-gutters bg-blue-light d-flex align-items-center py-2 px-4 mt-3 rounded">
                                        <div class="col-11">
                                            <div>
                                                <input type="text" class="custom-form-element form-input commission-notes-input" data-label="Add Notes"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <a href="javascript: void(0)" class="btn btn-primary save-commission-notes-button ml-2"><i class="fad fa-save"></i></a>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5 text-orange my-4 w-100 border-bottom">Income</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checks In --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5">
                        <div class="d-flex justify-content-end align-items-center mt-3">
                            <div class="text-primary text-right show-view-add-button">
                                Checks In
                            </div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Checks In" data-content="Add all checks from other Brokers, Title Companies, etc."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-primary show-view-add-button">View/Add <i class="fal fa-plus ml-2"></i></a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="checks_in_count"></div>
                                <div class="mr-2 font-10 text-success">
                                    <span id="checks_in_total_display"></span>
                                    <input type="hidden" id="checks_in_total" name="checks_in_total" class="total form-value">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="pr-2">

                    <div class="popout-div mr-3">

                        <div class="popout top animate__animated animate__fast animate__lightSpeedInRight w-100 bg-blue-light">

                            <div class="px-3 py-1">

                                <div class="d-flex justify-content-start align-items-center mb-3">
                                    <div class="h4 mt-2 text-primary">Checks In</div>
                                    <div class="ml-4">
                                        <a href="javascript: void(0)" class="btn btn-success add-check-in-button"><i class="fal fa-plus mr-2"></i> Add</a>
                                    </div>
                                </div>

                                <div class="view-add-div checks-in-div p-1 p-sm-2">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @if($type == 'sale' && $property -> EarnestHeldBy == 'us')
            {{-- Earnest Deposit --}}
            <div class="row">

                <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                    <div class="row">
                        <div class="col-5">
                            <div class="h-100 d-flex justify-content-end align-items-center">
                                <div class="text-gray">Earnest Deposit</div>
                                <div>
                                    <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Earnest Deposit" data-content="This amount cannot be changed. If we are holding earnest the number will be imported from the Earnest Deposit tab."><i class="fad fa-question-circle ml-2"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="pr-4">
                                <input type="text" class="custom-form-element form-input money-decimal text-success text-right pr-2 total form-value readonly" name="earnest_deposit_amount" id="earnest_deposit_amount" value="{{ $property -> EarnestAmount ?? 0 }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        @else
            <input type="hidden" class="form-value" id="earnest_deposit_amount" name="earnest_deposit_amount" value="0">
        @endif

        {!! $divider !!}

        {{-- Income Deductions --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5">
                        <div class="d-flex justify-content-end align-items-center mt-3">
                            <div class="text-primary text-right show-view-add-button">
                                Income Deductions
                            </div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Income Deductions" data-content="Use this for any portions of the checks that are not income. Situations would include payment to another agent in our company who worked on the opposite side of the transaction."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-primary show-view-add-button">View/Add <i class="fal fa-plus ml-2"></i></a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="income_deductions_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="income_deductions_total_display"></span>
                                    <input type="hidden" id="income_deductions_total" name="income_deductions_total" class="total form-value">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="pr-2">

                    <div class="popout-div mr-3">

                        <div class="popout top animate__animated animate__fast animate__lightSpeedInRight w-100">

                            <div class="px-1 px-sm-3 pb-3 pt-1">

                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="h4 mt-2 text-primary">Income Deductions</div>
                                    <div class="ml-4">
                                        <a class="btn btn-success" data-toggle="collapse" href="#add_income_deduction_div" role="button" aria-expanded="false" aria-controls="add_income_deduction_div"><i class="fal fa-plus mr-2"></i> Add</a>
                                    </div>
                                </div>

                                <div class="view-add-div">

                                    <div class="collapse" id="add_income_deduction_div">

                                        <div class="d-flex flex-wrap justify-content-start align-items-center">
                                            <div class="mr-2">
                                                <input type="text" class="custom-form-element form-input required" name="income_deduction_description" id="income_deduction_description" data-label="Enter Description">
                                            </div>
                                            <div class="mr-2">
                                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="income_deduction_amount" id="income_deduction_amount" data-label="Enter Amount">
                                            </div>
                                            <div>
                                                <div class="d-flex justify-content-start align-items-center h-100">
                                                    <a href="javascript: void(0);" class="btn btn-primary" id="save_add_income_deduction_button"><i class="fad fa-save mr-2"></i> Save</a>
                                                    <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#add_income_deduction_div" role="button" aria-expanded="false" aria-controls="add_income_deduction_div"><i class="fal fa-ban"></i></a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="p-1 p-sm-2 p-md-4">
                                        <div class="list-group check-deductions-div"></div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        @if($type == 'sale')
        {{-- Admin Fee - Paid By Client --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 d-flex justify-content-end align-items-center">
                            <div class="text-gray">Admin Fee - Paid By Client</div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Admin Fee - Paid By Client" data-content="This is the final close date on the ALTA. This is not the same as the Settle Date."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-danger text-right pr-2 total form-value" id="admin_fee_from_client" id="admin_fee_from_client" value="{{ $commission -> admin_fee_from_client }}">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @else
            <input type="hidden" class="form-value" id="admin_fee_from_client" name="admin_fee_from_client" value="0">
        @endif

        {!! $divider !!}

        {{-- Total Income --}}
        <div class="row no-gutters">

            <div class="col-12 col-lg-5 py-2">

                <div class="row">
                    <div class="col-5">
                        <div class="h-100 font-10 d-flex text-success justify-content-end align-items-center">
                            Total Income
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="bg-green-light rounded p-2 mr-4">
                            <div class="d-flex justify-content-end">
                                <div class="mr-1 font-10 text-success">
                                    <span id="total_income_display"></span>
                                    <input type="hidden" class="form-value" id="total_income" name="total_income">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Agent Commission % --}}
        <div class="row no-gutters">

            <div class="col-12 col-lg-5 py-2">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 d-flex justify-content-end align-items-center">
                            <div class="text-gray">Agent Commission %</div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Agent Commission %" data-content="The agents commission percentage."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="agent-commission percent d-flex justify-content-start align-items-center">
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel text-center total form-value" id="agent_commission_percent" id="agent_commission_percent" >
                                        <option value=""></option>
                                        @foreach($commission_percentages as $percent)
                                        <option value="{{ $percent }}" @if($percent == $agent_details -> commission_percent) selected @endif>{{ $percent }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fal fa-percentage text-primary ml-1"></i>
                                </div>
                                <div class="d-none d-xl-block mx-5"></div>
                                <div class="w-100">
                                    <input type="text" class="custom-form-element form-input text-success text-right pr-2 total readonly" readonly name="agent_commission_amount" id="agent_commission_amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {!! $divider !!}

        {{-- Commission Deductions Header --}}
        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5 text-orange mb-2 mt-3 w-100 border-bottom">Commission Deductions</div>
                    </div>
                </div>
            </div>
            <div class="col-7">

            </div>
        </div>

        @if($type == 'sale')
        {{-- Admin Fee - Paid By Agent --}}
        <div class="row">

            <div class="col-12 col-lg-5 pr-2 pr-lg-0">
                <div class="row">
                    <div class="col-5">
                        <div class="h-100 d-flex justify-content-end align-items-center">
                            <div class="text-gray">Admin Fee - Paid By Agent</div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Admin Fee - Paid By Agent" data-content="If the client did not pay the admin fee add it here to be deducted from the agent's commission."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="pr-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only text-danger text-right pr-2 total form-value" id="admin_fee_from_agent" id="admin_fee_from_agent" value="{{ $commission -> admin_fee_from_agent }}">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @else
            <input type="hidden" class="form-value" id="admin_fee_from_agent" name="admin_fee_from_agent" value="0">
        @endif

        {{-- Commission Deductions --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="d-flex justify-content-end align-items-center mt-3">
                            <div class="text-primary text-right show-view-add-button toggle-agent-info">
                                Commission Deductions
                            </div>
                            <div>
                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Commission Deductions" data-content="Use this for any deductions from the agent's commission. Examples include wage garnishments, child support and dues payments."><i class="fad fa-question-circle ml-2"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-primary show-view-add-button toggle-agent-info">View/Add <i class="fal fa-plus ml-2"></i></a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="commission_deductions_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="commission_deductions_total_display"></span>
                                    <input type="hidden" id="commission_deductions_total" name="commission_deductions_total" class="total form-value">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="pr-2">

                    <div class="popout-div mr-3 h-100">

                        <div class="popout top animate__animated animate__fast animate__lightSpeedInRight w-100">

                            <div class="px-1 px-sm-3 pb-3 pt-1">

                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="h4 mt-2 text-primary">Commission Deductions</div>
                                    <div class="ml-4">
                                        <a class="btn btn-success" data-toggle="collapse" href="#add_commission_deduction_div" role="button" aria-expanded="false" aria-controls="add_commission_deduction_div"><i class="fal fa-plus mr-2"></i> Add</a>
                                    </div>
                                </div>


                                <div class="row">

                                    <div class="col-7">

                                        <div class="view-add-div">

                                            <div class="collapse" id="add_commission_deduction_div">

                                                <div class="bg-white rounded p-2 mt-3">
                                                    <div class="row">
                                                        <div class="col-12 col-md-8">
                                                            <input type="text" class="custom-form-element form-input required" name="commission_deduction_description" id="commission_deduction_description" data-label="Enter Description">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="commission_deduction_amount" id="commission_deduction_amount" data-label="Enter Amount">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-around align-items-center">
                                                                <div>
                                                                    <a href="javascript: void(0);" class="btn btn-primary" id="save_add_commission_deduction_button"><i class="fad fa-save mr-2"></i> Save</a>
                                                                    <a class="btn btn-sm btn-danger" data-toggle="collapse" href="#add_commission_deduction_div" role="button" aria-expanded="false" aria-controls="add_commission_deduction_div"><i class="fal fa-ban"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="p-1 p-sm-2 p-md-4">
                                                <div class="list-group commission-deductions-div"></div>
                                            </div>

                                            <div class="col-12 mt-3">

                                                @if($agent_details -> owe_other == 'yes')
                                                    <div class="wage-garnishments p-1 mt-1 bg-orange-light text-danger rounded">{!! nl2br($agent_details -> owe_other_notes) !!}</div>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-5">

                                        <div class="p-2 text-gray">

                                            @if(!stristr($agent_details -> company, 'referral'))

                                                <div class="row">

                                                    <div class="col-12">
                                                        @if($type == 'sale' || $type == 'referral')
                                                            <div class="d-flex justify-content-between">
                                                                <div>Admin Fee Amount</div>
                                                                <div>${{ $for_sale ? $agent_details -> admin_fee : $agent_details -> admin_fee_rentals }}</div>
                                                            </div>
                                                        @endif
                                                        <div class="d-flex justify-content-between @if($agent_details -> balance > 0) text-danger @endif">
                                                            <div>Balance Dues</div>
                                                            <div>${{ number_format($agent_details -> balance, 2) ?? '0.00' }}</div>
                                                        </div>

                                                        <div class="d-flex justify-content-between @if($agent_details -> balance_eno > 0) text-danger @endif">
                                                            <div>Balance E&O</div>
                                                            <div>${{ number_format($agent_details -> balance_eno, 2) ?? '0.00' }}</div>
                                                        </div>

                                                        @if($agent_details -> office_rent_amount > 0 || $agent_details -> balance_rent != 0)
                                                            <div class="d-flex justify-content-between @if($agent_details -> balance_rent > 0) text-danger @endif">
                                                                <div>Balance Rent</div>
                                                                <div>${{ number_format($agent_details -> balance_rent, 2) ?? '0.00' }}</div>
                                                            </div>
                                                        @endif

                                                        <hr class="my-1">

                                                        <div class="d-flex justify-content-between">
                                                            <div>Auto Billed</div>
                                                            <div>{{ $agent_details -> auto_bill == 'on' ? 'Yes' : 'No' }}</div>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <div>Commission</div>
                                                            <div>{{ ucwords($agent_details -> commission_percent) }}% - Plan {{ ucwords($agent_details -> commission_plan) }}</div>
                                                        </div>

                                                    </div>

                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Total Commission to Agent --}}
        <div class="row no-gutters pr-2">

            <div class="col-12 col-lg-5">

                <div class="rounded py-3 my-3 mr-3 bg-green-light">

                    <div class="col-12">

                        <div class="row">
                            <div class="col-5">
                                <div class="h-100 font-10 d-flex text-success justify-content-end align-items-center">
                                    Total Commission To Agent
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="d-flex justify-content-end">
                                    <div class="mr-1 text-success font-11">
                                        <span id="total_commission_to_agent_display"></span>
                                        <input type="hidden" class="form-value" id="total_commission_to_agent" name="total_commission_to_agent">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Save --}}
        <div class="row pr-2">

            <div class="col-12 col-lg-5">

                <div class="d-flex justify-content-center pt-3">

                    <a href="javascript: void(0)" class="btn btn-lg btn-primary" id="save_commission_button"><i class="fad fa-save mr-2"></i> Save Commission</a>

                </div>

            </div>

        </div>

        {!! $divider_full !!}

        {{-- Checks Out Header --}}
        <div class="row">
            <div class="col-5">
                <div class="row">
                    <div class="col-5"></div>
                    <div class="col-7">
                        <div class="h5 text-orange mb-2 mt-3 w-100 border-bottom">Checks Out</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checks Out --}}
        <div class="row popout-row">

            <div class="col-12 col-lg-5 pr-3 pr-lg-0">

                <div class="row">

                    <div class="col-5 text-gray">
                        <div class="text-primary mt-3 text-right show-view-add-button toggle-agent-info">
                            Checks Out
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="popout-action pr-1 pr-lg-4 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="javascript: void(0)" class="btn btn-primary show-view-add-button toggle-agent-info">View/Add <i class="fal fa-plus ml-2"></i></a>
                                </div>
                                <div class="badge badge-pill badge-primary py-1" id="checks_out_count"></div>
                                <div class="mr-2 font-10 text-danger">
                                    <span id="checks_out_total_display"></span>
                                    <input type="hidden" id="checks_out_total" name="checks_out_total" class="total form-value">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7 p-lg-0">

                <div class="pr-2">

                    <div class="popout-div mr-3">

                        <div class="popout middle animate__animated animate__fast animate__lightSpeedInRight w-100">

                            <div class="px-3 pb-3 pt-1">

                                <div class="d-flex justify-content-start align-items-center mb-3">
                                    <div class="h4 mt-2 text-primary">Checks Out</div>
                                    <div class="ml-4">
                                        <a href="javascript: void(0)" class="btn btn-success add-check-out-button"><i class="fal fa-plus mr-2"></i> Add</a>
                                    </div>
                                </div>

                                <div class="view-add-div checks-out-div p-1">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {!! $divider !!}

        {{-- Total Left --}}
        <div class="row no-gutters pr-2">

            <div class="col-12 col-lg-5">

                <div class="rounded py-4 mr-3 total-left">

                    <div class="col-12">

                        <div class="row">
                            <div class="col-5">
                                <div class="h-100 font-12 d-flex justify-content-end align-items-center">
                                    Total Left
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="d-flex justify-content-end">
                                    <div class="mr-1 font-12">
                                        <span id="total_left_display"></span>
                                        <input type="hidden" class="form-value" id="total_left" name="total_left">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- <input type="hidden" class="form-value" id="Commission_ID" name="Commission_ID" value="{{ $commission -> id }}"> --}}

    </form>


</div>


