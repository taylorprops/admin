@extends('layouts.main')
@section('title', 'Contacts')

@section('content')
<div class="container page-container page-contacts">

    <div class="row">

        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">
                <div class="h2 text-orange my-4">Contacts</div>
                <div d-flex justify-content-end align-items-center">
                    <button class="btn btn-primary btn-sm import-contacts-button"><i class="fa fa-download mr-2"></i> Import Contacts</button>
                    <button class="btn btn-primary add-contact-button"><i class="fal fa-plus mr-2"></i> Add Contact</button>
                </div>
            </div>

            <div class="table-responsive contacts-table-div"></div>

        </div>

    </div>

</div>

<div class="modal fade draggable" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="import_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="import_modal_title">Import Contacts</h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 p-2">
                        <div class="text-gray">
                            You can import your contacts from an excel spreadsheet using the following format.<br><br>
                            * The first line of the spreadsheet should contain the column names.<br>
                            * The columns must be in the correct order. The order is: <br><br>
                            First Name, Last Name, Company, Cell Phone, Home Phone, Street, City, State, Zip.<br><br>
                            View example <a href="storage/public_docs/excel_import/import_template.xls" target="_blank">Here</a>
                        </div>
                    </div>
                </div>
                <hr>
                <form id="import_form">
                    <div class="font-10 text-orange">Upload your excel file</div>
                    <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" class="custom-form-element form-input-file required" name="contacts_file" id="contacts_file" data-label="Click to search or Drag and Drop files here">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_import_button" data-dismiss"modal"><i class="fad fa-upload mr-2"></i> Upload Contacts</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade draggable" id="edit_contact_modal" tabindex="-1" role="dialog" aria-labelledby="edit_contact_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="edit_contact_modal_title"></h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <form id="edit_contact_form">

                    <div class="row">

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input" id="contact_first" name="contact_first" data-label="First Name">
                        </div>

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input" id="contact_last" name="contact_last" data-label="Last Name">
                        </div>

                        <div class="col-12">
                            <input type="text" class="custom-form-element form-input" id="contact_company" name="contact_company" data-label="Company">
                        </div>

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input phone" id="contact_phone_cell" name="contact_phone_cell" data-label="Cell Phone">
                        </div>

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input phone" id="contact_phone_home" name="contact_phone_home" data-label="Other Phone">
                        </div>

                        <div class="col-12">
                            <input type="email" class="custom-form-element form-input" id="contact_email" name="contact_email" data-label="Email">
                        </div>

                        <div class="col-12">
                            <input type="text" class="custom-form-element form-input" id="contact_street" name="contact_street" data-label="Street Address">
                        </div>

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input" id="contact_city" name="contact_city" data-label="City">
                        </div>

                        <div class="col-12 col-sm-6">
                            <select class="custom-form-element form-select buyer-state" id="contact_state" name="contact_state" data-label="State">
                                <option value=""></option>
                                @foreach($states as $state)
                                <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6">
                            <input type="text" class="custom-form-element form-input" id="contact_zip" name="contact_zip" data-label="Zip">
                        </div>

                    </div>


                    <input type="hidden" id="contact_id" name="contact_id">
                    <input type="hidden" name="Agent_ID" value="{{ auth() -> user() -> user_id }}">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_edit_contact_button" data-dismiss"modal"><i class="fad fa-save mr-2"></i> Save</a>
            </div>
        </div>
    </div>
</div>
@endsection
