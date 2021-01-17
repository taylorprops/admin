<div class="row">

    {{-- <div class="col-12">

        <div class="p-3">

            @foreach($common_fields_groups -> where('id', '1') as $common_fields_group_people)

                <div class="font-12 font-weight-bold text-orange border-bottom">{{ $common_fields_group_people -> group_name }}</div>

                <div class="sortable-fields">

                    @foreach($common_fields_group_people -> common_fields as $common_field_people)

                        <div class="d-flex justify-content-between align-items-center common-field-input-container border-bottom" data-field-id="{{ $common_field_people -> id }}">

                            <div class="w-5 sortable-handle"><i class="fal fa-grip-vertical fa-lg"></i></div>

                            <div class="w-90 d-flex justify-content-between align-items-center common-field-input-div">

                                <div class="w-30 field-name-display common-field-input">
                                    <div class="p-2 font-8 common-field-input-value">
                                        {{ $common_field_people -> field_name }}
                                    </div>
                                </div>
                                <div class="w-20 pl-3">
                                    <select class="custom-form-element form-select form-small form-select-no-cancel group-id" data-label="Field Group">
                                        <option value=""></option>
                                        @foreach($common_fields_groups as $common_field_group)
                                            <option value="{{ $common_field_group -> id }}" @if($common_field_group -> id == $common_field_people -> group_id) selected @endif>{{ $common_field_group -> group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-20 pl-3">
                                    <select class="custom-form-element form-select form-small form-wide form-select-no-cancel sub-group-id" data-label="Field Sub Group">
                                        <option value=""></option>
                                        @foreach($common_fields_sub_groups as $common_field_sub_group)
                                            <option value="{{ $common_field_sub_group -> id }}" data-group-id="{{ $common_field_sub_group -> group_id }}" @if($common_field_sub_group -> id == $common_field_people -> sub_group_id) selected @endif>{{ $common_field_sub_group -> sub_group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-30 pl-3">
                                    <select class="custom-form-element form-select form-small form-select-no-cancel field-name-db" data-label="Select DB Column">
                                        <option value=""></option>
                                        @foreach($db_fields as $db_field)
                                            <option value="{{ $db_field }}" @if($db_field == $common_field_people -> db_column_name) selected @endif>{{ $db_field }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="w-5">

                                <a href="javascript: void(0)" class="btn btn-sm btn-success save-edit-common-field-button" data-id="{{ $common_field_people -> id }}"><i class="fad fa-save"></i></a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endforeach

        </div>

    </div>

    <div class="col-12"> --}}

        <div class="row">


            @foreach($common_fields_groups as $common_fields_group)

                <div class="col-12">

                    <div class="p-3">

                        <div class="font-12 font-weight-bold text-orange border-bottom">{{ $common_fields_group -> group_name }}</div>

                        <div class="sortable-fields">

                            @foreach($common_fields_group -> common_fields as $common_field)

                                <div class="d-flex align-items-center common-field-input-container border-bottom" data-field-id="{{ $common_field -> id }}">

                                    <div class="w-5 sortable-handle"><i class="fal fa-grip-vertical fa-lg"></i></div>

                                    <div class="w-90 d-flex align-items-center common-field-input-div">

                                        <div class="w-20 field-name-display common-field-input">
                                            <div class="p-2 font-8 common-field-input-value">
                                                {{ $common_field -> field_name }}
                                            </div>
                                        </div>

                                        <div class="w-15 pl-3">
                                            <select class="custom-form-element form-select form-small form-select-no-cancel group-id" data-label="Field Group">
                                                <option value=""></option>
                                                @foreach($common_fields_groups as $common_field_group)
                                                    <option value="{{ $common_field_group -> id }}" @if($common_field_group -> id == $common_field -> group_id) selected @endif>{{ $common_field_group -> group_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if($common_fields_group -> group_name == 'People' || $common_fields_group -> group_name == 'Offices')
                                        <div class="w-20 pl-3">
                                            <select class="custom-form-element form-select form-small form-wide form-select-no-cancel sub-group-id" data-label="Field Sub Group">
                                                <option value=""></option>
                                                @foreach($common_fields_sub_groups as $common_field_sub_group)
                                                    <option value="{{ $common_field_sub_group -> id }}" data-group-id="{{ $common_field_sub_group -> group_id }}" @if($common_field_sub_group -> id == $common_field -> sub_group_id) selected @endif>{{ $common_field_sub_group -> sub_group_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif

                                        <div class="w-15 pl-3">
                                            <select class="custom-form-element form-select form-small form-select-no-cancel field-type" data-label="Field Type">
                                                <option value=""></option>
                                                <option value="text" @if($common_field -> field_type == 'text') selected @endif>Text</option>
                                                <option value="name" @if($common_field -> field_type == 'name') selected @endif>Name</option>
                                                <option value="address" @if($common_field -> field_type == 'address') selected @endif>Address</option>
                                                <option value="number" @if($common_field -> field_type == 'number') selected @endif>Number</option>
                                                <option value="date" @if($common_field -> field_type == 'date') selected @endif>Date</option>
                                                <option value="phone" @if($common_field -> field_type == 'phone') selected @endif>Phone</option>
                                            </select>
                                        </div>

                                        <div class="w-30 pl-3">
                                            <select class="custom-form-element form-select form-small form-select-no-cancel field-name-db" data-label="Select DB Column">
                                                <option value=""></option>
                                                @foreach($db_fields as $db_field)
                                                    <option value="{{ $db_field }}" @if($db_field == $common_field -> db_column_name) selected @endif>{{ $db_field }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    <div class="w-10">

                                        <a href="javascript: void(0)" class="btn btn-primary save-edit-common-field-button" data-id="{{ $common_field -> id }}"><i class="fad fa-save mr-2"></i> Save</a>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>
