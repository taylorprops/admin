
<div class="card">
    <div class="card-body">

        <div class="row">

            <div class="col-12 col-md-6">
                <span class="font-weight-bold text-orange">First select a Member Type</span>
                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel member-type-id required" data-label="Member Type">
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
                    <option value="{{ $contact_type -> resource_id }}">{{ $member_type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 d-flex align-items-center">
                <button class="btn btn-sm btn-primary import-contact-button" data-ele="#add_member_div" disabled><i class="fad fa-cloud-download-alt mr-2"></i> Import Contact</button>
                <a href="javascript: void(0)" class="btn btn-sm btn-danger cancel-add-member-button"><i class="fad fa-do-not-enter mr-2"></i> Cancel</a>
            </div>

            <div class="col-12 bank-trust-div">
                <input type="checkbox" class="custom-form-element form-checkbox bank-trust" data-member="" data-label="" disabled>
            </div>

            <div class="col-12 member-entity-name-div">
                <input type="text" class="custom-form-element form-input member-entity-name" data-label="Trust, Company or other Entity Name">
            </div>

            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-first-name" disabled data-label="First Name">
            </div>

            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-last-name" disabled data-label="Last Name">
            </div>
        </div>

        <div class="row">

            <div class="col-12 col-md-6 company-div">
                <input type="text" class="custom-form-element form-input member-company" disabled data-label="Company">
            </div>

            <div class="col-12 col-md-6 bright-mls-id-div">
                <input type="text" class="custom-form-element form-input member-bright-mls-id" disabled data-label="Bright MLS ID">
            </div>

        </div>
        <div class="row">

            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input phone member-phone" disabled data-label="Phone">
            </div>

            <div class="col-12 col-md-6">
                <input type="text" class="custom-form-element form-input member-email" disabled data-label="Email">
            </div>

            <div class="col-12 col-md-6 home-address-div">
                <input type="text" class="custom-form-element form-input member-home-street" disabled data-label="Street Address">
            </div>

            <div class="col-12 col-md-6 home-address-div">
                <input type="text" class="custom-form-element form-input member-home-city" disabled data-label="City">
            </div>

            <div class="col-12 col-md-6 home-address-div">
                <select class="custom-form-element form-select form-select-no-cancel member-home-state" disabled data-label="State">
                    <option value=""></option>
                    @foreach($states as $state)
                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 home-address-div">
                <input type="text" class="custom-form-element form-input member-home-zip" disabled data-label="Zip Code">
            </div>


            <div class="col-12 col-md-6 office-address-div">
                <input type="text" class="custom-form-element form-input member-office-street" disabled data-label="Office Street Address">
            </div>

            <div class="col-12 col-md-6 office-address-div">
                <input type="text" class="custom-form-element form-input member-office-city" disabled data-label="Office City">
            </div>

            <div class="col-12 col-md-6 office-address-div">
                <select class="custom-form-element form-select form-select-no-cancel member-office-state" disabled data-label="Office State">
                    <option value=""></option>
                    @foreach($states as $state)
                    <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 office-address-div">
                <input type="text" class="custom-form-element form-input member-office-zip" disabled data-label="Office Zip Code">
            </div>

            <input type="hidden" class="member-crm-contact-id">

        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="javascript: void(0)" class="btn btn-lg btn-primary save-member-button"><i class="fad fa-save mr-2"></i> Save Details</a>
            </div>
        </div>

    </div>
</div>

