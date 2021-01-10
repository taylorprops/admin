@extends('layouts.main')
@section('title', 'Required Details')

@section('content')

@php

$seller_one_first = substr($property -> Owner1, strpos($property -> Owner1, ' ') + 1);
$seller_one_last = substr($property -> Owner1, 0, strpos($property -> Owner1, ' '));
$seller_two_first = null;
$seller_two_last = null;

if(stristr($property -> Owner1, 'llc')) {
    $seller_one_first = substr($property -> Owner1, 0, strpos($property -> Owner1, ' '));
    $seller_one_last = substr($property -> Owner1, strpos($property -> Owner1, ' ') + 1);
}

if($property -> Owner2 != '') {
    $seller_two_first = substr($property -> Owner2, strpos($property -> Owner2, ' ') + 1);
    $seller_two_last = substr($property -> Owner2, 0, strpos($property -> Owner2, ' '));
    if(stristr($property -> Owner1, 'llc')) {
        $seller_two_first = substr($property -> Owner2, 0, strpos($property -> Owner2, ' '));
        $seller_two_last = substr($property -> Owner2, strpos($property -> Owner2, ' ') + 1);
    }
}

@endphp

<script>
    let states = JSON.parse({!!json_encode($states_json)!!});
</script>
<div class="container-1000 page-container page-required-details mx-auto mb-5 pb-5">
    <div class="row">
        <div class="col-8 mb-5 mx-auto">

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $property -> FullStreetAddress }} {{ $property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode }}</div>

            <div class="h4 text-primary mt-3 mb-4 text-center">Just a few more details</div>

            <form id="details_form" autocomplete="off">

                <input autocomplete="false" name="hidden" type="text" style="display:none;">

                <div class="container shadow mb-4 py-3">
                    <div class="row">

                        <div class="col-12 buyer-container">

                            <div class="h4 step-title text-gray mb-4">{{ $for_sale ? 'Buyer' : 'Renter' }}(s)</div>

                            <input type="checkbox" class="custom-form-element form-checkbox bank-trust" data-member="buyer" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }} is a Trust, Company or other Entity">

                            <div class="buyer-div mb-3">

                                <div class="h5 text-orange buyer-header">{{ $for_sale ? 'Buyer' : 'Renter' }} 1</div>

                                <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0 import-from-contacts-button" data-member="buyer" data-member-id="1"><i class="fad fa-user-friends mr-2"></i> Import from Contacts</a>

                                <div class="row bank-trust-row hide">
                                    <div class="col-12>
                                        <input type="text" class="custom-form-element form-input required" name="buyer_entity_name" data-label="Trust, Company or other Entity Name">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="buyer_first_name[]" data-label="First Name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="buyer_last_name[]" data-label="Last Name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input phone required" name="buyer_phone[]" data-label="Phone">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input" name="buyer_email[]" data-label="Email">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input buyer-street required" name="buyer_street[]" data-label="Home Address">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input buyer-city required" name="buyer_city[]" data-label="City">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="custom-form-element form-select buyer-state required" name="buyer_state[]" data-label="State">
                                            <option value=""></option>
                                            @foreach($states as $state)
                                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="text" class="custom-form-element form-input buyer-zip required" name="buyer_zip[]" data-label="Zip Code">
                                    </div>
                                    <input type="hidden" name="buyer_crm_contact_id[]">
                                </div>
                            </div> {{-- end seller-div --}}

                        </div>

                        <div class="col-12">
                            <a href="javascript: void(0);" class="btn btn-sm btn-success add-member-button" data-type="contract" data-member="buyer"><i class="fa fa-plus mr-2"></i> Add {{ $for_sale ? 'Buyer' : 'Renter' }}</a>
                        </div>

                    </div>
                </div>

                <div class="container shadow mb-4 py-3">
                    <div class="row">

                        <div class="col-12 seller-container">

                            <div class="h4 step-title text-gray my-2">{{ $for_sale ? 'Seller' : 'Owner' }}(s)</div>

                            <input type="checkbox" class="custom-form-element form-checkbox bank-trust" data-member="seller" data-label="{{ $for_sale ? 'Seller' : 'Owner' }} is a Trust, Company or other Entity">

                            <div class="seller-div mb-3">
                                <div class="h5 text-orange seller-header">{{ $for_sale ? 'Seller' : 'Owner' }} 1</div>
                                <div class="row bank-trust-row hide">
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input required" name="seller_entity_name" data-label="Trust, Company or other Entity Name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_first_name[]" data-label="First Name" value="{{ $seller_one_first }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_last_name[]" data-label="Last Name" value="{{ $seller_one_last }}">
                                    </div>
                                </div>
                            </div>

                            @if($property -> Owner2 != '')
                            <div class="seller-div mb-3">
                                <div class="d-flex justify-content-between">
                                    <div class="h5 text-orange seller-header">{{ $for_sale ? 'Seller' : 'Owner' }} 2</div>
                                    <div><a href="javascript: void(0)" class="member-delete text-danger" data-member="seller"><i class="fal fa-times fa-2x"></i></a></div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_first_name[]" data-label="First Name" value="{{ $seller_two_first }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_last_name[]" data-label="Last Name" value="{{ $seller_two_last }}">
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>

                        <div class="col-12">
                            <a href="javascript: void(0);" class="btn btn-sm btn-success add-member-button @if($property -> Owner2 != '') hide @endif" data-type="contract" data-member="seller"><i class="fa fa-plus mr-2"></i> Add {{ $for_sale ? 'Seller' : 'Owner' }}</a>
                        </div>

                    </div>
                </div>

                @if($property -> PropertySubType != $resource_items -> GetResourceID('For Sale By Owner', 'checklist_property_sub_types'))

                <div class="container shadow mb-4 py-3">
                    <div class="row">

                        <div class="col-12">

                            <div class="h4 step-title text-gray my-2">List Agent</div>

                            <div class="p-2">

                                <div class="row">
                                    <div class="col-12">
                                        <a class="btn btn-primary btn-sm my-3" data-toggle="collapse" href="#list_agent_search_div" role="button" aria-expanded="false" aria-controls="list_agent_search_div">
                                            <i class="fad fa-search mr-2"></i> Search Agents in Bright MLS
                                        </a>
                                        <div class="collapse" id="list_agent_search_div">
                                            <div class="p-2 mb-4 font-8 bg-blue-light border">
                                                <div class="mb-4">Type the Agent's Name, Email or BrightMLS ID</div>
                                                <input type="text" class="custom-form-element form-input agent-search" data-type="list" data-label="Enter Agent's Name, Email or ID" autocomplete="agentsearch">
                                                <div class="search-results-container">
                                                    <div class="list-group search-results bg-white p-2 border w-100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input required" id="ListAgentFirstName" name="ListAgentFirstName" value="{{ $property -> ListAgentFirstName }}" data-label="Agent First Name">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input required" id="ListAgentLastName" name="ListAgentLastName" value="{{ $property -> ListAgentLastName }}" data-label="Agent Last Name">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input required" id="ListAgentOfficeName" name="ListAgentOfficeName" value="{{ $property -> ListOfficeName }}" data-label="Agent Company">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input" id="ListAgentMlsId" name="ListAgentMlsId" value="{{ $property -> ListAgentMlsId }}" data-label="Agent MLS ID">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input phone numbers-only required" id="ListAgentPreferredPhone" name="ListAgentPreferredPhone" value="{{ $property -> ListAgentPreferredPhone }}" data-label="Agent Phone">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <input type="text" class="custom-form-element form-input" id="ListAgentEmail" name="ListAgentEmail" value="{{ $property -> ListAgentEmail }}" data-label="Agent Email">
                                    </div>
                                </div>

                                <input type="hidden" id="ListAgentOfficeStreet" name="ListAgentOfficeStreet" value="{{ $office -> OfficeAddress1 ?? null }}">
                                <input type="hidden" id="ListAgentOfficeCity" name="ListAgentOfficeCity" value="{{ $office -> OfficeCity ?? null }}">
                                <input type="hidden" id="ListAgentOfficeState" name="ListAgentOfficeState" value="{{ $office -> OfficeStateOrProvince ?? null }}">
                                <input type="hidden" id="ListAgentOfficeZip" name="ListAgentOfficeZip" value="{{ $office -> OfficePostalCode ?? null }}">

                            </div>

                        </div>

                    </div>
                </div>

                @endif

                @if($property -> SaleRent != 'rental')

                    <div class="container shadow mb-4 py-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="h4 step-title text-gray my-2">Dates</div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="p-3">
                                            <div class="h5 text-orange mb-3">Contract Date</div>
                                            <input type="text" class="custom-form-element form-input datepicker required" name="ContractDate" id="ContractDate" data-label="Contract Date" value="{{ ($property -> ContractDate != '0000-00-00' ? $property -> ContractDate : '') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="p-3">
                                            <div class="h5 text-orange mb-3">Settlement Date</div>
                                            <input type="text" class="custom-form-element form-input datepicker required" name="CloseDate" id="CloseDate" data-label="Settlement Date" value="{{ ($property -> CloseDate != '0000-00-00' ? $property -> CloseDate : '') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container shadow mb-4 py-3">
                        <div class="row">
                            <div class="col-12">

                                <div class="h4 step-title text-gray my-2">Title and Earnest</div>

                                <div class="p-3">

                                    <div class="h5 text-orange mb-3">Are the Buyer's using Heritage Title?</div>
                                    <div class="mr-2 using-heritage">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="UsingHeritage" id="UsingHeritage" data-label="Using Heritage">
                                            <option value=""></option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                            <option value="not_sure">Not Sure Yet</option>
                                        </select>
                                    </div>

                                    <div class="not-using-heritage">
                                        <input type="text" class="custom-form-element form-input" name="TitleCompany" id="TitleCompany" data-label="Title Company">
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="p-3">
                                    <div class="h5 text-orange mb-3">Earnest Deposit Amount</div>
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only" name="EarnestAmount" id="EarnestAmount" data-label="Amount">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-3">
                                    <div class="h5 text-orange mb-3">Earnest Held By</div>
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" name="EarnestHeldBy" id="EarnestHeldBy" data-label="Held By">
                                        <option value=""></option>
                                        <option value="us">Taylor/Anne Arundel Properties</option>
                                        <option value="other_company">Other Real Estate Company</option>
                                        <option value="title">Title Company/Attorney</option>
                                        <option value="heritage_title">Heritage Title</option>
                                        <option value="builder">Builder</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                @else



                    <div class="container shadow mb-4 py-3">

                        <div class="row">
                            <div class="col-12 col-md-6">

                                <div class="h4 step-title text-gray my-2">Dates</div>

                                <div class="p-3">
                                    <div class="h5 text-orange mb-3">Lease Date</div>
                                    <input type="text" class="custom-form-element form-input datepicker required" name="CloseDate" id="CloseDate" data-label="Lease Date" value="{{ ($property -> CloseDate != '0000-00-00' ? $property -> CloseDate : '') }}">
                                </div>

                            </div>
                        </div>

                    </div>

                @endif

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center w-100 h-100">
                            <button id="save_required_details" class="waves-effect waves-light btn btn-lg btn-success">Continue <i class="fa fa-arrow-circle-right ml-3"></i></button>
                        </div>
                    </div>
                </div>



                <input type="hidden" name="Contract_ID" value="{{ $property -> Contract_ID }}">
                <input type="hidden" name="Agent_ID" value="{{ $property -> Agent_ID }}">
                <input type="hidden" name="transaction_type" id="transaction_type" value="{{ $transaction_type }}">
                <input type="hidden" id="for_sale" value="{{ $for_sale ? 'sale' : 'rental' }}">

            </form>


        </div>
    </div>


    <div class="modal fade draggable" id="import_contact_modal" tabindex="-1" role="dialog" aria-labelledby="import_contact_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="import_contact_modal_title">Select Contacts</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="table-responsive text-nowrap">
                            <table id="contacts_table" class="table table-hover table-bordered table-sm" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Type</th>
                                        <th>Last</th>
                                        <th>First</th>
                                        <th>Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                    <tr>
                                        <td>
                                            <a href="javascript: void(0)"
                                            class="btn btn-sm btn-primary add-contact-button"
                                            data-contact-id="{{ $contact -> id }}"
                                            data-contact-first="{{ $contact -> contact_first }}"
                                            data-contact-last="{{ $contact -> contact_last }}"
                                            data-contact-company="{{ $contact -> contact_company }}"
                                            data-contact-phone="{{ $contact -> contact_phone_cell }}"
                                            data-contact-email="{{ $contact -> contact_email }}"
                                            data-contact-street="{{ $contact -> contact_street }}"
                                            data-contact-city="{{ $contact -> contact_city }}"
                                            data-contact-state="{{ $contact -> contact_state }}"
                                            data-contact-zip="{{ $contact -> contact_zip }}"
                                            >Import</a>
                                        </td>
                                        <td>{{ $resource_items -> GetResourceName($contact -> contact_type_id) }}</td>
                                        <td>{{ $contact -> contact_last }}</td>
                                        <td>{{ $contact -> contact_first }}</td>
                                        <td>{{ $contact -> contact_street.' '.$contact -> contact_city.', '.$contact -> contact_state.' '.$contact -> contact_zip }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
