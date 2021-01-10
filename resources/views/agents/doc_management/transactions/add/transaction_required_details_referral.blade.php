@extends('layouts.main')
@section('title', 'Required Details')

@section('content')

<div class="container-600 page-container page-required-details mx-auto mb-5 pb-5">
    <div class="row">
        <div class="col-12">

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $referral -> FullStreetAddress }} {{ $referral -> City.', '.$referral -> StateOrProvince.' '.$referral -> PostalCode }}</div>

            <div class="h4 text-primary my-4 text-center">Just a few more details</div>

            <form id="details_form" autocomplete="off">
                <input autocomplete="false" name="hidden" type="text" style="display:none;">

                <div class="container shadow mb-4 py-3">

                    <div class="row">
                        <div class="col-12">
                            <div class="h5 text-orange mt-3">Client Details</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input required" id="ClientFirstName" name="ClientFirstName" data-label="Client First">
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input required" id="ClientLastName" name="ClientLastName" data-label="Client Last">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input phone required" id="ClientPhone" name="ClientPhone" data-label="Client Phone">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-4">
                            <a href="javascript: void(0);" class="btn btn-sm btn-primary" id="import_property_address_button" data-street="{{ $referral -> FullStreetAddress }}" data-city="{{ $referral -> City }}" data-state="{{ $referral -> StateOrProvince }}" data-zip="{{ $referral -> PostalCode }}">Use Property Address</a> <span class="font-8 text-gray">{{ $referral -> FullStreetAddress }} {{ $referral -> City.', '.$referral -> StateOrProvince.' '.$referral -> PostalCode }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input required" id="ClientStreet" name="ClientStreet" data-label="Client Street">
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input required" id="ClientCity" name="ClientCity" data-label="Client City">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <select class="custom-form-element form-select buyer-state required" id="ClientState" name="ClientState" data-label="Client State">
                                <option value=""></option>
                                @foreach($states as $state)
                                <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input numbers-only required" maxlength="5" id="ClientZip" name="ClientZip" data-label="Client Zip">
                        </div>
                    </div>

                </div>

                <div class="container shadow mb-4 py-3">

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange">Receiving Agent Details</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-primary btn-sm my-3" id="show_agent_search_div_button" data-toggle="collapse" href="#receiving_agent_search_div" role="button" aria-expanded="false" aria-controls="receiving_agent_search_div">
                                        <i class="fad fa-search mr-2"></i> Search Agents in Bright MLS
                                    </a>
                                    <span class="text-orange small">MD, VA, DC, PA, DE and NJ Only</span>
                                    <div class="collapse" id="receiving_agent_search_div">
                                        <div class="p-2 mb-4 bg-blue-light">
                                            <div class="mb-4">Type the Agent's Name, Email or BrightMLS ID</div>
                                            <input type="text" class="custom-form-element form-input agent-search" data-label="Enter Agent's Name, Email or ID" autocomplete="agentsearch">
                                            <div class="search-results-container">
                                                <div class="list-group search-results bg-white p-2 border shadow w-100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="ReceivingAgentFirstName" name="ReceivingAgentFirstName" data-label="Agent First Name">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="ReceivingAgentLastName" name="ReceivingAgentLastName" data-label="Agent Last Name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="ReceivingAgentOfficeName" name="ReceivingAgentOfficeName" data-label="Agent Company">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="ReceivingAgentOfficeStreet" name="ReceivingAgentOfficeStreet" data-label="Office Street">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="ReceivingAgentOfficeCity" name="ReceivingAgentOfficeCity" data-label="Office City">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <select class="custom-form-element form-select buyer-state required" id="ReceivingAgentOfficeState" name="ReceivingAgentOfficeState" data-label="Office State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input numbers-only required" maxlength="5" id="ReceivingAgentOfficeZip" name="ReceivingAgentOfficeZip" data-label="Office Zip">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="container shadow mb-4 py-3">

                    <div class="row">
                        <div class="col-12">
                            <div class="h5 text-orange mt-3">Commission Details</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input numbers-only money-decimals required" id="CommissionAmount" name="CommissionAmount" data-label="Commission Amount">
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text" max="100" min="0" maxlength="2" class="custom-form-element form-input numbers-only" id="ReferralPercentage" name="ReferralPercentage" data-label="Commission Referral Percentage">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input numbers-only money-decimals required" id="AgentCommission" name="AgentCommission" data-label="Your Commission">
                        </div>
                        <div class="col-12 col-sm-6">
                            <input type="text"class="custom-form-element form-input numbers-only money-decimals required" id="OtherAgentCommission" name="OtherAgentCommission" data-label="Other Agent's Commission">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center mt-5 mb-5">
                            <a href="javascript: void(0)" class="btn btn-lg btn-success py-3 px-5" id="save_details_button"> Continue <i class="fa fa-arrow-circle-right ml-2"></i></a>
                        </div>
                    </div>

                </div>

                <input type="hidden" name="Referral_ID" id="Referral_ID" value="{{ $referral -> Referral_ID }}">
                <input type="hidden" name="Agent_ID" id="Agent_ID" value="{{ $referral -> Agent_ID }}">
                <input type="hidden" name="ReceivingAgentOfficePhone" id="ReceivingAgentOfficePhone">
                <input type="hidden" name="ReceivingAgentEmail" id="ReceivingAgentEmail">
                <input type="hidden" name="ReceivingAgentPreferredPhone" id="ReceivingAgentPreferredPhone">

            </form>


        </div>
    </div>


</div>
@endsection
