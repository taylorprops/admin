@extends('layouts.main')
@section('title', 'Common Fields')

@section('content')
<div class="container-1400 page-container page-common-fields mx-auto">
    <div class="row">
        <div class="col-12">

            <h2>Common Fields</h2>

            <div class="row">
                <div class="col-12">

                    <a class="btn btn-primary" data-toggle="collapse" href="#add_common_field_collapse" role="button" aria-expanded="false" aria-controls="add_common_field_collapse">
                        <i class="fal fa-plus mr-2"></i> Add Field
                    </a>

                    <div class="collapse" id="add_common_field_collapse">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="w-20 mr-3">
                                <input type="text" class="custom-form-element form-input" id="field_name" data-label="Field Name">
                            </div>
                            <div class="w-15 mr-2">
                                <select class="custom-form-element form-select form-select-no-cancel" id="group_id" data-label="Form Group">
                                    <option value=""></option>
                                    @foreach($common_fields_groups as $common_field_group)
                                        <option value="{{ $common_field_group -> id }}">{{ $common_field_group -> group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-20 mr-2">
                                <select class="custom-form-element form-select form-select-no-cancel" id="sub_group_id" data-label="Form Sub Group">
                                    <option value=""></option>
                                    @foreach($common_fields_sub_groups as $common_field_sub_group)
                                        <option value="{{ $common_field_sub_group -> id }}">{{ $common_field_sub_group -> sub_group_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-15 mr-2">
                                <select class="custom-form-element form-select form-select-no-cancel" id="field_type" data-label="Field Type">
                                    <option value=""></option>
                                    <option value="text">Text</option>
                                    <option value="name">Name</option>
                                    <option value="address">Address</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="phone">Phone</option>
                                </select>
                            </div>
                            <div class="w-30 mr-2">
                                <select class="custom-form-element form-select form-select-no-cancel" id="db_column_name" data-label="Select DB Column">
                                    <option value=""></option>
                                    @foreach($db_fields as $db_field)
                                        <option value="{{ $db_field }}">{{ $db_field }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <a href="javascript: void(0)" class="btn btn-success mt-2" id="save_add_common_field_button"><i class="fal fa-save mr-2"></i> Save</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="common_fields_div"></div>


        </div>
    </div>
</div>
@endsection


