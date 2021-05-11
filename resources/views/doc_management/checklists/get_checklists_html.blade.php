

<div class="d-flex justify-content-between align-items-center">
    <div class="h3 text-secondary mb-4 property-type-div-header">{{ ucwords($checklist_type) }} Checklists</div>
    <div>
        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel checklist-type-option" data-label="Checklist Type">
            <option value="listing">Listing</option>
            <option value="contract">Contract/Lease</option>
        </select>
    </div>
</div>

@php
$checklist = $checklists_model -> where('checklist_type', 'referral') -> first();
@endphp

<div class="referral-checklist-div">

    <div class="d-flex justify-content-start">
        <h4 class="text-primary">Referral</h4>
    </div>

    <div class="sortable-checklists">

        <div class="border-bottom border-gray checklist-items-referral" data-checklist-id="{{ $checklist -> id }}">
            <div class="row my-2">
                <div class="col-6">
                    <div class="row mt-2 checklist-items">
                        <div class="col">
                            <span class="text-primary-dark font-weight-bold ml-2">
                                Standard
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex justify-content-start align-items-center">
                        <div>
                            <span class="badge bg-blue-light text-primary-dark rounded">{{ $checklist -> checklist_count ?? 0 }}</span>
                        </div>
                        <div>
                            <a href="javascript: void(0)"
                                data-checklist-id="{{ $checklist -> id }}"
                                class="btn btn-sm btn-primary float-right add-items-button">
                                <i class="fal fa-plus mr-2"></i>
                                Add Items
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>

</div>

<div class="non-referral-checklist-div">

    @foreach($property_types as $property_type)

    <div class="property-type-div mb-4 mt-1 pb-3">

        <div class="d-flex justify-content-start">
            <h4 class="text-primary">{{ $property_type -> resource_name }}</h4>
            <a href="javascript: void(0)"
                data-location-id="{{ $checklist_location_id }}"
                data-state="{{ $checklist_state }}"
                data-property-type="{{ $property_type -> resource_id }}"
                data-form-type="add"
                class="text-success add-checklist-button mt-1 ml-5">
                <i class="fal fa-plus mr-2"></i>
                Add Checklist
            </a>
        </div>

        <div class="sortable-checklist">
            @php
            $property_type_checklists = $checklists_model -> getChecklistsByPropertyType($property_type -> resource_id, $checklist_location_id, '');
            @endphp
            @foreach($property_type_checklists as $checklist)

                @php
                $checklist_type = $checklist -> checklist_type;
                /* $in_use = $checklists_model -> isChecklistInUse($checklist -> id); */
                @endphp

                <div class="checklist-items-container border-bottom border-gray checklist-items-{{ $checklist_type }} @if($checklist_type == 'contract') hide @endif" data-checklist-id="{{ $checklist -> id }}">
                    <div class="row my-2">
                        <div class="col-6">
                            <div class="row mt-2 checklist-items">
                                <div class="col">
                                    <i class="fas fa-sort mr-2 mt-1 checklist-handle text-primary"></i>
                                    <span class="text-primary-dark font-weight-bold ml-2 checklist-handle">
                                        {{ ucwords($checklist -> checklist_sale_rent) }}
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="text-primary checklist-handle">
                                        {{ ($checklist -> checklist_represent == 'seller' ? 'Seller/Owner' : 'Buyer/Renter') }}
                                    </span>
                                </div>
                                <div class="col-5">
                                    <span class="text-orange checklist-handle">
                                        @if($checklist -> checklist_property_sub_type_id > 0)
                                        {{ $resource_items -> getResourceName($checklist -> checklist_property_sub_type_id) }}
                                        @else
                                        Standard
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <div>
                                    <span class="badge bg-primary-light text-primary-dark rounded">{{ $checklist -> checklist_count ?? 0 }}</span>
                                </div>
                                <div>
                                    <a href="javascript: void(0)"
                                        data-checklist-id="{{ $checklist -> id }}"
                                        class="btn btn-sm btn-primary float-right add-items-button">
                                        <i class="fal fa-plus mr-2"></i>
                                        Add Items
                                    </a>
                                </div>
                                <div>
                                    <a href="javascript: void(0)"
                                        data-checklist-id="{{ $checklist -> id }}"
                                        data-location-id="{{ $checklist -> checklist_location_id }}"
                                        data-state="{{ $checklist -> checklist_state }}"
                                        data-property-type="{{ $checklist -> checklist_property_type_id }}"
                                        data-sale-rent="{{ $checklist -> checklist_sale_rent }}"
                                        data-represent="{{ $checklist -> checklist_represent }}"
                                        data-property-sub-type="{{ $checklist -> checklist_property_sub_type_id }}"
                                        data-form-type="edit"
                                        class="btn btn-sm btn-primary float-right edit-checklist-button">
                                        <i class="fad fa-edit mr-2"></i>
                                        Edit
                                    </a>
                                </div>
                                <div>
                                    {{--  @if($in_use) --}}
                                    <a href="javascript: void(0)"
                                        data-checklist-id="{{ $checklist -> id }}"
                                        data-checklist-location-id="{{ $checklist -> checklist_location_id }}"
                                        data-checklist-type="{{ $checklist -> checklist_type }}"
                                        class="btn btn-sm btn-danger float-right delete-checklist-button">
                                        <i class="fad fa-trash mr-2"></i>
                                        Delete
                                    </a>
                                    {{-- @endif --}}
                                </div>
                                <div>
                                    <a href="javascript: void(0)"
                                        data-checklist-id="{{ $checklist -> id }}"
                                        data-checklist-location-id="{{ $checklist -> checklist_location_id }}"
                                        data-checklist-type="{{ $checklist -> checklist_type }}"
                                        class="btn btn-sm btn-primary float-right duplicate-checklist-button">
                                        <i class="fad fa-copy mr-2"></i>
                                        Duplicate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>

    </div> <!-- ./ .property-type-div -->

    @endforeach

</div>

<input type="hidden" id="files_count" value="{{ $checklists_count }}">


