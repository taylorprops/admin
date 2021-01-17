@php
$location = $resource_items -> getLocation($checklist -> checklist_location_id);
@endphp

<div class="row">
    <div class="col-8">
        <div class="row">
            <div class="col-12">
                <h4>Checklist Items</h4>
            </div>
        </div>
        <div class="checklist-items-selected border border-primary">

            @foreach($checklist_groups as $checklist_group)

                <ul class="list-group sortable-checklist-items">

                    <li class="list-group-header h5 py-2 pl-2 mb-0 font-weight-bold" data-form-group-id="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</li>

                    @php
                    $checklist_group_items = $checklist_items_model -> getChecklistItemsByGroup($checklist_id, $checklist_group -> resource_id);
                    @endphp

                    @if($checklist_group_items -> count() > 0)

                        @foreach($checklist_group_items as $checklist_item)

                            @php
                            $form_id = $checklist_item -> checklist_form_id ?? null;
                            $form_name = '';
                            $form_name_orig = '';
                            if($form_id) {
                                $form_name = $files -> getFormName($form_id);
                                $form_name_orig = $form_name;
                                $form_name = shorten_text($form_name, 85);
                            }
                            $form_link = 'javascript:void(0)';
                            if($checklist_item -> file_location != '') {
                                $form_link = '/'.$files -> getFormLocation($form_id);
                            }

                            @endphp

                            <li class="list-group-item checklist-item w-100 pt-1 pb-0" data-form-id="{{ $checklist_item -> checklist_form_id ?? null }}" data-form-group-id="{{ $checklist_group -> resource_id }}"">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div>
                                                <i class="fas fa-sort fa-lg mx-3 text-primary checklist-item-handle ui-sortable-handle"></i>
                                            </div>
                                            <div class="h5 text-primary" title="{{ $form_name_orig }}"><a href="{{ $form_link }}" target="_blank">{{ $form_name }}</a></div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <span class="mr-1">Required:</span>
                                            <input type="radio" class="custom-form-element form-radio checklist-item-required required" name="checklist_item_required_{{ $form_id }}" value="yes" data-label="Yes" @if( $checklist_item -> checklist_item_required == 'yes') checked @endif>
                                            <input type="radio" class="custom-form-element form-radio checklist-item-required required" name="checklist_item_required_{{ $form_id }}" value="no" data-label="No" @if( $checklist_item -> checklist_item_required == 'no') checked @endif>
                                        </div>
                                        {{-- <select class="custom-form-element form-select form-select-no-cancel form-select-no-search checklist-item-required required" data-label="Required">
                                            <option value=""></option>
                                            <option value="yes" @if( $checklist_item -> checklist_item_required == 'yes') selected @endif>Yes</option>
                                            <option value="no" @if( $checklist_item -> checklist_item_required == 'no') selected @endif>No</option>
                                        </select> --}}
                                    </div>
                                    <div class="col-1 d-flex align-items-center">
                                        <a class="btn btn-sm btn-danger delete-checklist-item-button"><i class="fad fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>

                        @endforeach

                    @endif
                </ul>
                @endforeach

        </div>
    </div>
    <div class="col-4 forms-container">

        <h4 class="mb-3">Forms</h4>

        <div>
            <select class="custom-form-element form-select form-select-no-cancel form-select-no-search select-form-group mt-3" data-label="Select Form Group">
                <option value="all" selected>All</option>
                @foreach($form_groups as $form_group)
                <option value="{{ $form_group -> resource_id }}">{{ $form_group -> resource_state }} @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <div class="d-flex justify-content-start">
                <i class="fad fa-search text-primary mt-4 mr-3 fa-2x"></i>
                <input type="text" class="custom-form-element form-input mr-5 form-search" data-label="Search">
            </div>
        </div>

        <div class="form-groups-container mt-3">

            @foreach($form_groups as $form_group)

                <ul class="list-group form-group-div" data-form-group-id="{{ $form_group -> resource_id }}">
                    <li class="list-group-header">
                        {{ $form_group -> resource_state }}
                        @if($form_group -> resource_state != $form_group -> resource_name) | {{ $form_group -> resource_name }} @endif
                    </li>

                    @php
                    $forms = $files -> formGroupFiles($form_group -> resource_id, null, null, '');
                    $forms = $forms['forms_available'];
                    @endphp

                    @foreach($forms as $form)

                        <li class="list-group-item form-name" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}">
                            <div class="d-flex justify-content-start">
                                <div class="mr-2 my-auto">
                                    <a href="javascript: void(0)" class="btn btn-primary add-to-checklist-button" data-form-id="{{ $form -> file_id }}" data-text="{{ $form -> file_name_display }}" data-form-loc="{{ $form -> file_location }}" data-checklist-group-id="{{ $form -> checklist_group_id }}">Add</a>
                                </div>
                                <div title="{{ $form -> file_name_display }}">
                                    {{ shorten_text($form -> file_name_display, 65) }}
                                    <br>
                                    @php $categories = explode(',', $form -> form_categories); @endphp
                                    @foreach($categories as $category)
                                        <span class="badge badge-pill text-white ml-1" style="background-color: {{ $resource_items -> GetCategoryColor($category) }}">{{ $resource_items -> getResourceName($category) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </li>

                    @endforeach

                </ul>

            @endforeach

        </div>

    </div>
</div>
<input type="hidden" id="checklist_id" value="{{ $checklist -> id }}">
<input type="hidden" id="add_item_checklist_location_id" value="{{ $checklist -> checklist_location_id }}">
<input type="hidden" id="add_item_checklist_type" value="{{ $checklist -> checklist_type }}">


<input type="hidden" id="checklist_header_val" value="{{ $checklist -> checklist_state }} @if($checklist -> checklist_state != $location) {{ $location }} @endif - {{ ucwords($checklist -> checklist_sale_rent) }} - {{ ucwords($checklist -> checklist_type) }} - {{ $resource_items -> getResourceName($checklist -> checklist_property_type_id) }} @if($checklist -> checklist_property_sub_type_id != '') - {{ $resource_items -> getResourceName($checklist -> checklist_property_sub_type_id) }}  @endif - {{ ucwords($checklist -> checklist_represent) }}">


{{-- options for checklist group select --}}
<div class="d-none" id="checklist_groups_options">
@foreach($checklist_groups as $checklist_group)
<option value="{{ $checklist_group -> resource_id }}">{{ $checklist_group -> resource_name }}</option>
@endforeach
</div>
