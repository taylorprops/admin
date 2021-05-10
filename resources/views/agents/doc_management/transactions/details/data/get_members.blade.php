<div class="container p-1 p-sm-4">
    <div class="row">
        <div class="col-12 col-md-10 mx-auto">

            <div class="row members-list-group">
                <div class="col-12 col-lg-4">
                    <a href="javascript: void(0)" id="add_member_button" class="btn btn-primary"><i class="fal fa-plus mr-2"></i> Add Member</a>
                    <div class="list-group my-3 border-top
                    " id="members_tab_section" role="tablist">
                        <a class="list-group-item list-group-item-action hide font-weight-bold" id="add_member_group" data-toggle="list" href="#add_member_div" role="tab">New Contact</a>
                        @foreach($members as $member)
                            @php
                            $member_type = $member -> member_type -> resource_name;
                            if($for_sale == false) {
                                if($member_type == 'Seller') {
                                    $member_type = 'Owner';
                                } else if($member_type == 'Buyer') {
                                    $member_type = 'Renter';
                                } else if($member_type == 'Buyer Agent') {
                                    $member_type = 'Renter Agent';
                                }
                            }
                            if($member_type == '') {
                                $member_type = 'Other';
                            }
                            @endphp
                            <a class="list-group-item list-group-item-action list-group-item-member @if($loop -> first) active @endif" id="member_{{ $member -> id }}_item" data-toggle="list" href="#member_{{ $member -> id }}_div" role="tab" data-member-type="{{ $member_type }}">
                                <div class="row">
                                    <div class="col-5">
                                        <span class="font-weight-bold">{{ $member_type }}</span>
                                    </div>
                                    <div class="col-7">
                                        @if($member -> entity_name) {{ $member -> entity_name }} @elseif($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="tab-content get-members-tabs pt-2" id="members_tab_div">

                        @foreach($members as $member)
                            @php
                            $disabled = $member -> disabled == true ? 'disabled' : '';
                            @endphp
                        <div class="tab-pane animate__animated animate__fadeIn animate__slow mt-0 {{ $disabled }} @if($loop -> first) show active @endif member-div" id="member_{{ $member -> id }}_div" role="tabpanel">
                            <div class="h3 text-orange mb-2">@if($member -> entity_name) {{ $member -> entity_name }} @elseif($member -> first_name) {{ $member -> first_name . ' ' . $member -> last_name }} @else {{ $member -> company }} @endif</div>
                            <div class="card" id="member_div_{{ $member -> id }}">
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel member-type-id" {{ $disabled }} data-label="Member Role">
                                                <option value=""></option>
                                                @foreach($contact_types as $contact_type)
                                                    @php
                                                    $member_type = $contact_type -> resource_name;
                                                    if($for_sale == false) {
                                                        if($member_type == 'Seller') {
                                                            $member_type = 'Owner';
                                                        } else if($member_type == 'Buyer') {
                                                            $member_type = 'Renter';
                                                        } else if($member_type == 'Buyer Agent') {
                                                            $member_type = 'Renter Agent';
                                                        }
                                                    }
                                                    @endphp
                                                    <option value="{{ $contact_type -> resource_id }}" @if($contact_type -> resource_id == $member -> member_type_id) selected @endif>{{ $member_type }}</option>
                                                @endforeach
                                                <option value="Other" @if($member -> member_type_id == 0) selected @endif>Other</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 d-flex align-items-center flex-wrap">
                                            @if($disabled == '')
                                            <a href="javascript: void(0)" class="btn btn-sm btn-primary import-contact-button" data-ele="{{ '#member_div_'.$member -> id }}"><i class="fad fa-cloud-download-alt mr-2"></i> Import Contact</a>
                                            <a href="javascript: void(0)" class="btn btn-sm btn-danger delete-member-button" data-member-id="{{ $member -> id }}"><i class="fad fa-trash mr-2"></i> Delete Member</a>
                                            @endif
                                        </div>

                                        <div class="col-12 bank-trust-div">
                                            <input type="checkbox" class="custom-form-element form-checkbox bank-trust" {{ $disabled }} data-member="buyer" data-label="Buyer is a Trust, Company or other Entity" @if($member -> entity_name != '') checked @endif>
                                        </div>

                                        <div class="col-12 member-entity-name-div">
                                            <input type="text" class="custom-form-element form-input member-entity-name" {{ $disabled }} data-label="Trust, Company or other Entity Name" value="{{ $member -> entity_name }}">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-first-name" {{ $disabled }} value="{{ $member -> first_name }}" data-label="First Name">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-last-name" {{ $disabled }} value="{{ $member -> last_name }}" data-label="Last Name">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-md-6 company-div">
                                            <input type="text" class="custom-form-element form-input member-company" {{ $disabled }} value="{{ $member -> company }}" data-label="Company">
                                        </div>

                                        <div class="col-12 col-md-6 bright-mls-id-div">
                                            <input type="text" class="custom-form-element form-input member-bright-mls-id" {{ $disabled }} value="{{ $member -> bright_mls_id }}" data-label="Bright MLS ID">
                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input phone member-phone" {{ $disabled }} value="{{ $member -> cell_phone }}" data-label="Phone">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <input type="text" class="custom-form-element form-input member-email" {{ $disabled }} value="{{ $member -> email }}" data-label="Email">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-street" {{ $disabled }} value="{{ $member -> address_home_street }}" data-label="Street Address">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-city" {{ $disabled }} value="{{ $member -> address_home_city }}" data-label="City">
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <select class="custom-form-element form-select form-select-no-cancel member-home-state" {{ $disabled }} data-label="State">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($member -> address_home_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 home-address-div">
                                            <input type="text" class="custom-form-element form-input member-home-zip" {{ $disabled }} value="{{ $member -> address_home_zip }}" data-label="Zip Code">
                                        </div>


                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-street" {{ $disabled }} value="{{ $member -> address_office_street }}" data-label="Office Street Address">
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-city" {{ $disabled }} value="{{ $member -> address_office_city }}" data-label="Office City">
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <select class="custom-form-element form-select form-select-no-cancel member-office-state" {{ $disabled }} data-label="Office State">
                                                <option value=""></option>
                                                @foreach($states as $state)
                                                <option value="{{ $state -> state }}" @if($member -> address_office_state == $state -> state) selected @endif>{{ $state -> state }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 office-address-div">
                                            <input type="text" class="custom-form-element form-input member-office-zip" {{ $disabled }} value="{{ $member -> address_office_zip }}" data-label="Office Zip Code">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            @if($disabled == '')
                                            <a href="javascript: void(0)" class="btn btn-lg btn-primary save-member-button font-11 p-3 mt-3"><i class="fad fa-save mr-2"></i> Save Details</a>
                                            @endif
                                        </div>
                                    </div>

                                    <input type="hidden" class="member-id" value="{{ $member -> id }}">
                                    <input type="hidden" class="member-crm-contact-id" value="{{ $member -> CRMContact_ID ?? 0 }}">

                                </div>
                            </div>
                        </div>

                        @endforeach

                        <div class="tab-pane animate__animated animate__fadeIn animate__slow mt-0 member-div" id="add_member_div" role="tabpanel" aria-labelledby="add_member_group"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


