<div class="edit-properties-div">

    <div class="d-flex justify-content-between align-items-center">
        <div class="font-10 my-2 text-orange">{{ strtoupper($field_category) }}</div>
        <a type="button" class="btn btn-danger btn-sm close-field-options"><i class="fal fa-times"></i></a>
    </div>

    <div class="pt-0 pb-1 px-2 form-div">

        <div class="p-3 mt-2 bg-blue-light text-gray rounded">

            <div class="text-primary">Field Name</div>

            <div class="row">

                @if($field_category != 'radio')

                    <div class="col-12">

                        <ul class="navbar-nav-dropdown p-0">

                            <li class="nav-item dropdown dropdown-input">
                                @php $id = date('YmdHis').mt_rand(111111111111, 999999999999); @endphp
                                <input type="text" class="custom-form-element form-input dropdown-toggle common-field-name-input" href="javascript: void(0)" id="group_dropdown_{{ $id }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Shared Field Name" readonly data-default-value="{{$common_name}}" value="{{$common_name}}">


                                <ul class="dropdown-menu dropdown-parent" aria-labelledby="group_dropdown_{{ $id }}">

                                    <li><a href="javascript: void(0)" class="small text-danger clear-common-field-name">Clear <i class="fad fa-times-circle ml-1"></i></a></li>

                                    @php
                                    if($field_category == 'number') {
                                        $common_fields_groups = $common_fields_groups -> whereIn('id', ['4']);
                                    } else if($field_category == 'date') {
                                        $common_fields_groups = $common_fields_groups -> whereIn('id', ['3']);
                                    } else {
                                        $common_fields_groups = $common_fields_groups -> whereNotIn('id', ['3', '4']);
                                    }
                                    @endphp
                                    @foreach($common_fields_groups as $common_fields_group)

                                        @if($field_category != 'number' && $field_category != 'date')

                                        <li class="nav-item dropdown">
                                            <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="sub_group_dropdown_{{ $loop -> index }}" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ $common_fields_group -> group_name }}
                                            </a>
                                            <ul class="dropdown-menu @if($field_category == 'number' || $field_category == 'date') top-drop @endif" aria-labelledby="sub_group_dropdown_{{ $loop -> index }}">

                                        @endif

                                                @if(count($common_fields_group -> sub_groups) > 0)

                                                    @foreach($common_fields_group -> sub_groups as $common_fields_sub_group)
                                                        <li class="nav-item dropdown">
                                                            <a href="javascript: void(0)" class="dropdown-item dropdown-toggle" id="sub_group_dropdown_{{ $loop -> index }}" role="button" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                {{ $common_fields_sub_group -> sub_group_name }}
                                                            </a>
                                                            <ul class="dropdown-menu @if($field_category == 'number' || $field_category == 'date') top-drop @endif" aria-labelledby="sub_group_dropdown_{{ $loop -> index }}">

                                                                @foreach($common_fields_group -> common_fields -> where('group_id', $common_fields_group -> id) -> where('sub_group_id', $common_fields_sub_group -> id) as $common_field)
                                                                    <li class="nav-item">
                                                                        <a href="javascript: void(0)"
                                                                        class="nav-link dropdown-item field-name"
                                                                        data-field-id="{{ $common_field -> id }}"
                                                                        data-field-type="{{ $common_field -> field_type }}"
                                                                        data-field-sub-group-id="{{ $common_field -> sub_group_id }}"
                                                                        >{{ $common_field -> field_name }}</a>
                                                                    </li>
                                                                @endforeach

                                                            </ul>
                                                        </li>
                                                    @endforeach

                                                @else
                                                    @foreach($common_fields -> where('group_id', $common_fields_group -> id) as $common_field)
                                                        <li class="nav-item">
                                                            <a href="javascript: void(0)"
                                                            class="nav-link dropdown-item field-name"
                                                            data-field-id="{{ $common_field -> id }}"
                                                            data-field-type="{{ $common_field -> field_type }}"
                                                            data-field-sub-group-id="{{ $common_field -> sub_group_id }}"
                                                            >{{ $common_field -> field_name }}</a>
                                                        </li>
                                                    @endforeach

                                                @endif

                                            @if($field_category != 'number' && $field_category != 'date')
                                                </ul>
                                            </li>
                                            @endif
                                    @endforeach
                                </ul>

                            </li>

                        </ul>

                    </div>

                    <div class="col-12">
                        <div class="w-100 text-center font-weight-bold text-gray">OR</div>
                    </div>

                @endif

                <div class="col-12">
                    <input type="text" class="custom-form-element form-input field-data-name custom-field-name" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="{{ $label }}">

                    <div class="custom-name-wrapper">
                        <div class="custom-name-results">
                            <div class="list-group dropdown-results-div"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" class="field-data-name common-field-name" data-field-type="common" value="{{$common_name}}" data-default-value="{{$common_name}}">
                <input type="hidden" class="common-field-id" value="{{$common_field_id}}" data-default-value="{{$common_field_id}}">
                <input type="hidden" class="common-field-type" value="{{$common_field_type}}" data-default-value="{{$common_field_type}}">
                <input type="hidden" class="common-field-sub-group-id" value="{{$common_field_sub_group_id}}" data-default-value="{{$common_field_sub_group_id}}">

            </div>

        </div>

        @if($field_category == 'number')

            <div class="p-3 mt-2 bg-blue-light text-gray rounded">

                <div class="text-primary">Number Type</div>

                <div class="row">

                    <div class="col-12">

                        <div class="d-flex justify-content-around align-items-center">
                            <div>
                                <input type="radio" class="custom-form-element form-radio number-type" name="number_type_{{ $field_id }}" value="written" data-label="Written" @if($number_type == 'written') checked @endif>
                            </div>
                            <div>
                                <input type="radio" class="custom-form-element form-radio number-type" name="number_type_{{ $field_id }}" value="numeric" data-label="Numeric" @if($number_type == 'numeric') checked @endif>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        @endif

        <div class="row">

            <div class="col-12">
                <div class="d-flex justify-content-around mt-3">
                    <a type="button"
                    class="btn btn-primary save-field-properties-button"
                    data-group-id="{{ $group_id }}"
                    data-field-id="{{ $field_id }}"
                    data-field-category="{{ $field_category }}"
                    >
                        <i class="fad fa-save fa-lg mr-2"></i> Save
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="alert alert-success hide mt-3 mb-2" role="alert">
                    <div class="d-flex justify-content-around">
                        <div class="d-flex justify-content-start align-items-center">
                            <div><i class="fal fa-check fa-lg mr-2"></i></div>
                            <div>Successfully Saved!</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="alert alert-danger hide mt-3 mb-2" role="alert">
                    <div class="d-flex justify-content-around">
                        <div class="d-flex justify-content-start align-items-center">
                            <div><i class="fad fa-exclamation-circle fa-2x mr-2"></i></div>
                            <div class="error-message font-8 text-center"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>





