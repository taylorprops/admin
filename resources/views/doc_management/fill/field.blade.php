<?php
$field_type = $field['field_type'];
$class = 'fillable-field-container';
if ($field_type == 'number') {
    // hide written field since automatically filled in after numeric typed in
    if ($field['number_type'] == 'written') {
        $class = 'fillable-field-container-hidden';
    }
}
$class .= ' standard ' . $field_type;

$common_name = '';
$custom_name = '';

if ($field['field_name_type'] == 'common') {
    $common_name = $field['field_name_display'];
} elseif ($field['field_name_type'] == 'custom') {
    $custom_name = $field['field_name_display'];
}

$top = $field['top_perc'];
$left = $field['left_perc'];
$height = $field['height_perc'];
$width = $field['width_perc'];

$data_div_classes = 'data-div-shrink-font';
$data_div_styles = 'font-family: Helvetica, Arial, sans-serif;';

if($field_type == 'radio' || $field_type == 'checkbox') {
    //$data_div_classes = 'd-flex justify-content-center data-div-radio-check';
    $data_div_classes = 'data-div-radio-check';
    if($field_type == 'checkbox') {
        $data_div_classes = $data_div_classes.' data-div-checkbox';
    }
    /* $height = $field['height_perc'] * 1.3;
    $width = $field['width_perc'] * 1.3;
    $top = $field['top_perc'] - ($field['height_perc'] * .3);
    $left = $field['left_perc'] - ($field['width_perc'] * .3); */
} else {
    $data_div_styles = $data_div_styles.' letter-spacing:0.09em;';
}

?>
<div class="field-div {{ $class }} group_{{ $field['group_id'] }}"
    style="position: absolute;
    top: {{ $top }}%;
    left: {{ $left }}%;
    height: {{ $height + ($height * .1) }}%;
    width: {{ $width }}%;
    {{ $data_div_styles }}"
    id="field_{{ $field['field_id'] }}"
    data-field-id="{{ $field['field_id'] }}"
    data-group-id="{{ $field['group_id'] }}"
    data-type="{{ $field_type }}"
    data-address-type="{{ $field['address_type'] }}"
    data-number-type="{{ $field['number_type'] }}"
    data-page="{{ $field['page'] }}"
    data-x="{{ $field['left'] }}"
    data-y="{{ $field['top'] }}"
    data-h="{{ $field['height'] }}"
    data-w="{{ $field['width'] }}"
    data-xp="{{ $field['left_perc'] }}"
    data-yp="{{ $field['top_perc'] }}"
    data-hp="{{ $field['height_perc'] }}"
    data-wp="{{ $field['width_perc'] }}"
    data-field-name-type="{{ $field['field_name_type'] }}"
    data-commonname="{{ $common_name }}"
    data-customname="{{ $custom_name }}">

    <div class="data-div {{ $data_div_classes }} w-100 h-100" style="{{ $data_div_styles }}"></div>

    @if($field_type == 'date')

    @php
    $input_id = $field['field_id'];
    $value = get_value($field_values, $input_id);
    @endphp
    <input type="text" class="field-datepicker fillable-field-input" id="{{ $input_id }}" value="{{ $value }}" data-default-value="{{ $value }}">

    @elseif($field_type == 'radio')

    @php
    $input_id = $field['field_id'];
    $checked = get_value_radio_checkbox($field_values, $input_id);
    @endphp

    <input type="radio" class="fillable-field-input" value="{{ $field['radio_value'] }}" id="{{ $input_id }}" {{ $checked }}>

    @elseif($field_type == 'checkbox')

    @php
    $input_id = $field['field_id'];
    $checked = get_value_radio_checkbox($field_values, $input_id);
    @endphp

    <input type="checkbox" class="fillable-field-input" value="{{ $field['checkbox_value'] }}" id="{{ $input_id }}" {{ $checked }}>

    @else

    <div class="modal fade fill-fields-div" id="fill_fields_div_modal_{{$field['field_id']}}" tabindex="-1" role="dialog" aria-labelledby="fill_fields_div_modal_{{$field['field_id']}}_title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="fill_fields_div_modal_{{$field['field_id']}}_title">{{ strtoupper($field['field_name_display']) }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <div class="container form-div">

                        @if($field_type == 'textline')

                        @php
                        $input_id = $field['field_id'];
                        $value = get_value($field_values, $input_id);
                        $textline_class = $field['textline_type'] ?? null;
                        @endphp

                        <div class="row p-3 mb-1">
                            <div class="col-12">
                                <div class="my-3">
                                    <textarea class="custom-form-element form-textarea fillable-field-input {{ $textline_class }}" id="{{ $input_id }}" data-label="{{ $field['field_name'] }}" rows="3" data-default-value="{{ $value }}">{{  $value }}</textarea>
                                </div>
                            </div>
                        </div>

                        @elseif($field_type == 'name' || $field_type == 'address')

                        <div class="row p-3 mb-1">
                            @foreach($field_inputs as $input)
                            @if($field['field_id'] == $input['field_id'])

                            @php
                            $input_id = $field['field_id'].'_'.$input['input_id'];
                            $value = get_value($field_values, $input_id);
                            $address_type = address_type($input['input_name']);
                            @endphp

                            <div class="col-12">
                                <div class=" my-3">

                                    <input type="text" class="custom-form-element form-input fillable-field-input" id="{{ $input_id }}" data-label="{{ $input['input_name'] }}" data-type="{{ $input['input_name'] }}" data-address-type="{{ $address_type }}" value="{{ $value }}" data-default-value="{{ $value }}">

                                </div>
                            </div>

                            @endif
                            @endforeach

                        </div>

                        @elseif($field_type == 'number')

                        @php
                        $input_id = $field['field_id'];
                        $value = get_value($field_values, $input_id);
                        @endphp

                        <div class="row p-3 border border-secondary mb-1">
                            <div class="col-12">

                                <div class="my-1">
                                    <input type="text" class="custom-form-element form-input numbers-only fillable-field-input" id="{{ $input_id }}" data-label="{{ $field['field_name'] }}" value="{{ $value }}">
                                </div>
                            </div>
                        </div>

                        @endif

                    </div> <!-- end container form-div -->

                </div> <!-- end modal-body -->
                <div class="modal-footer">
                    <a href="javascript: void(0);" class="btn btn-primary btn-sm shadow save-fillable-fields" data-group-id="{{ $field['group_id'] }}" data-type="{{ $field_type }}" data-field-name-type="{{ $field['field_name_type'] }}">Save</a>
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</a>
                </div>

            </div> <!-- end modal-content -->

        </div> <!-- end modal-dialog -->

    </div> <!-- end modal -->

    @endif



</div> <!-- end field-div -->
