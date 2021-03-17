@extends('layouts.main')
@section('title', 'Employees')

@section('content')

<div class="container page-container page-page-class">

    <div class="row">

        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center">
                <div class="h2 text-orange my-4">Employees</div>
                <div>
                    <button class="btn btn-primary btn-lg font-12" id="add_employee_button"><i class="fal fa-plus mr-2"></i> Add Employee</button>
                </div>
            </div>

            <ul class="nav nav-tabs" id="employee_tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link employee-nav-link active" id="in_house_tab" data-toggle="tab" href="#in_house_div" role="tab" aria-controls="in_house_div" aria-selected="true" data-type="in_house">In House</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link employee-nav-link" id="transaction_coordinators_tab" data-toggle="tab" href="#transaction_coordinators_div" role="tab" aria-controls="transaction_coordinators_div" aria-selected="false" data-type="transaction_coordinators">Transaction Coordinators</a>
                </li>
            </ul>

            <div class="tab-content pt-2 pb-3 px-4" id="employee_tabs_content">

                <div class="wpx-125 mb-3 ml-2">
                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" id="show_active" data-label="Showing">
                        <option value="yes">Active</option>
                        <option value="no">Not Active</option>
                    </select>
                </div>

                <div class="tab-pane fade show active" id="in_house_div" role="tabpanel" aria-labelledby="in_house_tab"></div>

                <div class="tab-pane fade" id="transaction_coordinators_div" role="tabpanel" aria-labelledby="transaction_coordinators_tab"></div>

            </div>


        </div>

    </div>

</div>

<div class="modal fade draggable" id="edit_employee_modal" tabindex="-1" role="dialog" aria-labelledby="edit_employee_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-1000" role="document">
        <div class="modal-content">
            <div class="modal-header draggable-handle">
                <h4 class="modal-title" id="edit_employee_modal_title"></h4>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-12 col-lg-6">

                        <form id="edit_employee_form">

                            <div class="row">

                                <div class="col-12 col-sm-3 edit-col">
                                    <select class="custom-form-element form-select form-select-no-search" id="active" name="active" data-label="Active">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <select class="custom-form-element form-select form-select-no-cancel form-select-no-search required" id="emp_type" name="emp_type" data-label="Employee Type">
                                        <option value=""></option>
                                        <option value="admin" data-type="in_house">Admin</option>
                                        <option value="mortgage" data-type="in_house">Mortgage</option>
                                        <option value="title" data-type="in_house">Title</option>
                                        <option value="transaction_coordinators" data-type="transaction_coordinators">Transaction Coordinator</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <select class="custom-form-element form-select form-select-no-search required" id="emp_position" name="emp_position" data-label="Position">
                                        <option value=""></option>
                                        <option value="employee" data-type="title,admin">Employee</option>
                                        <option value="manager" data-type="mortgage,title,admin">Manager</option>
                                        <option value="processor" data-type="mortgage,title">Processor</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="first_name" name="first_name" data-label="First Name">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="last_name" name="last_name" data-label="Last Name">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="email" name="email" data-label="Company Email">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input" id="email_personal" name="email_personal" data-label="Personal Email">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input phone required" id="cell_phone" name="cell_phone" data-label="Phone">
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input required" id="address_street" name="address_street" data-label="Street">
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-12 col-sm-6">
                                    <input type="text" class="custom-form-element form-input required" id="address_city" name="address_city" data-label="City">
                                </div>

                                <div class="col-6 col-sm-3">
                                    <select class="custom-form-element form-select form-select-no-cancel required" id="address_state" name="address_state" data-label="State">
                                        <option value=""></option>
                                        @foreach($states as $state)
                                        <option value="{{ $state -> state }}">{{ $state -> state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-sm-3">
                                    <input type="text" class="custom-form-element form-input required" id="address_zip" name="address_zip" data-label="Zip">
                                </div>

                            </div>

                            <input type="hidden" id="id" name="id">

                        </form>

                    </div>

                    <div class="col-12 col-lg-6">

                    </div>

                </div>
            </div>
            <div class="modal-footer d-flex justify-content-around">
                <a class="btn btn-primary" id="save_edit_employee_button" data-dismiss"modal"><i class="fad fa-save mr-2"></i> Save</a>
            </div>
        </div>
    </div>
</div>
@endsection
