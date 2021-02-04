@extends('layouts.main')
@section('title', $property -> FullStreetAddress.' '.$property -> City.' '.$property -> StateOrProvince.' '.$property -> PostalCode )

@section('content')

<div class="container page-container page-transaction-details mb-5">

    <div id="details_header" class="animate__animated animate__slow animate__fadeIn"></div>

    <span id="scroll_to"></span>

    <div class="row animate__animated animate__slower animate__fadeIn">
        <div class="col-md-12 px-1 px-sm-3 mt-3 details-tabs">
            <ul id="tabs" class="nav nav-tabs details-list-group">

                <li class="nav-item"><a href="javascript: void(0)" data-tab="details" data-target="#details_tab" data-toggle="tab" class="nav-link transaction-details-nav-link active"><i class="fad fa-home-lg-alt mr-2 d-none d-md-inline-block"></i> Details</a></li>

                @if($transaction_type != 'referral')
                    <li class="nav-item"><a href="javascript: void(0)" data-tab="members" data-target="#members_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-user-friends mr-2 d-none d-md-inline-block"></i> Members</a></li>
                @endif

                <li class="nav-item"><a href="javascript: void(0)" data-tab="documents"  data-target="#documents_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-folder-open mr-2 d-none d-md-inline-block"></i> Documents</a></li>

                <li class="nav-item"><a href="javascript: void(0)" data-tab="checklist"  data-target="#checklist_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-tasks mr-2 d-none d-md-inline-block"></i> Checklist</a></li>

                @if($transaction_type == 'listing')

                    <li class="nav-item"><a href="javascript: void(0)" data-tab="contracts"  data-target="#contracts_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-file-signature mr-2 d-none d-md-inline-block"></i> {{ $for_sale ? 'Contracts' : 'Leases' }}</a></li>

                @else

                    @php
                    // agent and admin have different commission tabs
                    $commission = 'commission';
                    if(auth() -> user() -> group == 'agent') {
                        $commission = 'agent_commission';
                    } else if(auth() -> user() -> group == 'referral') {
                        $commission = 'referral_commission';
                    }
                    @endphp

                    {{-- show listing link if exists --}}
                    <li class="nav-item"><a href="javascript: void(0)" data-tab="{{ $commission }}"{{--  id="open_{{ $commission }}_tab" --}} data-target="#{{ $commission }}_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-sack-dollar mr-2 d-none d-md-inline-block"></i> Commission</a></li>

                    @if($for_sale && auth() -> user() -> group == 'admin')
                        <li class="nav-item"><a href="javascript: void(0)" data-tab="earnest" data-target="#earnest_tab" data-toggle="tab" class="nav-link transaction-details-nav-link"><i class="fad fa-envelope-open-dollar mr-2 d-none d-md-inline-block"></i> Earnest</a></li>
                    @endif

                @endif
            </ul>

            <div id="details_tabs" class="tab-content details-main-tabs">
                <div id="details_tab" class="tab-pane fade active show">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="members_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="documents_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="checklist_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="contracts_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @if(auth() -> user() -> group == 'admin')
                <div id="commission_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                <div id="earnest_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @elseif(auth() -> user() -> group == 'agent')
                <div id="agent_commission_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @elseif(auth() -> user() -> group == 'referral')
                <div id="referral_commission_tab" class="tab-pane fade">
                    <div class="w-100 my-5 text-center">
                        {!! config('global.vars.loader') !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" id="address" value="{{ $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode }}">
    <input type="hidden" id="Listing_ID" value="{{ $property -> Listing_ID }}">
    <input type="hidden" id="Contract_ID" value="{{ $property -> Contract_ID }}">
    <input type="hidden" id="Referral_ID" value="{{ $property -> Referral_ID }}">
    <input type="hidden" id="Agent_ID" value="{{ $property -> Agent_ID }}">
    <input type="hidden" id="User_ID" value="{{ auth() -> user() -> id }}">
    <input type="hidden" id="Commission_ID" value="{{ $property -> Commission_ID }}">
    <input type="hidden" id="transaction_type" value="{{ $transaction_type }}">
    <input type="hidden" id="questions_confirmed" value="{{ $questions_confirmed }}">
    <input type="hidden" id="for_sale" value="{{ $for_sale == true ? 'yes' : 'no' }}">

</div>

{{-- ******** Modals ******** --}}

{{-- details --}}
{{-- import property data from mls --}}
<div class="modal fade draggable" id="confirm_import_modal" tabindex="-1" role="dialog" aria-labelledby="import_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="import_title">Confirm Import</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body"> </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary modal-confirm-button" id="confirm_import_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

{{-- members --}}
<div class="modal fade draggable" id="confirm_delete_member_modal" tabindex="-1" role="dialog" aria-labelledby="delete_member_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="delete_member_title">Confirm</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container text-center">Delete Member?</div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary modal-confirm-button" id="delete_member_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
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

                    <table id="contacts_table" class="table nowrap table-hover table-sm" width="100%">
                        <thead>
                            <tr>
                                <th></th>
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
                                    data-contact-type-id="{{ $contact -> contact_type_id }}"
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
                                <td>{{ $contact -> contact_last }}</td>
                                <td>{{ $contact -> contact_first }}</td>
                                <td>{{ $contact -> contact_street.' '.$contact -> contact_city.', '.$contact -> contact_state.' '.$contact -> contact_zip }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>

{{-- documents --}}

<div class="modal fade draggable" id="send_email_modal" tabindex="-1" role="dialog" aria-labelledby="send_email_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="send_email_modal_title">Email Documents</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="container">

                    <div class="row">

                        <div class="col-12">

                            <div class="p-2">

                                <div class="row">
                                    <div class="col-2">
                                        <div class="h-100 d-flex justify-content-end align-items-center">
                                            <div>From:</div>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <input type="text" class="custom-form-element form-input" id="email_from" value="{{ \Auth::user() -> name }} <{{ \Auth::user() -> email }}>">
                                    </div>
                                </div>


                                @if($members)

                                    @foreach($members as $member)

                                        <div class="row to-addresses">
                                            <div class="col-2">
                                                @if($loop -> first)
                                                    <input type="hidden" class="email-address-type" value="to">
                                                    <div class="h-100 d-flex justify-content-end align-items-center">
                                                        <div>To:</div>
                                                    </div>
                                                @else
                                                    <select class="custom-form-element form-select form-select-no-cancel form-select-no-search email-address-type">
                                                        <option value="to">To:</option>
                                                        <option value="cc">Cc:</option>
                                                        <option value="bcc">Bcc:</option>
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="@if($loop -> first) col-10 @else col-9 @endif">
                                                <input type="text" class="custom-form-element form-input email-to-address" value="{{ $member -> first_name.' '.$member -> last_name }} <{{ $member -> email }}>">
                                            </div>
                                            @if(!$loop -> first)
                                            <div class="col-1">
                                                <div class="h-100 d-flex justify-content-end align-items-center">
                                                    <button class="btn btn-sm btn-danger delete-address-button"><i class="fal fa-times delete-address-button"></i></button>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                    @endforeach

                                @else

                                    <div class="row to-addresses">
                                        <div class="col-2">
                                            <input type="hidden" class="email-address-type" value="to">
                                            <div class="h-100 d-flex justify-content-end align-items-center">
                                                <div>To:</div>
                                            </div>
                                        </div>
                                        <div class="col-10">
                                            <input type="text" class="custom-form-element form-input email-to-address" value="">
                                        </div>
                                    </div>

                                @endif

                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-10">
                                        <a class="add-address-button"><i class="fal fa-plus mr-1 text-success"></i> Add Recipient</a>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <hr>
                <div class="container mt-2">

                    <div class="row">

                        <div class="col-12">

                            <div class="p-2">

                                <div class="row">
                                    <div class="col-2">
                                        <div class="h-100 d-flex justify-content-end align-items-center">
                                            <div>Subject:</div>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <input type="text" class="custom-form-element form-input" id="email_subject" value="Documents - {{ $property -> FullStreetAddress }} {{ $property -> City }}, {{ $property -> StateOrProvince }} {{ $property -> PostalCode }}">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <div class="h-100 d-flex justify-content-end align-items-center">
                                            <div>Attachments:</div>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <div class="w-100 border p-2" id="email_attachments"></div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-2">
                                        <div class="h-100 d-flex justify-content-end align-items-center">
                                            <div>Message:</div>
                                        </div>
                                    </div>
                                    <div class="col-10">
                                        <textarea class="custom-form-element form-textarea" id="email_message" rows="4">&#13;&#10; &#13;&#10; Thank you,&#13;&#10; {{ \Auth::user() -> name }}</textarea>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <div class="d-flex justify-content-around pb-3">
                <a class="btn btn-primary" id="send_email_button"><i class="fad fa-share mr-2"></i> Send Email</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_individual_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_individual_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="add_individual_template_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="add_individual_template_modal_title">Add Individual Template Documents</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row mt-2 mb-3">
                                    <div class="col-12 col-lg-6">
                                        <select class="custom-form-element form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                                            <option value="all" selected>All</option>
                                            @foreach($form_groups as $form_group)
                                                <option value="{{ $form_group -> resource_id }}">{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div id="form_search_div">
                                            <input type="text" class="custom-form-element form-input form-search" data-label="Search All Forms">
                                        </div>
                                    </div>

                                </div>

                                {{-- <div class="row">

                                    <div class="col-12 col-lg-6 mt-1 mt-lg-0">
                                        <select class="custom-form-element form-select form-select-no-search" id="form_categories_search" multiple data-label="Search Form Tags">
                                            <option value="all">All</option>
                                            @foreach($form_categories as $form_category)
                                            <option value="{{ $form_category -> resource_id }}">{{ $form_category -> resource_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="form-groups-container p-1 p-md-3 p-lg-4 mt-2">

                                    @foreach($form_groups as $form_group)

                                    <ul class="list-group form-group-div" data-form-group-id="{{ $form_group -> resource_id }}">
                                        <li class="h5 text-orange list-group-header @if($loop -> first) mt-3 @else mt-1 @endif">
                                            {{ $form_group -> resource_state }}
                                            @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif
                                        </li>

                                        @php
                                        $forms = $available_files -> formGroupFiles($form_group -> resource_id, $Listing_ID, $Contract_ID, $transaction_type);
                                        $forms_available = $forms['forms_available'];
                                        /* $forms_in_use = null;
                                        if($forms['forms_in_use']){
                                            $forms_in_use = $forms['forms_in_use'] -> toArray();
                                        } */
                                        @endphp

                                        @foreach($forms_available as $form)

                                            @php
                                            $form_categories = preg_replace('/,/', ' ', $form -> form_categories);
                                            $form_status_class = '';
                                            /* if(in_array($form -> file_id, $forms_in_use)) {
                                                //$form_status_class = 'form-in-use';
                                            } */
                                            @endphp

                                            <li class="list-group-item form-name p-1 {{ $form_status_class }}" data-form-id="{{ $form -> file_id }}" data-template-id="{{ $form -> template_id }}" data-text="{{ $form -> file_name_display }}" data-tags="{{ $form_categories }}">

                                                <div class="d-flex justify-content-between align-items-center">

                                                    <div class="d-flex justify-content-start align-items-center">

                                                        <div class="ml-3">

                                                            <input type="checkbox" class="custom-form-element form-checkbox individual-template-form"
                                                                id="template_form_{{ $form -> file_id }}"
                                                                data-file-id="{{ $form -> file_id }}"
                                                                data-template-id="{{ $form -> template_id }}"
                                                                data-file-name="{{ $form -> file_name }}"
                                                                data-file-name-display="{{ $form -> file_name_display }}"
                                                                data-pages-total="{{ $form -> pages_total }}"
                                                                data-file-location="{{ $form -> file_location }}"
                                                                data-file-size="{{ get_mb(filesize(Storage::disk('public') -> path(str_replace('/storage/', '', $form -> file_location)))) }}"
                                                                >

                                                        </div>

                                                        <label class="mt-3" for="template_form_{{ $form -> file_id }}" title="{{ $form -> file_name_display }}">
                                                            <a class="text-primary pointer">{{ shorten_text($form -> file_name_display, 65) }}</a>
                                                        </label>

                                                    </div>

                                                    <div>
                                                        <a href="{{ $form -> file_location }}" class="btn btn-sm btn-primary" target="_blank"><i class="fad fa-eye mr-2"></i> View File</a>
                                                    </div>

                                                    {{-- <div class="col-12 col-lg-3 d-none d-lg-block">
                                                        @foreach(explode(' ', $form_categories) as $tag)
                                                        <span class="badge badge-pill form-pill text-white ml-1" style="background-color: {{ $resource_items -> GetCategoryColor($tag) }}">{{ $resource_items -> getResourceName($tag) }}</span>
                                                        @endforeach
                                                    </div> --}}

                                                </div>

                                            </li>

                                        @endforeach

                                    </ul>

                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-4 mx-auto">
                                <div class="my-5">
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="individual_templates_folder" data-label="Select Folder To Add Forms To">
                                        @foreach($folders as $folder)
                                            @php
                                            $folder_name = $folder -> folder_name;
                                            if($for_sale == false) {
                                                $folder_name = str_replace('Contract', 'Lease', $folder_name);
                                            }

                                            if($transaction_type == 'listing') {
                                                $selected_folder = 'Listing Documents';
                                            } else if($transaction_type == 'contract') {
                                                $selected_folder = 'Contract Documents';
                                                if($for_sale == false) {
                                                    $selected_folder = 'Lease Documents';
                                                }
                                            } else if($transaction_type == 'referral') {
                                                $selected_folder = 'Referral Documents';
                                            }
                                            @endphp
                                            @if($folder -> folder_name != 'Trash')
                                            <option value="{{ $folder -> id }}" @if($selected_folder == $folder_name) selected @endif >{{ $folder_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_add_individual_template_button"><i class="fal fa-plus mr-2"></i> Add Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_checklist_template_modal" tabindex="-1" role="dialog" aria-labelledby="add_checklist_template_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="add_checklist_template_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="add_checklist_template_modal_title">Add Checklist Template Documents</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-primary">Import the selected templates below</div>
                                <br>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-xl-4">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="checklist_templates_folder" data-label="Select Folder">
                                            @foreach($folders as $folder)
                                                @php
                                                $folder_name = $folder -> folder_name;
                                                if($for_sale == false) {
                                                    $folder_name = str_replace('Contract', 'Lease', $folder_name);
                                                }

                                                if($transaction_type == 'listing') {
                                                    $selected_folder = 'Listing Documents';
                                                } else if($transaction_type == 'contract') {
                                                    $selected_folder = 'Contract Documents';
                                                    if($for_sale == false) {
                                                        $selected_folder = 'Lease Documents';
                                                    }
                                                } else if($transaction_type == 'referral') {
                                                    $selected_folder = 'Referral Documents';
                                                }

                                                @endphp
                                                @if($folder -> folder_name != 'Trash')
                                                <option value="{{ $folder -> id }}" @if($selected_folder == $folder_name) selected @endif >{{ $folder_name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <ul class="list-group mt-2 template-docs-div">

                                    <div class="h5 text-orange">Required Documents</div>

                                    @foreach($checklist_items_required as $checklist_item_required)

                                        @php
                                        // get if required
                                        $checklist_form_required = $available_files -> where('file_id', $checklist_item_required -> checklist_form_id) -> first();
                                        @endphp
                                        @if($checklist_form_required && $checklist_form_required -> file_location != '')
                                            <li class="list-group-item p-0 border-top-0 border-left-0 border-right-0 border-bottom">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div>
                                                        <input type="checkbox" class="custom-form-element form-checkbox checklist-template-form"
                                                        data-file-id="{{ $checklist_form_required -> file_id }}"
                                                        data-template-id="{{ $checklist_form_required -> template_id }}"
                                                        data-file-name="{{ $checklist_form_required -> file_name }}"
                                                        data-file-name-display="{{ $checklist_form_required -> file_name_display }}"
                                                        data-pages-total="{{ $checklist_form_required -> pages_total }}"
                                                        data-file-location="{{ $checklist_form_required -> file_location }}"
                                                        data-file-size="{{ get_mb(filesize(Storage::disk('public') -> path(str_replace('/storage/', '', $checklist_form_required -> file_location)))) }}"
                                                        checked>
                                                    </div>
                                                    <div class="ml-3 mt-1">
                                                        <a href="javascript: void(0)">{{ $checklist_form_required -> file_name_display }}</a>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach

                                    <div class="h5 text-orange mt-4">If Applicable Documents</div>

                                    @foreach($checklist_items_if_applicable as $checklist_item_if_applicable)
                                        @php
                                        // get if applicable
                                        $checklist_form_if_applicable = $available_files -> where('file_id', $checklist_item_if_applicable -> checklist_form_id) -> first();
                                        @endphp
                                        @if($checklist_form_if_applicable -> file_location != '')
                                            <li class="list-group-item border-top-0 border-left-0 border-right-0 border-bottom">
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div>
                                                        <input type="checkbox" class="custom-form-element form-checkbox checklist-template-form"
                                                        data-file-id="{{ $checklist_form_if_applicable -> file_id }}"
                                                        data-template-id="{{ $checklist_form_if_applicable -> template_id }}"
                                                        data-file-name="{{ $checklist_form_if_applicable -> file_name }}"
                                                        data-file-name-display="{{ $checklist_form_if_applicable -> file_name_display }}"
                                                        data-pages-total="{{ $checklist_form_if_applicable -> pages_total }}"
                                                        data-file-location="{{ $checklist_form_if_applicable -> file_location }}"
                                                        data-file-size="{{ get_mb(filesize(Storage::disk('public') -> path(str_replace('/storage/', '', $checklist_form_if_applicable -> file_location)))) }}"
                                                        >
                                                    </div>
                                                    <div class="ml-3">
                                                        <a href="javascript:void(0)">{{ $checklist_form_if_applicable -> file_name_display }}</a>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_add_checklist_template_button"><i class="fal fa-plus mr-2"></i> Add Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="upload_documents_modal" tabindex="-1" role="dialog" aria-labelledby="upload_documents_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="upload_documents_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="upload_documents_modal_title">Upload Documents</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="documents_folder" data-label="Select Folder">
                                    @foreach($folders as $folder)
                                        @php
                                        $folder_name = $folder -> folder_name;
                                        if($for_sale == false) {
                                            $folder_name = str_replace('Contract', 'Lease', $folder_name);
                                        }

                                        if($transaction_type == 'listing') {
                                            $selected_folder = 'Listing Documents';
                                        } else if($transaction_type == 'contract') {
                                            $selected_folder = 'Contract Documents';
                                            if($for_sale == false) {
                                                $selected_folder = 'Lease Documents';
                                            }
                                        } else if($transaction_type == 'referral') {
                                            $selected_folder = 'Referral Documents';
                                        }
                                        @endphp
                                        @if($folder -> folder_name != 'Trash')
                                        <option value="{{ $folder -> id }}" @if($selected_folder == $folder -> folder_name) selected @endif >{{ $folder_name }}</option>
                                        @endif
                                    @endforeach
                                </select>

                                <div id="file_upload" class="dm-uploader p-5 mt-3">
                                    <h3 class="mb-5 mt-5 text-muted">Drag &amp; drop files here</h3>

                                    <div class="btn btn-primary btn-block mb-5">
                                        <span>Click to browse files</span>
                                        <input type="file" title='Click to browse files' />
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-7">
                                <div class="card h-100">
                                    <div class="card-header bg-primary text-white">
                                        Pending File List
                                    </div>

                                    <ul class="list-unstyled p-2 d-flex flex-column col" id="files_queue">
                                        <li class="text-muted text-center empty">No files uploaded.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_upload_documents_button"><i class="fad fa-upload mr-2"></i> Upload Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="move_documents_modal" tabindex="-1" role="dialog" aria-labelledby="move_documents_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="move_documents_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="move_documents_modal_title">Move Documents</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">Move Documents To:</div>
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="move_documents_folder" data-label="Select Folder">
                                    @foreach($folders as $folder)
                                        @php
                                        $folder_name = $folder -> folder_name;
                                        if($for_sale == false) {
                                            $folder_name = str_replace('Contract', 'Lease', $folder_name);
                                        }
                                        @endphp
                                        @if($folder_name != 'Trash')
                                            <option value="{{ $folder -> id }}">{{ $folder_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_move_documents_button"><i class="fad fa-dolly mr-2"></i> Move Documents</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_to_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="add_to_checklist_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xxl" role="document">
        <div class="modal-content">
            <form id="add_to_checklist_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="add_to_checklist_modal_title">Assign To Checklist</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body p-2">
                    <div class="container p-0 p-sm-1 p-md-2">
                        <div class="row">
                            <div class="col-12">
                                <div id="add_items_to_checklist_div"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer save-add-to-checklist-footer d-flex justify-content-around">
                    <a class="btn btn-primary btn-lg" id="save_add_to_checklist_button" data-dismiss="modal" data-checklist-id="{{ $checklist_id }}"><i class="fad fa-save mr-2"></i> Save Checklist Changes</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_folder_modal" tabindex="-1" role="dialog" aria-labelledby="add_folder_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="add_folder_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="add_folder_modal_title">Add Folder</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <div class="h5 text-primary text-center mb-4">Enter Folder Name <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Adding a Folder" data-content="You can create multiple folders for different types of documents. Examples include 'Original Files', 'Signed Docs'"><i class="fad fa-question-circle ml-2"></i></a>
                        </div>
                        <input type="text" class="custom-form-element form-input required" id="new_folder_name" data-label="Folder Name">
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-primary" id="save_add_folder_button"><i class="fad fa-save mr-2"></i> Save Folder</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_document_modal" tabindex="-1" role="dialog" aria-labelledby="delete_document_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="delete_document_title">Delete Document</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            Are you sure you want to move this form to Trash?
                            <br>
                            <div class="font-weight-bold text-primary mt-2" id="delete_document_name"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" data-dismiss="modal" id="confirm_delete_document_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_folder_modal" tabindex="-1" role="dialog" aria-labelledby="delete_folder_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="delete_folder_title">Delete Folder</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            This will remove the folder and place all of its forms in the Trash Folder.<br>Continue?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" id="confirm_delete_folder_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_documents_modal" tabindex="-1" role="dialog" aria-labelledby="delete_documents_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="delete_documents_title">Move To Trash</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    Move Documents To Trash?
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" id="confirm_delete_documents_button" data-dismiss="modal"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="image_zoom_modal" tabindex="-1" role="dialog" aria-labelledby="image_zoom_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="image_zoom_div" class="text-center"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="split_document_modal" tabindex="-1" role="dialog" aria-labelledby="split_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xxl" id="split_document_modal_dialog" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="split_document_modal_title">Split Document</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body py-0 px-2">
                <div id="split_document_container"></div>
                <input type="hidden" id="folder_id">
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-around align-items-center w-100">
                    <button type="button" class="btn btn-lg btn-primary p-3 modal-dismiss" data-dismiss="modal"><i class="fal fa fa-times mr-2"></i> Finish and Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="rename_document_modal" tabindex="-1" role="dialog" aria-labelledby="rename_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="rename_document_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="rename_document_modal_title">Rename Document</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">

                                <div class="text-gray">
                                    Enter New Name
                                </div>
                                <input type="text" class="custom-form-element form-input" id="new_document_name" data-title="Enter Document Name">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_rename_document_button" data-dismiss="modal"><i class="fad fa-save mr-2"></i> Save Name</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_matches_modal" tabindex="-1" role="dialog" aria-labelledby="matches_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="matches_title">Matches Found</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-start align-items-center">
                                <div><i class="fal fa-check-circle fa-2x mr-3 text-success"></i></div>
                                <div>We have found <span id="match_count"></span> documents that can be automatically assigned. Would you like for us to add them?</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal" id="cancel_matches_button"><i class="fal fa-times mr-2"></i> Do Not Add Them</a>
                <a class="btn btn-primary modal-confirm-button" id="confirm_matches_button"><i class="fal fa-check mr-2"></i> Yes, Add Them!</a>
            </div>
        </div>
    </div>
</div>

{{-- checklist --}}
<div class="modal fade draggable " id="change_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="change_checklist_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="change_checklist_modal_title">Change Checklist</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary">Edit the options below</h5>

                            <form id="change_checklist_form">

                                <div class="container property-options">

                                    <div class="row my-3">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="listing_type" id="listing_type" data-label="Sale/Rental" required>
                                                <option value="sale" @if($checklist -> checklist_sale_rent == 'sale') selected @endif>Sale</option>
                                                <option value="rental" @if($checklist -> checklist_sale_rent == 'rental') selected @endif>Rental</option>
                                                <option value="both" @if($checklist -> checklist_sale_rent == 'both') selected @endif>Both</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="property_type" id="property_type" data-label="Listing Type" required>
                                                @foreach($property_types as $property_type)
                                                <option value="{{ $property_type -> resource_name}}" @if($property_type -> resource_id == $checklist -> checklist_property_type_id) selected @endif>{{ $property_type -> resource_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 property-sub-type">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel transaction-option-trigger required" name="property_sub_type" id="property_sub_type" data-label="Property Type" required>
                                                @foreach($property_sub_types as $property_sub_type)
                                                    @if($property_sub_type -> resource_name != 'For Sale By Owner')
                                                    <option value="{{ $property_sub_type -> resource_name }}" @if($property_sub_type -> resource_id == $checklist -> checklist_property_sub_type_id) selected @endif>{{ $property_sub_type -> resource_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 hoa disclosure">
                                        <div class="col-12">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" name="hoa_condo" id="hoa_condo" data-label="HOA/Condo Association" required>
                                                <option value="hoa" @if($transaction_checklist_hoa_condo == 'hoa') selected @endif>HOA Fees</option>
                                                <option value="condo" @if($transaction_checklist_hoa_condo == 'condo') selected @endif>Condo Fees</option>
                                                <option value="none" @if($transaction_checklist_hoa_condo == 'none') selected @endif>None</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row my-3 year-built">
                                        <div class="col-12">
                                            <input type="text" class="custom-form-element form-input numbers-only required" name="year_built" id="year_built" value="{{ $transaction_checklist_year_built }}" data-label="Year Built" required>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_change_checklist_button"><i class="fad fa-save mr-2"></i> Save</a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_change_checklist_modal" tabindex="-1" role="dialog" aria-labelledby="change_checklist_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="change_checklist_title">Change Checklist Title</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <i class="fad fa-exclamation-triangle fa-lg text-danger mr-2"></i> The checklist will be replaced to include the documents required for the new listing checklist. Any relevant documents will be kept in the checklist but some may need to be added or replaced.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" id="confirm_change_checklist_button"><i class="fal fa-check mr-2"></i> Continue</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade draggable" id="add_document_modal" tabindex="-1" role="dialog" aria-labelledby="add_document_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="add_document_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="add_document_modal_title">Add Document To Checklist Item</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div id="documents_available_div"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                </div>
                <input type="hidden" id="add_document_checklist_id">
                <input type="hidden" id="add_document_checklist_item_id">
            </form>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_delete_checklist_item_doc_modal" tabindex="-1" role="dialog" aria-labelledby="delete_checklist_item_doc_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="delete_checklist_item_doc_title">Delete Document</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            Delete Document From Checklist Item?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" id="delete_checklist_item_doc_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

{{-- checklist and doc review shared --}}
@include('/agents/doc_management/transactions/details/shared/checklist_review_modals')

{{-- commission --}}

<div class="modal fade draggable" id="add_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="add_check_in_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl-1400" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_check_in_modal_title">Add Check In - <span id="add_check_in_address"></span></h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12 col-lg-5 border-right">

                        <form id="add_check_in_form" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Upload</div>
                                </div>
                                <div class="col-12">
                                    <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="check_in_upload" id="check_in_upload" data-label="Click to search or Drag and Drop files here"></div>
                                </div>
                                <div class="col-12">
                                    <div class="check-in-preview-div"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Check Details</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input datepicker required" name="check_in_date" id="check_in_date" data-label="Date On Check">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input numbers-only required" name="check_in_number" id="check_in_number" data-label="Check Number">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_in_amount" id="check_in_amount" data-label="Check Amount">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Dates</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input datepicker required" name="check_in_date_received" id="check_in_date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input datepicker" name="check_in_date_deposited" id="check_in_date_deposited" data-label="Date Deposited">
                                </div>
                            </div>

                        </form>

                        <div class="d-flex justify-content-around">
                            <button type="button" class="btn btn-primary" id="save_add_check_in_button"><i class="fad fa-save mr-2"></i> Save Check</button>
                        </div>

                    </div>

                    <div class="col-12 col-lg-7">
                        <h5 class="text-orange">Checks In Queue</h5>
                        <div class="checks-queue-div"></div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade draggable" id="edit_check_in_modal" tabindex="-1" role="dialog" aria-labelledby="edit_check_in_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="edit_check_in_modal_title">Edit Check</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_check_in_form">

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="edit-check-in-preview-div"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="h5 text-orange">Check Details</div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <input type="text" class="custom-form-element form-input datepicker required" name="edit_check_in_date" id="edit_check_in_date" data-label="Date On Check">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <input type="text" class="custom-form-element form-input numbers-only required" name="edit_check_in_number" id="edit_check_in_number" data-label="Check Number">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="edit_check_in_amount" id="edit_check_in_amount" data-label="Check Amount">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="h5 text-orange">Dates</div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <input type="text" class="custom-form-element form-input datepicker required" name="edit_check_in_date_received" id="edit_check_in_date_received" value="{{ date('Y-m-d') }}" data-label="Date Received">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <input type="text" class="custom-form-element form-input datepicker" name="edit_check_in_date_deposited" id="edit_check_in_date_deposited" data-label="Date Deposited">
                        </div>
                    </div>
                    <input type="hidden" name="edit_check_in_id" id="edit_check_in_id">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_edit_check_in_button" data-dismiss="modal"><i class="fad fa-save mr-2"></i> Save Check Details</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="add_check_out_modal" tabindex="-1" role="dialog" aria-labelledby="add_check_out_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_check_out_modal_title">Add Check Out</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_check_out_form" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="h5 text-orange mb-0">Upload</div>
                        </div>
                        <div class="col-12">
                            <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="check_out_upload" id="check_out_upload" data-label="Click to search or Drag and Drop files here"></div>
                        </div>
                        <div class="col-12">
                            <div class="check-out-preview-div"></div>
                        </div>
                    </div>

                    <div class="p-3">

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="check_out_date" id="check_out_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="check_out_number" id="check_out_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="check_out_amount" id="check_out_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Recipient</div>
                            </div>

                            <div class="col-col-12 col-sm-6">
                                <select class="custom-form-element form-select" name="check_out_agent_id" id="check_out_agent_id" data-label="Select Our Agent">
                                    <option value=""></option>
                                    @foreach($agents as $agent)
                                        @php
                                        $agent_name = $agent -> first_name . ' ' . $agent -> last_name;
                                        $recipient = $agent_name;
                                        if($agent -> llc_name != '') {
                                            $agent_name = $agent_name.' - '.$agent -> llc_name;
                                            $recipient = $agent -> llc_name;
                                        }
                                        @endphp
                                        <option value="{{ $agent -> id }}" data-recipient="{{ $recipient }}">{{ $agent_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input required" id="check_out_recipient" name="check_out_recipient" data-label="Check Recipient">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Delivery Method</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <select class="custom-form-element form-select form-select-no-search" id="check_out_delivery_method" name="check_out_delivery_method" data-label="Select Delivery Method">
                                    <option value=""></option>
                                    <option value="pickup">Picking Up</option>
                                    <option value="mail">Mailing To</option>
                                    <option value="fedex">FedEx</option>
                                    <option value="settlement">At Settlement</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" id="check_out_date_ready" name="check_out_date_ready" data-label="Date Ready">
                            </div>
                        </div>

                        <div class="mail-to-div">

                            <span class="text-gray">Enter the address to send the check to</span>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <input type="text" class="custom-form-element form-input required" id="check_out_mail_to_street" name="check_out_mail_to_street" data-label="Street Address">
                                </div>
                                <div class="col-3">
                                    <input type="text" class="custom-form-element form-input required" id="check_out_mail_to_city" name="check_out_mail_to_city" data-label="City">
                                </div>
                                <div class="col-2">
                                    <select class="custom-form-element form-select form-select-no-cancel required" id="check_out_mail_to_state" name="check_out_mail_to_state" data-label="State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="custom-form-element form-input required" id="check_out_mail_to_zip" name="check_out_mail_to_zip" data-label="Zip Code">
                                </div>
                            </div>

                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <button type="button" class="btn btn-primary" id="save_add_check_out_button"><i class="fad fa-save mr-2"></i> Save Check</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="edit_check_out_modal" tabindex="-1" role="dialog" aria-labelledby="edit_check_out_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="edit_check_out_modal_title">Edit Check</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_check_out_form">

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="edit-check-out-preview-div"></div>
                        </div>
                    </div>

                    <div class="p-3">

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Check Details</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker required" name="edit_check_out_date" id="edit_check_out_date" data-label="Date On Check">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input numbers-only required" name="edit_check_out_number" id="edit_check_out_number" data-label="Check Number">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="edit_check_out_amount" id="edit_check_out_amount" data-label="Check Amount">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Recipient</div>
                            </div>

                            <div class="col-col-12 col-sm-6">
                                <select class="custom-form-element form-select" name="edit_check_out_agent_id" id="edit_check_out_agent_id" data-label="Select Our Agent">
                                    <option value=""></option>
                                    @foreach($agents as $agent)
                                        @php
                                        $agent_name = $agent -> first_name . ' ' . $agent -> last_name;
                                        $recipient = $agent_name;
                                        if($agent -> llc_name != '') {
                                            $agent_name = $agent_name.' - '.$agent -> llc_name;
                                            $recipient = $agent -> llc_name;
                                        }
                                        @endphp
                                        <option value="{{ $agent -> id }}" data-recipient="{{ $recipient }}">{{ $agent_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input required" id="edit_check_out_recipient" name="edit_check_out_recipient" data-label="Check Recipient">
                            </div>
                        </div>

                        <div class="row mb-3 mt-3">
                            <div class="col-12">
                                <div class="h5 text-orange mb-0">Delivery Method</div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <select class="custom-form-element form-select form-select-no-search" id="edit_check_out_delivery_method" name="edit_check_out_delivery_method" data-label="Select Delivery Method">
                                    <option value=""></option>
                                    <option value="pickup">Picking Up</option>
                                    <option value="mail">Mailing To</option>
                                    <option value="fedex">FedEx</option>
                                    <option value="settlement">At Settlement</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <input type="text" class="custom-form-element form-input datepicker" id="edit_check_out_date_ready" name="edit_check_out_date_ready" data-label="Date Ready">
                            </div>
                        </div>

                        <div class="edit-mail-to-div">

                            <span class="text-gray">Enter the address to send the check to</span>
                            <div class="row mb-3">
                                <div class="col-5">
                                    <input type="text" class="custom-form-element form-input required" id="edit_check_out_mail_to_street" name="edit_check_out_mail_to_street" data-label="Street Address">
                                </div>
                                <div class="col-3">
                                    <input type="text" class="custom-form-element form-input required" id="edit_check_out_mail_to_city" name="edit_check_out_mail_to_city" data-label="City">
                                </div>
                                <div class="col-2">
                                    <select class="custom-form-element form-select form-select-no-cancel required" id="edit_check_out_mail_to_state" name="edit_check_out_mail_to_state" data-label="State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                            <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="custom-form-element form-input required" id="edit_check_out_mail_to_zip" name="edit_check_out_mail_to_zip" data-label="Zip Code">
                                </div>
                            </div>

                        </div>

                    </div>

                    <input type="hidden" name="edit_check_out_id" id="edit_check_out_id">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_edit_check_out_button" data-dismiss="modal"><i class="fad fa-save mr-2"></i> Save Check Details</a>
            </div>
        </div>
    </div>
</div>


{{-- earnest --}}

<div class="modal fade draggable" id="add_earnest_check_modal" tabindex="-1" role="dialog" aria-labelledby="add_earnest_check_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="add_earnest_check_modal_title">Add Check</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">

                        <form id="add_earnest_check_form" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Upload</div>
                                </div>
                                <div class="col-12">
                                    <div><input type="file" accept="application/pdf" class="custom-form-element form-input-file required" name="add_earnest_check_upload" id="add_earnest_check_upload" data-label="Click to search or Drag and Drop files here"></div>
                                </div>
                                <div class="col-12">
                                    <div class="add-earnest-check-preview-div"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Check Details</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 check-in">
                                    <input type="text" class="custom-form-element form-input required" name="add_earnest_check_name" id="add_earnest_check_name" data-label="Name On Check">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 check-out">
                                    <input type="text" class="custom-form-element form-input required" name="add_earnest_check_payable_to" id="add_earnest_check_payable_to" data-label="Payable To">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input datepicker required" name="add_earnest_check_date" id="add_earnest_check_date" data-label="Date On Check">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input numbers-only required" name="add_earnest_check_number" id="add_earnest_check_number" data-label="Check Number">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="add_earnest_check_amount" id="add_earnest_check_amount" data-label="Check Amount">
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4 check-in">
                                    <input type="text" class="custom-form-element form-input datepicker" name="add_earnest_check_date_deposited" id="add_earnest_check_date_deposited" data-label="Date Deposited">
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4 check-out">
                                    <input type="text" class="custom-form-element form-input datepicker" name="add_earnest_check_date_sent" id="add_earnest_check_date_sent" data-label="Date Sent">
                                </div>

                                <div class="col-12 col-lg-9 check-out">
                                    <textarea class="custom-form-element form-textarea" name="add_earnest_check_mail_to_address" id="add_earnest_check_mail_to_address" data-label="Mail To Address"></textarea>
                                </div>

                            </div>

                            <input type="hidden" id="add_earnest_check_type" name="add_earnest_check_type">

                        </form>

                        <div class="d-flex justify-content-around">
                            <button type="button" class="btn btn-primary" id="save_add_earnest_check_button"><i class="fad fa-save mr-2"></i> Save Check</button>
                        </div>

                    </div>


                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade draggable" id="edit_earnest_check_modal" tabindex="-1" role="dialog" aria-labelledby="edit_earnest_check_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="edit_earnest_check_modal_title">Add Check</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">

                        <form id="edit_earnest_check_form" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Check Image</div>
                                </div>
                                <div class="col-12">
                                    <div class="edit-earnest-check-preview-div"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="h5 text-orange">Check Details</div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 edit-check-in">
                                    <input type="text" class="custom-form-element form-input required" name="edit_earnest_check_name" id="edit_earnest_check_name" data-label="Name On Check">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 edit-check-out">
                                    <input type="text" class="custom-form-element form-input required" name="edit_earnest_payable_to" id="edit_earnest_payable_to" data-label="Payable To">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input datepicker required" name="edit_earnest_check_date" id="edit_earnest_check_date" data-label="Date On Check">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input numbers-only required" name="edit_earnest_check_number" id="edit_earnest_check_number" data-label="Check Number">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" name="edit_earnest_check_amount" id="edit_earnest_check_amount" data-label="Check Amount">
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4 edit-check-in">
                                    <input type="text" class="custom-form-element form-input datepicker" name="edit_earnest_date_deposited" id="edit_earnest_date_deposited" data-label="Date Deposited">
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4 edit-check-out">
                                    <input type="text" class="custom-form-element form-input datepicker" name="edit_earnest_date_sent" id="edit_earnest_date_sent" data-label="Date Sent">
                                </div>

                                <div class="col-12 col-lg-4 edit-check-out">
                                    <textarea class="custom-form-element form-textarea" name="edit_earnest_mail_to_address" id="edit_earnest_mail_to_address" data-label="Mail To Address"></textarea>
                                </div>

                            </div>

                            <input type="hidden" id="edit_earnest_check_id" name="edit_earnest_check_id">
                            <input type="hidden" id="edit_earnest_check_type" name="edit_earnest_check_type">

                        </form>

                        <div class="d-flex justify-content-around">
                            <button type="button" class="btn btn-primary" id="save_edit_earnest_check_button"><i class="fad fa-save mr-2"></i> Save Check Details</button>
                        </div>

                    </div>


                </div>

            </div>

        </div>
    </div>
</div>

{{-- accept/cancel/release listings and contracts --}}
<div class="modal fade draggable" id="accept_contract_modal" tabindex="-1" role="dialog" aria-labelledby="accept_contract_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="accept_contract_modal_title">Accept {{ $for_sale ? 'Contract' : 'Lease' }}</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="h5 text-primary pb-3 border-bottom">Enter the required details <a href="javascript: void(0)" role="button" data-toggle="popover" data-html="true" data-trigger="focus" title="Required Details" data-content="This information will be used to autopopulate your forms"><i class="fad fa-question-circle ml-2"></i></a></div>

                <form id="accept_contract_form" autocomplete="off">

                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-orange">{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent Details</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="text-primary mr-2">
                                    Who is representing the {{ $for_sale ? 'Buyer' : 'Renter' }}(s)?
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="accept_contract_BuyerRepresentedBy" data-label="Select One">
                                    <option value=""></option>
                                    <option value="other_agent">Agent From Other Company</option>
                                    <option value="our_agent">Agent From Taylor or Anne Arundel Properties</option>
                                    <option value="none">Not Represented</option>
                                    @if($property -> StateOrProvince != 'MD')
                                        <option value="agent">You Represent Both {{ $for_sale ? 'Seller' : 'Owner' }} and {{ $for_sale ? 'Buyer' : 'Renter' }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="our-agent-div">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <select class="custom-form-element form-select" id="accept_contract_our_agent" data-label="Select Agent">
                                        <option value=""></option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent -> id }}" data-id="{{ $agent -> id }}" data-first="{{ $agent -> first_name }}" data-last="{{ $agent -> last_name }}" data-email="{{ $agent -> email }}" data-phone="{{ $agent -> cell_phone }}" data-company="{{ $agent -> company }}">{{ $agent -> first_name . ' ' . $agent -> last_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="buyer-agent-details">

                            <div class="row bright-search-row">
                                <div class="col-12">
                                    <a class="btn btn-primary my-3" data-toggle="collapse" href="#agent_search_div" role="button" aria-expanded="false" aria-controls="agent_search_div">
                                        <i class="fal fa-search mr-2"></i> Search Agents in Bright MLS
                                    </a>
                                    <div class="collapse border" id="agent_search_div">
                                        <div class="p-2 mb-4">
                                            <div class="mb-4">Type the Agent's Name, Email or BrightMLS ID</div>
                                            <input type="text" class="custom-form-element form-input" id="agent_search" data-label="Enter Agent's Name, Email or ID" autocomplete="agentsearch">
                                            <div class="search-results-container">
                                                <div class="list-group search-results bg-white p-2 border shadow w-100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input agent-details agent-details-required required" id="accept_contract_buyer_agent_first" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent First Name" data-agent-detail="{{ $agent_details -> first_name }}">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input agent-details agent-details-required required" id="accept_contract_buyer_agent_last" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent Last Name" data-agent-detail="{{ $agent_details -> last_name }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input agent-details agent-details-required required" id="accept_contract_buyer_agent_company" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent Company" data-agent-detail="{{ $agent_details -> company }}">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input agent-details" id="accept_contract_buyer_agent_mls_id" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent BrightMLS ID">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input phone agent-details" id="accept_contract_buyer_agent_phone" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent Phone" data-agent-detail="{{ $agent_details -> cell_phone }}">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="custom-form-element form-input agent-details" id="accept_contract_buyer_agent_email" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }}'s Agent Email" data-agent-detail="{{ $agent_details -> email }}">
                                </div>
                                <input type="hidden" id="accept_contract_buyer_agent_street" class="agent-details" data-agent-detail="175 Admiral Cochrane Dr., Suite 111">
                                <input type="hidden" id="accept_contract_buyer_agent_city" class="agent-details" data-agent-detail="Annapolis">
                                <input type="hidden" id="accept_contract_buyer_agent_state" class="agent-details" data-agent-detail="MD">
                                <input type="hidden" id="accept_contract_buyer_agent_zip" class="agent-details" data-agent-detail="21401">
                                <input type="hidden" id="accept_contract_OtherAgent_ID" class="agent-details">
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-orange">{{ $for_sale ? 'Buyer' : 'Renter' }} Details</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_one_first" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }} One First Name">
                            </div>
                            <div class="col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input required" id="accept_contract_buyer_one_last" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }} One Last Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input" id="accept_contract_buyer_two_first" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }} Two First Name">
                            </div>
                            <div class="col-12 col-sm-6">
                                <input type="text" class="custom-form-element form-input" id="accept_contract_buyer_two_last" data-label="{{ $for_sale ? 'Buyer' : 'Renter' }} Two Last Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>
                        </div>

                        @if($for_sale)
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange">Title and Earnest Details</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-start flex-wrap align-items-center">
                                        <div class="text-primary mr-2">
                                            Are the Buyer's using Heritage Title?
                                        </div>
                                        <div class="mr-2 using-heritage">
                                            <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="accept_contract_using_heritage" data-label="Using Heritage">
                                                <option value=""></option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="not-using-heritage">
                                            <input type="text" class="custom-form-element form-input" id="accept_contract_title_company" data-label="Title Company">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" id="accept_contract_earnest_amount" data-label="Earnest Deposit Amount">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="accept_contract_earnest_held_by" data-label="Earnest Deposit Held By">
                                        <option value=""></option>
                                        <option value="us">Taylor/Anne Arundel Properties</option>
                                        <option value="other_company">Other Real Estate Company</option>
                                        <option value="title">Title Company/Attorney</option>
                                        <option value="heritage_title">Heritage Title</option>
                                        <option value="builder">Builder</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="h5 text-orange">{{ $for_sale ? 'Contract' : 'Lease' }} Details</div>
                            </div>
                        </div>

                        @if($for_sale)
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input datepicker required" id="accept_contract_contract_date" data-label="Contract Date">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input datepicker required" id="accept_contract_close_date" data-label="Settle Date">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" id="accept_contract_contract_price" data-label="Sales Price">
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input datepicker required" id="accept_contract_close_date" data-label="Lease Date">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" id="accept_contract_lease_amount" data-label="Lease Price">
                                </div>
                            </div>
                        @endif
                    </div>

                </form>

            </div>
            <div class="modal-footer d-flex justify-content-around pb-3">
                <a class="btn btn-primary btn-lg p-3" id="save_accept_contract_button"><i class="fad fa-save mr-2"></i> Save Contract Details</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="cancel_listing_modal" tabindex="-1" role="dialog" aria-labelledby="cancel_listing_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="cancel_listing_modal_title">cancel Listing</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">

                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Do Not Cancel</a>
                <a class="btn btn-primary" id="save_cancel_listing_button"><i class="fal fa-check mr-2"></i> Submit Cancellation</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="confirm_undo_cancel_modal" tabindex="-1" role="dialog" aria-labelledby="undo_cancel_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="undo_cancel_title">Undo Release/Cancellation</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            This will {{ $for_sale ? 'REJECT the Release (if submitted) and' : '' }} reactivate the {{ $for_sale ? 'Sales Contract' : 'Lease Agreement' }}.<br>
                            Are you sure you want to UNDO this Release/Cancellation?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <a class="btn btn-primary modal-confirm-button" id="undo_cancel_button"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="cancel_contract_modal" tabindex="-1" role="dialog" aria-labelledby="cancel_contract_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="cancel_contract_modal_title">Cancel {{ $for_sale ? 'Contract' : 'Lease' }}</h4>
                <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2"></i>
                </button>
            </div>
            <div class="modal-body pt-3">

                <div class="list-group cancel-alerts">

                    {{-- Contracts --}}
                    <div class="list-group-item cancel-contract docs-submitted d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-info-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            Your cancellation request will submitted to the office for review.
                        </div>
                    </div>
                    <div class="list-group-item cancel-contract docs-not-submitted d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-info-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            Since we have not reviewed and approved a Sales Contract for this property the Contract will be instantly canceled.
                        </div>
                    </div>
                    <div class="list-group-item cancel-contract has-listing docs-not-submitted d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-info-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            Your listing will remain active and you will be able to accept a new Sales Contract immediately.
                        </div>
                    </div>

                    {{-- Leases --}}
                    <div class="list-group-item cancel-lease docs-not-submitted d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-info-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            Since we have not reviewed and approved a Lease Agreement for this property the Lease Agreement will be instantly canceled.
                        </div>
                    </div>

                    <div class="list-group-item cancel-lease has-listing docs-not-submitted d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-info-circle text-primary fa-2x"></i>
                        </div>
                        <div>
                            Your listing will remain active and you will be able to accept a new Lease Agreement immediately.
                        </div>
                    </div>
                    <div class="list-group-item expired-listing d-flex justify-content-start align-items-center">
                        <div class="pr-3">
                            <i class="fad fa-exclamation-circle text-danger fa-2x"></i>
                        </div>
                        <div>
                            Your Listing Agreement is past its expiration date, please submit an extension or Cancel it.
                        </div>
                    </div>

                </div>


            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Do Not Cancel</a>
                <a class="btn btn-primary" id="save_cancel_contract_button"><i class="fal fa-check mr-2"></i> Submit Cancellation</a>
            </div>
        </div>
    </div>
</div>

{{-- required fields --}}
<div class="modal fade draggable" id="required_fields_modal" tabindex="-1" role="dialog" aria-labelledby="required_fields_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="required_fields_form">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="required_fields_modal_title">Add Required Fields</h4>
                    <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="h5 text-orange mt-2 mb-3">Please enter the following required details before submitting any documents.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    Are the Buyer's using Heritage Title?
                                    <br>
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="required_fields_using_heritage" name="required_fields_using_heritage" data-label="Using Heritage">
                                        <option value=""></option>
                                        <option value="yes" @if($property -> UsingHeritage == "yes") selected @endif>Yes</option>
                                        <option value="no" @if($property -> UsingHeritage == "no") selected @endif>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="not-using-heritage">
                                        <input type="text" class="custom-form-element form-input required" id="required_fields_title_company" name="required_fields_title_company" value="{{ $property -> TitleCompany }}" data-label="Title Company">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input money-decimal numbers-only required" id="required_fields_earnest_amount" name="required_fields_earnest_amount" value="{{ $property -> EarnestAmount > 0 ? $property -> EarnestAmount : '' }}" data-label="Earnest Deposit Amount">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel required" id="required_fields_earnest_held_by" name="required_fields_earnest_held_by" data-label="Earnest Deposit Held By">
                                        <option value=""></option>
                                        <option value="us" @if($property -> TitleCompany == "us") selected @endif>Taylor/Anne Arundel Properties</option>
                                        <option value="other_company" @if($property -> TitleCompany == "other_company") selected @endif>Other Real Estate Company</option>
                                        <option value="title" @if($property -> TitleCompany == "title") selected @endif>Title Company/Attorney</option>
                                        <option value="heritage_title" @if($property -> TitleCompany == "heritage_title") selected @endif>Heritage Title</option>
                                        <option value="builder" @if($property -> TitleCompany == "builder") selected @endif>Builder</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-primary" id="save_required_fields_button"><i class="fad fa-save mr-2"></i> Save Required Details</a>
                </div>
            </form>
        </div>
    </div>
</div>







@endsection
