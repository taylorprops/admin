<div class="container mt-0">
    <div class="row">
        <div class="col-12">
            <form id="transaction_details_form">

                <div class="row">

                    <div class="col-12 col-md-6">
                        <div class="transaction-details-div my-2 z-depth-1 h-100">
                            <div class="h5 responsive m-2 mb-4 text-default">
                                <i class="fad fa-file-signature mr-3"></i> {{ ucwords($transaction_type) }} Details
                            </div>

                            @if($transaction_type != 'referral')

                            <div class="row">
                                <div class="col-12 col-xl-8">
                                    <div class="row d-flex align-items-center">
                                        {{-- TODO: this needs to be dynamic if MLS ID is changed --}}
                                        <div class="col-1">
                                            @if($property -> MLS_Verified)
                                                <i class="fal fa-check fa-2x text-success mls-verified" data-toggle="tooltip" title="MLS ID Verified"></i>
                                            @endif
                                        </div>
                                        <div class="col-6 pr-0">
                                            <input type="text" class="custom-form-element form-input" data-label="MLS ID" name="ListingId" id="ListingId" value="{{ $property -> ListingId }}">
                                        </div>
                                        <div class="col-2 pl-0">
                                            <a href="javascript: void(0)" class="btn btn-primary" id="search_mls_button">Search</a>
                                        </div>
                                        <div class="col-2">
                                            <a href="javascript: void(0)" class="float-left" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Bright MLS ID" data-content="If the MLS ID is found, data from BrightMLS will be imported and auto-populated.<br><br><i class='fad fa-exclamation-triangle mr-2'></i> If the County is changed a new checklist will be provided. Any relevant forms will be kept in the checklist but some may need to be added or replaced."><i class="fad fa-question-circle ml-4  ml-sm-1 ml-md-3 ml-lg-2 ml-xl-3 fa-lg"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    @if($transaction_type == 'listing')
                                    <input type="text" class="custom-form-element form-input money required" data-label="List Price" name="ListPrice" id="ListPrice" value="{{ $property -> ListPrice }}">
                                    @else
                                    <input type="text" class="custom-form-element form-input money required" data-label="Sale Price" name="ContractPrice" id="ContractPrice" value="{{ $property -> ContractPrice }}">
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="text" class="custom-form-element form-input required" data-label="Year Built" name="YearBuilt" id="YearBuilt" value="{{ $property -> YearBuilt }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    @if($transaction_type == 'listing')
                                    <input type="text" class="custom-form-element form-input datepicker required" data-label="List Date" name="MLSListDate" id="MLSListDate" value="{{ $property -> MLSListDate }}">
                                    @else
                                    <input type="text" class="custom-form-element form-input datepicker required" data-label="Contract Date" name="ContractDate" id="ContractDate" value="{{ $property -> ContractDate }}">
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    @if($transaction_type == 'listing')
                                    <input type="text" class="custom-form-element form-input datepicker required" data-label="Expiration Date" name="ExpirationDate" id="ExpirationDate" value="{{ $property -> ExpirationDate }}">
                                    @else
                                    <input type="text" class="custom-form-element form-input datepicker required" data-label="Settle Date" name="CloseDate" id="CloseDate" value="{{ $property -> CloseDate }}">
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="text" class="custom-form-element form-input" data-label="Source" name="Source" id="Source" value="{{ $property -> Source }}">
                                </div>
                            </div>

                            @else

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="ClientFirstName" name="ClientFirstName" value="{{ $property -> ClientFirstName }}" data-label="Client First Name">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="ClientLastName" name="ClientLastName" value="{{ $property -> ClientLastName }}" data-label="Client Last Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input phone" id="ClientPhone" name="ClientPhone" value="{{ $property -> ClientPhone }}" data-label="Client Phone">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input" id="ClientStreet" name="ClientStreet" value="{{ $property -> ClientStreet }}" data-label="Client Street">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <input type="text" class="custom-form-element form-input" id="ClientCity" name="ClientCity" value="{{ $property -> ClientCity }}" data-label="Client City">
                                </div>
                                <div class="col-12 col-sm-4">
                                    <select class="custom-form-element form-select buyer-state" id="ClientState" name="ClientState" data-label="Client State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                        <option value="{{ $state -> state }}" @if($state -> state == $property -> ClientState) selected @endif>{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <input type="text" class="custom-form-element form-input numbers-only" maxlength="5" id="ClientZip" name="ClientZip" value="{{ $property -> ClientZip }}" data-label="Client Zip">
                                </div>
                            </div>

                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mt-4 mt-md-2">
                        <div class="transaction-details-div z-depth-1 h-100">
                            <div class="h5 responsive m-2 mb-4 text-default">
                                <i class="fad fa-users mr-3"></i> Agent(s)
                            </div>


                            @if($transaction_type != 'referral')
                                {{-- if a contract --}}
                                @if($transaction_type == 'contract')
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input" disabled value="{{ $list_agent }}" data-label="Listing Agent">
                                        </div>
                                    </div>
                                @else
                                    {{-- if a listing --}}
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select required" @if(Auth::user() -> group == 'agent') disabled @endif data-label="Listing Agent" name="Agent_ID" id="Agent_ID">
                                                <option value=""></option>
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent -> id }}" @if($property -> Agent_ID == $agent -> id) selected @endif>{{ $agent -> last_name . ', ' . $agent -> first_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select" data-label="Co-Listing Agent" name="CoAgent_ID" id="CoAgent_ID">
                                                <option value=""></option>
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent -> id }}" @if($property -> CoAgent_ID == $agent -> id) selected @endif>{{ $agent -> last_name . ', ' . $agent -> first_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                {{-- if our contract --}}
                                {{-- if our listing --}}
                                @if($transaction_type == 'contract' && $property -> Listing_ID > 0)
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input" disabled value="{{ $property -> BuyerAgentFirstName.' '.$property -> BuyerAgentLastName }}" data-label="Buyer's Agent">
                                        </div>
                                    </div>

                                {{-- if not our listing --}}
                                @elseif($transaction_type == 'contract' && $property -> Listing_ID == 0)
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select required" @if(Auth::user() -> group == 'agent') disabled @endif data-label="Buyer's Agent" name="Agent_ID" id="Agent_ID">
                                                <option value=""></option>
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent -> id }}" @if($property -> Agent_ID == $agent -> id) selected @endif>{{ $agent -> last_name . ', ' . $agent -> first_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select" data-label="Co-Buyer's Agent" name="CoAgent_ID" id="CoAgent_ID">
                                                <option value=""></option>
                                                @foreach($agents as $agent)
                                                <option value="{{ $agent -> id }}" @if($property -> CoAgent_ID == $agent -> id) selected @endif>{{ $agent -> last_name . ', ' . $agent -> first_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <select class="custom-form-element form-select" data-label="Transaction Coordinator" name="TransCoordinator_ID" id="TransCoordinator_ID">
                                            <option value=""></option>
                                            @foreach($trans_coords as $trans_coord)
                                            <option value="{{ $trans_coord -> id }}" @if($property -> TransCoordinator_ID == $trans_coord -> id) selected @endif>{{ $trans_coord -> last_name . ', ' . $trans_coord -> first_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <select class="custom-form-element form-select" data-label="Team" name="Team_ID" id="Team_ID">
                                            <option value=""></option>
                                            @foreach($teams as $team)
                                            <option value="{{ $team -> id }}" @if($property -> Team_ID == $team -> id) selected @endif>{{ $team -> team_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            @else {{-- if referral --}}

                                @php
                                $our_agent = $agents -> find($property -> Agent_ID);
                                $agent_name = $our_agent -> first_name.' '.$our_agent -> last_name;

                                $other_agent_first = $property -> ReceivingAgentFirstName;
                                $other_agent_last = $property -> ReceivingAgentLastName;
                                $other_agent_office = $property -> ReceivingAgentOfficeName;
                                $other_agent_office_phone = $property -> ReceivingAgentOfficePhone;
                                $other_agent_street = $property -> ReceivingAgentOfficeStreet;
                                $other_agent_city = $property -> ReceivingAgentOfficeCity;
                                $other_agent_state = $property -> ReceivingAgentOfficeState;
                                $other_agent_zip = $property -> ReceivingAgentOfficeZip;
                                @endphp
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input" disabled value="{{ $agent_name }}" data-label="{{ ucwords($property -> ReferralType) }} Agent">
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                    <input type="text" class="custom-form-element form-input" id="ReceivingAgentFirstName" name="ReceivingAgentFirstName" value="{{ $other_agent_first }}" data-label="Receiving Agent First">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentLastName" name="ReceivingAgentLastName" value="{{ $other_agent_last }}" data-label="Receiving Agent Last">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentOfficeName" name="ReceivingAgentOfficeName" value="{{ $other_agent_office }}" data-label="Office Name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input phone" id="ReceivingAgentOfficPhone" name="ReceivingAgentOfficPhone" value="{{ $other_agent_office_phone }}" data-label="Office Phone">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentOfficeStreet" name="ReceivingAgentOfficeStreet" value="{{ $other_agent_street }}" data-label="Office Street">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentOfficeCity" name="ReceivingAgentOfficeCity" value="{{ $other_agent_city }}" data-label="Office City">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentOfficeState" name="ReceivingAgentOfficeState" value="{{ $other_agent_state }}" data-label="Office State">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <input type="text" class="custom-form-element form-input" id="ReceivingAgentOfficeZip" name="ReceivingAgentOfficeZip" value="{{ $other_agent_zip }}" data-label="Office Zip">
                                    </div>
                                </div>

                            @endif
                        </div>
                    </div>

                </div>

                @php
                $disabled = null;
                if($has_listing) {
                    $disabled = 'disabled';
                }
                @endphp
                <div class="row">
                    <div class="col-12">
                        <div class="transaction-details-div mt-4 mb-2 z-depth-1">
                            <div class="row d-flex align-items-center">
                                <div class="col-12 col-xl-3">
                                    <div class="h5 responsive m-2 mb-2 mb-xl-4 text-default">
                                        <i class="fad fa-location mr-3"></i> Property Location
                                    </div>
                                </div>
                                <div class="col-12 col-xl-9">
                                    @if($property -> MLS_Verified && $disabled == null)
                                        <div class="text-success mb-3"><i class="fal fa-check fa-lg mr-3 mls-verified"></i> Location Details were verified by BrightMLS <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="How To Change The Address" data-content="To change the address you must remove the MLS ID or enter a different MLS ID."><i class="fad fa-question-circle ml-2 fa-lg"></i></a></div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-3 col-xl-2">
                                    <input type="text" class="custom-form-element form-input required" data-label="Street Number" name="StreetNumber" id="StreetNumber" value="{{ $property -> StreetNumber += 0 }}" {{ $disabled }}>
                                </div>
                                <div class="col-12 col-lg-6 col-xl-4">
                                    <input type="text" class="custom-form-element form-input required" data-label="Street Name" name="StreetName" id="StreetName" value="{{ $property -> StreetName }}" {{ $disabled }}>
                                </div>
                                <div class="col-12 col-lg-3 col-xl-2">
                                    <select class="custom-form-element form-select" data-label="Street Suffix" name="StreetSuffix" id="StreetSuffix" {{ $disabled }}>
                                        <option value=""></option>
                                        @foreach($street_suffixes as $street_suffix)
                                        <option value="{{ $street_suffix }}" @if($property -> StreetSuffix == $street_suffix) selected @endif>{{ $street_suffix }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3 col-xl-2">
                                    <select class="custom-form-element form-select" data-label="Street Dir" name="StreetDirSuffix" id="StreetDirSuffix" {{ $disabled }}>
                                        <option value=""></option>
                                        @foreach($street_dir_suffixes as $street_dir_suffix)
                                        <option value="{{ $street_dir_suffix }}" @if($property -> StreetDirSuffix == $street_dir_suffix) selected @endif>{{ $street_dir_suffix }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3 col-xl-2">
                                    <input type="text" class="custom-form-element form-input" data-label="Unit" name="UnitNumber" id="UnitNumber" value="{{ $property -> UnitNumber }}" {{ $disabled }}>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 col-xl-4">
                                    <input type="text" class="custom-form-element form-input required" data-label="City" name="City" id="City" value="{{ $property -> City }}" {{ $disabled }}>
                                </div>
                                <div class="col-12 col-lg-4 col-xl-2">
                                    <select class="custom-form-element form-select form-select-no-cancel required" data-label="State" name="StateOrProvince" id="StateOrProvince" {{ $disabled }}>
                                        <option value=""></option>
                                        @foreach($states as $state)
                                        <option value="{{ $state -> state }}" @if($property -> StateOrProvince == $state -> state) selected @endif>{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4 col-xl-2">
                                    <input type="text" class="custom-form-element form-input required" data-label="Postal Code" name="PostalCode" id="PostalCode" value="{{ $property -> PostalCode }}" {{ $disabled }}>
                                </div>
                                @if($transaction_type != 'referral')
                                    <div class="col-12 col-lg-8 col-xl-4">
                                        <div class="row">
                                            <div class="col-11 pr-0">
                                                <select class="custom-form-element form-select form-select-no-cancel required" disabled data-label="County" name="County" id="County">
                                                    <option value=""></option>
                                                    @foreach($counties as $county)
                                                    <option value="{{ $county -> county }}" @if(strtolower($property -> County) == strtolower($county -> county)) selected @endif>{{ $county -> county }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-1 pl-0 pt-2">
                                                <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="County" data-content="You cannot change the County here because it will change the checklist requirements. To change the county you must withdraw the listing and create a new one."><i class="fad fa-question-circle ml-2 fa-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4 mt-3 text-center text-xl-left">
                        <a href="javascript: void(0)" class="btn btn-lg btn-success save-details-button"><span class="h4"><i class="fad fa-save mr-2"></i> Save Details</span></a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
