@extends('layouts.main')
@section('title', 'Commission Breakdown')

@section('content')

<div class="container page-container page-transaction-details">

    <h2>Commission Breakdown</h2>
    <div class="row">
        <div class="col-12">
            <div id="commission_other_div"></div>
        </div>
    </div>

    <input type="hidden" id="Commission_Other_ID" name="Commission_Other_ID" value="{{ $Commission_ID }}">
</div>

@include('doc_management/commission/commission_and_commission_other_modal_shared');

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

{{-- <div class="modal fade draggable" id="add_check_out_modal" tabindex="-1" role="dialog" aria-labelledby="add_check_out_modal_title" aria-hidden="true">
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
                <a class="btn btn-danger" data-dismiss="modal"><i class="fal fa-times mr-2"></i> Cancel</a>
                <button type="button" class="btn btn-success" id="save_add_check_out_button"><i class="fal fa-check mr-2"></i> Save</button>
            </div>
        </div>
    </div>
</div> --}}

@endsection
