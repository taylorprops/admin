@extends('layouts.main')
@section('title', 'Required Details')

@section('content')

<script>
    let states = JSON.parse({!!json_encode($states_json)!!});
</script>
<div class="container-1000 page-container page-required-details mx-auto mb-5 pb-5">
    <div class="row">
        <div class="col-12 col-sm-8 mb-5 mx-auto">

            <div class="h3 text-orange mt-3 mt-sm-4 text-center w-100">{{ $property -> FullStreetAddress }} {{ $property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode }}</div>

            <div class="h4 text-primary my-3 text-center">Just a few more details</div>

            <form id="details_form" autocomplete="off">
                <input autocomplete="false" name="hidden" type="text" style="display:none;">

                <div class="container shadow mb-4 py-3">

                    <div class="row">

                        <div class="col-12 seller-container">

                            <div class="h4 text-gray my-2">{{ $for_sale ? 'Seller' : 'Owner' }}(s)</div>

                            <input type="checkbox" class="custom-form-element form-checkbox bank-trust" data-member="seller" data-label="{{ $for_sale ? 'Seller' : 'Owner' }} is a Trust, Company or other Entity">

                            <div class="seller-div mb-3">

                                <div class="h5 text-orange seller-header">{{ $for_sale ? 'Seller' : 'Owner' }} 1</div>

                                <a href="javascript: void(0)" class="btn btn-sm btn-primary ml-0 import-from-contacts-button" data-member="seller" data-member-id="1"><i class="fad fa-user-friends mr-2"></i> Import from Contacts</a>

                                <div class="row bank-trust-row hide">
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input required" name="seller_entity_name" data-label="Trust, Company or other Entity Name">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_first_name[]" data-label="First Name">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input required" name="seller_last_name[]" data-label="Last Name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input phone required" name="seller_phone[]" data-label="Phone">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input" name="seller_email[]" data-label="Email">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="text" class="custom-form-element form-input seller-street required" name="seller_street[]" data-label="Home Address">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="custom-form-element form-input seller-city required" name="seller_city[]" data-label="City">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="custom-form-element form-select seller-state required" name="seller_state[]" data-label="State">
                                            <option value=""></option>
                                            @foreach($states as $state)
                                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="text" class="custom-form-element form-input seller-zip required" name="seller_zip[]" data-label="Zip Code">
                                    </div>
                                    <input type="hidden" name="seller_crm_contact_id[]">
                                </div>
                            </div> {{-- end seller-div --}}

                        </div>

                        <div class="col-12">
                            <a href="javascript: void(0);" class="btn btn-sm btn-primary add-member-button" data-type="listing" data-member="seller"><i class="fal fa-plus mr-2"></i> Add {{ $for_sale ? 'Seller' : 'Owner' }}</a>
                        </div>

                    </div>

                </div>

                <div class="container shadow mb-4 py-3">

                    <div class="row">
                        <div class="col-12">

                            <div class="h4 text-gray my-2">Dates</div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="p-3">
                                        <div class="h5 text-orange mb-3">List Date</div>
                                        <input type="date" class="custom-form-element form-input date-field required" name="MLSListDate" id="MLSListDate" data-label="List Date" value="{{ $property -> MLSListDate != '' && $property -> MLSListDate != '0000-00-00' ? $property -> MLSListDate : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3">
                                        <div class="h5 text-orange mb-3">Expiration Date</div>
                                        <input type="date" class="custom-form-element form-input date-field required" name="ExpirationDate" id="ExpirationDate" data-label="Expiration Date" value="{{ $property -> ExpirationDate }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center w-100 h-100">
                            <button id="save_required_details" class="waves-effect waves-light btn btn-lg btn-primary p-4 font-13">Continue <i class="fal fa-arrow-circle-right ml-3"></i></button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="Listing_ID" value="{{ $property -> Listing_ID }}">
                <input type="hidden" name="Agent_ID" value="{{ $property -> Agent_ID }}">
                <input type="hidden" name="transaction_type" id="transaction_type" value="{{ $transaction_type }}">
                <input type="hidden" id="for_sale" value="{{ $for_sale ? 'sale' : 'rental' }}">

            </form>


        </div>
    </div>


    <div class="modal fade draggable" id="import_contact_modal" tabindex="-1" role="dialog" aria-labelledby="import_contact_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="import_contact_modal_title">Select Contacts</h4>
                    <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2 fa-lg"></i>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="no-wrap">
                            <table id="contacts_table" class="table table-hover table-bordered table-sm" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                    <tr class="add-contact-row"
                                    data-contact-id="{{ $contact -> id }}"
                                    data-contact-first="{{ $contact -> contact_first }}"
                                    data-contact-last="{{ $contact -> contact_last }}"
                                    data-contact-company="{{ $contact -> contact_company }}"
                                    data-contact-phone="{{ $contact -> contact_phone_cell }}"
                                    data-contact-email="{{ $contact -> contact_email }}"
                                    data-contact-street="{{ $contact -> contact_street }}"
                                    data-contact-city="{{ $contact -> contact_city }}"
                                    data-contact-state="{{ $contact -> contact_state }}"
                                    data-contact-zip="{{ $contact -> contact_zip }}">
                                        <td>{{ $contact -> contact_last.', '.$contact -> contact_first }}</td>
                                        <td class="w-60">{{ $contact -> contact_street.' '.$contact -> contact_city.', '.$contact -> contact_state.' '.$contact -> contact_zip }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