{{-- <div class="edit-properties-div p-2" id="edit_properties_div_{{ $field_id }}">

    @php
    $cols = $field_category == 'date' || $field_category == 'radio' ? 'col-12' : 'col-12 col-sm-6';
    @endphp

    <div class="h5 text-orange mb-2 ml-3">{{ strtoupper($field_category) }}</div>

    <div class="form-div">

        <div class="container p-0">

            <div class="row no-gutters">

                <div class="{{ $cols }}">

                    <div class="p-2 mx-2 border rounded">

                        <h6 class="text-primary">Field Name</h6>

                        @if($field_category != 'checkbox' && $field_category != 'radio')

                            <select class="custom-form-element form-select field-data-name required" id="name_select_{{ $field_id }}" data-field-type="common" data-default-value="{{$common_name}}" data-label="Select Common Name (Shared)">
                                <option value="">&nbsp;</option>
                                @foreach ($common_fields as $common_field_type => $field_names)
                                    @if($field_category == $common_field_type)
                                        @foreach($field_names as $field_name)
                                            <option value="{{ $field_name[0] }}" @if($field_name[0]==$common_name) selected @endif>{{ $field_name[0] }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>

                            <div class="text-primary w-100 text-center my-0">OR</div>

                        @endif

                        <input type="text" class="custom-form-element form-input field-data-name required" id="name_input_{{ $field_id }}" data-field-type="custom" value="{{ $custom_name }}" data-default-value="{{ $custom_name }}" data-label="{{ $label }}">



                    </div>

                </div>

                @php
                $allowed_field_types = ['number', 'textline', 'address', 'name'];
                @endphp
                @if(in_array($field_category, $allowed_field_types))

                    <div class="{{ $cols }}">

                        <div class="p-2 mx-2 border rounded">

                            <h6 class="text-primary">Options</h6>

                            @if($field_category == 'number')

                                <select class="custom-form-element form-select field-data-number-type required" id="number_select_{{ $field_id }}" data-field-type="number-type" data-default-value="{{ $field_number_type }}" data-label="Number Type">
                                    <option value="">&nbsp;</option>
                                    <option value="numeric" @if($field_number_type=='numeric' ) selected @endif>Numeric - 3,000</option>
                                    <option value="written" @if($field_number_type=='written' ) selected @endif>Written - Three Thousand</option>
                                </select>

                            @elseif($field_category == 'textline')

                                <select class="custom-form-element form-select field-data-textline-type" id="textline_select_{{ $field_id }}" data-field-type="textline-type" data-default-value="{{ $field_textline_type }}" data-label="Format Type - Optional">
                                    <option value="">&nbsp;</option>
                                    <option value="number numbers-only" @if($field_textline_type=='number numbers-only' ) selected @endif>Number</option>
                                    <option value="phone numbers-only" @if($field_textline_type=='phone numbers-only' ) selected @endif>Phone Number</option>
                                </select>

                            @elseif($field_category == 'name')

                                <select class="custom-form-element form-select field-data-name-type" id="name_select_{{ $field_id }}" data-field-type="name-type" data-default-value="{{ $field_name_type }}" data-label="Name Type">
                                    <option value="">&nbsp;</option>
                                    <option value="both" @if($field_name_type=='both' ) selected @endif>All Sellers or All Buyers</option>
                                    <option value="one" @if($field_name_type=='one' ) selected @endif>Seller One or Buyer One</option>
                                    <option value="two" @if($field_name_type=='two' ) selected @endif>Seller Two or Buyer Two</option>
                                </select>

                            @elseif($field_category == 'address')

                                <select class="custom-form-element form-select field-data-address-type required" id="address_select_{{ $field_id }}" data-field-type="address-type" data-default-value="{{ $field_address_type }}" data-label="Address Type">
                                    <option value="">&nbsp;</option>
                                    <option value="full" @if($field_address_type=='full' ) selected @endif>Full Address</option>
                                    <option value="street" @if($field_address_type=='street' ) selected @endif>Street Address</option>
                                    <option value="city" @if($field_address_type=='city' ) selected @endif>City</option>
                                    <option value="state" @if($field_address_type=='state' ) selected @endif>State</option>
                                    <option value="zip" @if($field_address_type=='zip' ) selected @endif>Zip Code</option>
                                    <option value="county" @if($field_address_type=='county' ) selected @endif>County</option>
                                </select>

                            @endif

                        </div>

                    </div>

                @endif

            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-around my-2">

                        <button class="btn btn-danger" data-toggle="collapse" href="#properties_container_{{ $field_id }}" role="button" aria-expanded="false" aria-controls="properties_container_{{ $field_id }}"><i class="fal fa-ban mr-2"></i> Cancel</button>

                        @if($published == 'no')
                            <a href="javascript: void(0);" class="btn btn-success field-save-properties" data-field-id="{{ $field_id }}" data-group-id="{{ $group_id }}" data-type="{{ $field_category }}"><i class="fad fa-save mr-2"></i> Save</a>
                        @endif

                    </div>
                </div>
            </div>

            <div class="field-inputs-div">

                @foreach($field_inputs as $field_input)

                    @if($field_input -> field_id == $field_id)

                        <input type="hidden" class="field-data-input" id="input_name_{{ $field_id }}_{{ $field_input -> input_id }}" value="{{ $field_input -> input_name }}" data-default-value="{{ $field_input -> input_name }}" data-id="{{ $field_input -> input_id }}">

                    @endif

                @endforeach

            </div>

        </div>

    </div>

</div> --}}
