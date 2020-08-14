@extends('layouts.main')
@section('title', 'Uploaded Files')
@section('content')
<div class="container page-files">
    <h2>Forms</h2>
    <div class="row">
        <div class="col-4">
            <div class="border-top border-bottom border-gray">
                <div class="list-group-container" data-simplebar data-simplebar-auto-hide="false">
                    <div class="list-group" role="tablist">
                        @foreach ($form_groups as $form_group)
                            @php
                                $form_count = $upload -> GetFormCount($form_group -> resource_id);
                            @endphp
                            <a class="list-group-item form-group-item list-group-item-action @if ($loop -> first) active @endif @if($loop -> last) last @endif"
                                id="list_{{ $form_group -> resource_id }}"
                                data-toggle="list"
                                href="#list_div_{{ $form_group -> resource_id }}"
                                role="tab"
                                data-id="{{ $form_group -> resource_id }}">
                                {{ $form_group -> resource_name }}
                                <span class="float-right badge bg-blue-med py-1 px-2">{{ $form_count['form_count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="tab-content">
                @foreach ($form_groups as $form_group)
                    <div class="list-div tab-pane fade @if ($loop -> first) show active @endif" id="list_div_{{ $form_group -> resource_id }}" role="tabpanel" aria-labelledby="list_{{ $form_group -> resource_id }}">
                        <div class="h3-responsive text-primary">{{ $form_group -> resource_name }}</div>
                        <div class="row">
                            <div class="col-7">
                                <div class="row">
                                    <div class="col-4">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-search uploads-filter-sort" data-label="Sort By">
                                            <option value="az" selected>A-Z</option>
                                            <option value="added">Recently Added</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-search uploads-filter-published" data-label="Published">
                                            <option value="all">Show All</option>
                                            <option value="published">Published</option>
                                            <option value="notpublished">Not published</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <select class="custom-form-element form-select form-select-no-search form-select-no-search uploads-filter-active" data-label="Active">
                                            <option value="all">Show All</option>
                                            <option value="active" selected>Active</option>
                                            <option value="notactive">Not Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="d-flex justify-content-between">
                                    <a href="javascript: void(0)" data-state="{{ $form_group -> resource_state }}" data-form-group-id="{{ $form_group -> resource_id }}" class="btn btn-success upload-file-button mt-3"><i class="fal fa-plus mr-2"></i> Add Form Item</a>
                                    <a href="javascript: void(0)" data-state="{{ $form_group -> resource_state }}" data-form-group-id="{{ $form_group -> resource_id }}" class="btn btn-success add-non-form-item-button mt-3"><i class="fal fa-plus mr-2"></i> Add Non-Form Item</a>
                                </div>
                            </div>
                        </div>
                        <div class="border border-gray">
                            <div class="list-group-divs pt-4" data-simplebar data-simplebar-auto-hide="false">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12 forms-data" id="list_div_{{ $form_group -> resource_id }}_files" data-form-group-id="{{ $form_group -> resource_id }}" data-state="{{ $form_group -> resource_state }}">
                                        </div>
                                    </div><!-- ./ .row -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Modals -->
    <div class="modal fade draggable" id="checklist_type_modal" tabindex="-1" role="dialog" aria-labelledby="checklist_type_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <form id="checklist_type_form">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="checklist_type_title">Select Checklist Type</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times mt-2"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <select class="custom-form-element form-select form-select-no-cancel form-select-no-search required" id="add_form_checklist_type" data-label="Select Checklist Type">
                                        <option value=""></option>
                                        <option value="listing">Listing</option>
                                        <option value="contract">Contract</option>
                                        <option value="referral">Referral</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                        <a class="btn btn-success modal-confirm-button" id="checklist_type_button"><i class="fad fa-check mr-2"></i> Continue</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="add_to_checklists_modal" tabindex="-1" role="dialog" aria-labelledby="add_to_checklists_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form id="add_to_checklists_form">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="add_to_checklists_modal_title">Add Form to Checklists <span class="text-yellow-light ml-3" id="add_to_checklists_form_name"></span></h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times mt-2"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="add_form_to_checklists_div" data-simplebar data-simplebar-auto-hide="false"> </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                        <a class="btn btn-success" id="save_add_to_checklists_button"><i class="fad fa-check mr-2"></i> Save</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="remove_form_modal" tabindex="-1" role="dialog" aria-labelledby="remove_form_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="remove_form_title">Remove Form</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        Are you sure you want to remove this form from all checklists?
                        <br><br>
                        <div class="text-danger font-weight-bold" id="remove_form_name"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success modal-confirm-button" id="confirm_remove_from_checklist_button"><i class="fad fa-check mr-2"></i> Confirm</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="replace_form_modal" tabindex="-1" role="dialog" aria-labelledby="replace_form_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="replace_form_title">Replace Form</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        Are you sure you want to replace this form in all checklists?
                        <br><br>
                        <div class="font-weight-bold">
                            <div class="text-danger">Old: <span id="replace_old_form"></span></div>
                            <div class="text-success">New: <span id="replace_new_form"></span></div>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success modal-confirm-button" id="confirm_replace_form_button"><i class="fad fa-check mr-2"></i> Confirm</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="form_manage_modal" tabindex="-1" role="dialog" aria-labelledby="form_manage_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header draggable-handle">
                    <h4 class="modal-title" id="form_manage_title">Manage Form</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times mt-2"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="form_manage_div"> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="edit_file_modal" tabindex="-1" role="dialog" aria-labelledby="edit_file_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form id="edit_file_form">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="edit_file_modal_title">Edit Form Details</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times mt-2"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <span id="edit_form_name" class="h5-responsive text-primary"></span>
                                </div>
                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input required" name="edit_file_name_display" id="edit_file_name_display" data-label="Form Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="edit_form_categories[]" id="edit_form_categories" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Form Categories" multiple>
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_categories') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="edit_form_tags" id="edit_form_tags" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search" data-label="Form Tags">
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_tags') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="edit_checklist_group_id" id="edit_checklist_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search required" data-label="Checklist Group">
                                        <option value=""></option>
                                        @foreach($checklist_groups as $checklist_group)
                                            <option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="edit_form_group_id" id="edit_form_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="From Group">
                                        <option value=""></option>
                                        @foreach($form_groups as $form_group)
                                            <option value="{{ $form_group -> resource_id }}" data-state="{{ $form_group -> resource_state }}">{{ $form_group -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="edit_state" id="edit_state" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="State">
                                        <option value=""></option>
                                        <option value="All">All</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state }}">{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <textarea name="edit_helper_text" id="edit_helper_text" class="custom-form-element form-textarea" data-label="Helper Text"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <a class="btn btn-primary" id="save_edit_file_button"><i class="fad fa-save mr-2"></i> Save Details</a>
                    </div>
                    <input type="hidden" name="edit_file_id" id="edit_file_id">
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade draggable" id="add_item_no_form_modal" tabindex="-1" role="dialog" aria-labelledby="add_item_no_form_modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="add_item_no_form_form">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="add_item_no_form_modal_title">Add Checklist Item - No Form</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times mt-2"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input required" name="no_form_file_name_display" id="no_form_file_name_display" data-label="Form Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="no_form_form_categories[]" id="no_form_form_categories" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Form Categories" multiple>
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_categories') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="no_form_form_tags" id="no_form_form_tags" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search" data-label="Form Tags">
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_tags') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="no_form_checklist_group_id" id="no_form_checklist_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search required" data-label="Checklist Group">
                                        <option value=""></option>
                                        @foreach($checklist_groups as $checklist_group)
                                            <option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="no_form_form_group_id" id="no_form_form_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Form Group">
                                        <option value=""></option>
                                        @foreach($resources as $resource)
                                            @if($resource -> resource_type == 'form_groups')
                                            <option value="{{ $resource -> resource_id }}" data-state="{{ $resource -> resource_state }}">{{ $resource -> resource_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="no_form_state" id="no_form_state" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="State">
                                        <option value=""></option>
                                        <option value="All">All</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state }}">{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <textarea name="no_form_helper_text" id="no_form_helper_text" class="custom-form-element form-textarea" data-label="Helper Text"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-around">
                        <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                        <a class="btn btn-success" id="save_add_item_no_form_button"><i class="fad fa-check mr-2"></i> Save</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade draggable" id="add_upload_modal" tabindex="-1" role="dialog" aria-labelledby="add_upload_modal_title" aria-hidden="true">
        <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form id="upload_file_form" enctype="multipart/form-data">
                    <div class="modal-header draggable-handle">
                        <h4 class="modal-title" id="add_upload_modal_title">Add Form</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <i class="fal fa-times mt-2"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <input type="file" class="custom-form-element form-input-file  required" name="file_upload" id="file_upload" data-label="Select File">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="custom-form-element form-input required" name="file_name_display" id="file_name_display" data-label="Form Name">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="form_categories[]" id="form_categories" class="custom-form-element form-select form-select-no-search form-select-no-cancel form-select-no-search required" data-label="Form Categories" multiple>
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_categories') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="form_tags" id="form_tags" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search" data-label="Form Tags">
                                        <option value=""></option>
                                        @foreach($resources -> where('resource_type', 'form_tags') as $resource)
                                            <option value="{{ $resource -> resource_id }}">{{ $resource -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="checklist_group_id" id="checklist_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search form-select-no-search required" data-label="Checklist Group">
                                        <option value=""></option>
                                        @foreach($checklist_groups as $checklist_group)
                                            <option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="form_group_id" id="form_group_id" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="Form Group">
                                        <option value=""></option>
                                        @foreach($resources as $resource)
                                            @if($resource -> resource_type == 'form_groups')
                                            <option value="{{ $resource -> resource_id }}" data-state="{{ $resource -> resource_state }}">{{ $resource -> resource_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <select name="state" id="state" class="custom-form-element form-select form-select-no-cancel form-select-no-search required" data-label="State">
                                        <option value=""></option>
                                        <option value="All">All</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <textarea name="helper_text" id="helper_text" class="custom-form-element form-textarea" data-label="Helper Text"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <a class="btn btn-primary" id="upload_file_button"><i class="fad fa-upload mr-2"></i> Upload Form</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade modal-confirm" id="confirm_publish_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_publish_modal_title"
        aria-hidden="true">
        <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="confirm_publish_modal_title">Delete Form</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times fa-2x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to publish this form?
                    <div class="alert alert-danger"><i class="fad fa-exclamation-triangle mr-2 text-danger"></i> Once published you can no longer add or edit fields</div>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success modal-confirm-button" id="confirm_publish"><i class="fad fa-check mr-2"></i> Confirm</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-confirm" id="confirm_delete_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_delete_modal_title"
        aria-hidden="true">
        <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="confirm_delete_modal_title">Delete Form</h3>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i class="fal fa-times fa-2x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to permanently delete this form?
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <a class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times mr-2"></i> Cancel</a>
                    <a class="btn btn-success modal-confirm-button" id="confirm_delete"><i class="fad fa-check mr-2"></i> Confirm</a>
                </div>
            </div>
        </div>
    </div>
</div><!-- ./ .container -->
@endsection
