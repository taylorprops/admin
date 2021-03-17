@extends('layouts.main')
@section('title', 'Resources')
@section('content')
<script>
    // this is accessed in resources.js
    var active_states = "{{ env("ACTIVE_STATES") }}";
</script>
<div class="container page-container page-resources">

    <h2>Resources</h2>
    <div class="row">
        @foreach($resources as $resource)
        <div class="col-6">
            <div class="resource-div">
                <div class="card">
                    <div class="card-header bg-primary">
                        <div class="h4 text-white mb-0">{{ $resource -> resource_type_title }}
                            <div class="float-right">
                                <a href="javascript:void(0)" class="add-resource-button" data-resource-type="{{ $resource -> resource_type }}"><i class="fal fa-plus text-white"></i></i></a>
                                <a href="javascript:void(0)" class="cancel-add-resource-button"><i class="fal fa-times text-danger"></i></i></a>
                            </div>
                        </div>
                        <div class="container add-resource-div bg-white p-3 shadow">
                            <h4 class="text-secondary mb-3">Add Resource</h4>
                            <form>
                                <div class="row">
                                    <div class="col-4 px-1">
                                        <input type="text" class="custom-form-element form-input add-resource-input" data-label="Resource Name">
                                    </div>
                                    @if($resource -> resource_state != '')
                                    <div class="col px-1">
                                        <select class="custom-form-element form-select add-resource-state form-select-no-cancel form-select-no-search required" data-label="State">
                                            <option value=""></option>
                                            <option value="All">All</option>
                                            @foreach($states as $state)
                                            <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    @if($resource -> resource_color != '')
                                    <div class="col px-1">
                                        <input type="color" class="custom-form-element form-input-color   add-resource-color colorpicker" value="#4C9BDB" data-default-value="#4C9BDB" data-label="Tag Color">
                                    </div>
                                    @endif
                                    <div class="col px-1">
                                        <a href="javascript:void(0)" class="btn btn-primary add-resource-save-button mt-3"><i class="fad fa-save mr-2"></i> Save</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="sortable list-group">
                        @foreach($resources_items as $resources_item)
                            @if($resource -> resource_type == $resources_item -> resource_type)
                            <li class="list-group-item" data-resource-id="{{ $resources_item -> resource_id }}" data-type="{{ $resources_item -> resource_type }}">
                                <div class="resource-div-details">

                                    <i class="fas fa-sort mr-2 mt-1 list-item-handle float-left"></i>

                                    @if($resources_item -> resource_color)<div class="resource-color-square mr-2 float-left list-item-handle" style="background-color: {{ $resources_item -> resource_color }}"></div> @endif

                                    <span class="edit-resource-title list-item-handle float-left">@if($resources_item -> resource_state) {{ $resources_item -> resource_state }} | @endif {{ $resources_item -> resource_name }} @if($resources_item -> resource_county_abbr) | {{ $resources_item -> resource_county_abbr }}@endif</span>

                                    <a href="javascript: void(0)" class="delete-deactivate-resource-button text-danger float-right ml-3" data-resource-id="{{ $resources_item -> resource_id }}" data-resource-name="{{ $resources_item -> resource_name }}" data-action="delete"><i class="fad fa-trash fa-lg"></i></a>

                                    <a href="javascript: void(0)" class="edit-resource-button text-primary float-right" data-resource-type="{{ $resources_item -> resource_type }}"><i class="fad fa-edit fa-lg"></i></a>

                                </div>
                                <div class="resource-div-edit container-fluid">
                                    <form>
                                        <div class="row  py-3">
                                            <div class="col-4 px-1">
                                                <input type="text" class="custom-form-element form-input edit-resource-input required" value="{{ $resources_item -> resource_name }}" data-default-value="{{ $resources_item -> resource_name }}" data-label="Resource Name">
                                            </div>
                                            @if($resources_item -> resource_state != '')
                                            <div class="col px-1">
                                                <select class="custom-form-element form-select edit-resource-state form-select-no-cancel form-select-no-search required" data-label="State" data-default-value="{{ $resources_item -> resource_state }}">
                                                    <option value=""></option>
                                                    <option value="All">All</option>
                                                    @foreach($states as $state)
                                                    <option value="{{ $state }}" @if( $resources_item -> resource_state == $state) selected @endif>{{ $state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif

                                            @if($resources_item -> resource_color != '')
                                            <div class="col-3 px-1">
                                                <input type="color" class="custom-form-element form-input-color   edit-resource-color colorpicker" value="{{ $resources_item -> resource_color }}" data-default-value="{{ $resources_item -> resource_color }}" data-label="Tag Color">
                                            </div>
                                            @endif

                                            <div class="col-1 px-1">
                                                <a href="javascript: void(0)" class="save-edit-resource-button" data-resource-id="{{ $resources_item -> resource_id }}"  data-resource-type="{{ $resources_item -> resource_type }}"><i class="fal fa-check text-success fa-2x mt-4"></i></a>
                                            </div>
                                            <div class="col-1 px-1">
                                                <a href="javascript: void(0)" class="close-edit-resource-button"><i class="fal fa-times text-danger fa-2x mt-4"></i></a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div><!-- ./ .row -->
</div><!-- ./ .container -->
<div class="modal fade modal-confirm" id="confirm_delete_deactivate_resource_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_delete_deactivate_resource_modal_title"
    aria-hidden="true">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="confirm_delete_deactivate_resource_modal_title"></h3>
                <a href="javascript: void(0)" class="text-danger font-13" data-dismiss="modal" aria-label="Close">
                    <i class="fal fa-times mt-2 fa-lg"></i>
                </a>
            </div>
            <div class="modal-body">
                <span class="confirm-delete-deactivate-resource-text"></span>
                <div class="h5 text-center text-orange font-weight-bold delete-deactivate-resource-file-name mt-3"></div>
            </div>
            <div class="modal-footer d-flex justify-content-around">

                <a class="btn btn-primary modal-confirm-button" id="confirm_delete_deactivate_resource"><i class="fal fa-check mr-2"></i> Confirm</a>
            </div>
        </div>
    </div>
</div>
@endsection

