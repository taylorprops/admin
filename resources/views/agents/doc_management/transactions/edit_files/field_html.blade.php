{{-- [$user_field, $c, $field_values, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $Agent_ID, $common_fields, $fields_user_inputs] --}}

<?php
$field_id = $user_field -> create_field_id;
$group_id = $user_field -> group_id;

$top_perc = $user_field -> top_perc;
$left_perc = $user_field -> left_perc;
$height_perc = $user_field -> height_perc;
$width_perc = $user_field -> width_perc;

$field_name = $user_field -> field_name;
$field_type = $user_field -> field_type;
$field_category = $user_field -> field_category;
$field_inputs = $user_field -> field_inputs;
$field_created_by = $user_field -> field_created_by;
$inline_editor_class = $field_inputs == 'yes' || $field_type == 'date' ? '' : 'inline-editor';
if($field_category == 'number' || $field_category == 'checkbox' || $field_category == 'radio' || $field_category == 'strikeout' || $field_category == 'highlight') {
    $inline_editor_class = '';
}
if($field_created_by == 'user') {

    $data_div_classes = $field_type;
    $data_div_styles = '';
    $field_div_class = '';
    $inline = '';
    $field_html = '';
    $handles = '
    <div class="field-handle ui-resizable-handle ui-resizable-e"></div>
    <div class="field-handle ui-resizable-handle ui-resizable-w"></div>
    ';

    if($field_category == 'user_text') {

        $field_div_class = 'user-field-div textline-div standard';
        $data_div_classes .= 'inline w-100 h-100';
        $data_div_styles = 'font-family: Helvetica, Arial;';
        /* $field_html = '
            <div class="data-div textline-html inline-editor"></div>
            <input type="hidden" class="field-input user-field-input" data-id="" data-field-id="'.$field_id.'" data-group-id="'.$group_id.'" data-field-type="'.$field_type.'">
        '; */


    } else if($field_category == 'strikeout') {

        $field_div_class = 'user-field-div strikeout-div standard';
        $data_div_classes .= '-html w-100';
        //$field_html = '<div class="data-div strikeout-html"></div>';

    } else if($field_category == 'highlight') {

        $field_div_class = 'user-field-div highlight-div standard';
        $data_div_classes .= '-html w-100 h-100';
        $handles = '
            <div class="field-handle ui-resizable-handle ui-resizable-nw"></div>
            <div class="field-handle ui-resizable-handle ui-resizable-ne"></div>
            <div class="field-handle ui-resizable-handle ui-resizable-se"></div>
            <div class="field-handle ui-resizable-handle ui-resizable-sw"></div>
        ';
        //$field_html = '<div class="data-div highlight-html"></div>';

    }

} else if($field_created_by == 'system') {

    $field_div_class = ' ' . $field_category;

    $data_div_classes = '';
    $data_div_styles = '';

    if($field_category == 'radio' || $field_category == 'checkbox') {
        $data_div_classes = 'data-div-radio-check data-div-radio';
        if($field_category == 'checkbox') {
            $data_div_classes = 'data-div-radio-check data-div-checkbox';
        }
    } else {
        $data_div_styles = $data_div_styles;
    }

}

?>

<div class="field-div-container animate__animated animate__fadeIn"
    style="position: absolute;
    top: {{ $top_perc }}%;
    left: {{ $left_perc }}%;
    height: {{ $height_perc }}%;
    width: {{ $width_perc }}%;">



    <div class="field-div {{ $field_div_class }} group_{{ $group_id }} @if($inline_editor_class != '') inline @endif"
        id="field_{{ $field_id }}"
        style="position: absolute;
        top: 0%;
        left: 0%;
        width: 100%;
        height: 100%;
        display: block;"
        data-field-id="{{ $field_id }}"
        data-group-id="{{ $group_id }}"
        data-type="{{ $field_type }}"
        data-field-name="{{ $field_name }}"
        data-category="{{ $field_category }}"
        data-number-type="{{ $user_field -> number_type }}"
        data-page="{{ $user_field -> page }}"
        data-xp="{{ $left_perc }}"
        data-yp="{{ $top_perc }}"
        data-hp="{{ $height_perc }}"
        data-wp="{{ $width_perc }}"
        data-field-name-type="{{ $user_field -> field_name_type }}">
    </div>

    @php
    // add values to data-div for all fields with one input and a db field
    $user_field_inputs = $user_field -> user_field_inputs;

    $value = '';
    $input = null;
    if(count($user_field_inputs) == 1) {
        $input = $user_field_inputs -> first();
        $value = $input -> input_value;
    }
    /* if($user_field -> id == 8659) {
        dd($value);
    } */

    if($field_category  == 'date') {

        if($value != '') {
            $value = date('n/j/Y', strtotime($value));
        }

    } else if($field_category  == 'radio' || $field_category  == 'checkbox') {

        $checked = $value;
        if($checked == 'checked') {
            $value = 'x';
        }

    }
    @endphp

    @if($field_category == 'date')

        <input type="text" class="field-datepicker field-input" id="field_{{ $field_id }}" data-id="{{ $input -> id }}" value="{{ $value }}" data-default-value="{{ $value }}">

    @elseif($field_category == 'number')

        <div class="inputs-container bg-white px-3 shadow @if($left_perc > 50) right @endif">

            <div class="input-div my-3">
                <input type="text" class="custom-form-element form-input field-input numbers-only" data-id="{{ $input -> id }}" data-group-id="{{ $group_id }}" data-field-type="{{ $field_type }}" data-number-type="{{ $user_field -> number_type }}" data-db-column="{{ $input -> db_column }}" data-label="{{ $input -> input_name_display }}" value="{{ $input -> input_value }}">
            </div>

            <div class="d-flex justify-content-around mb-2">
                <a href="javascript: void(0);" class="btn btn-sm btn-success close-field-button"><i class="fa fa-check-circle mr-2"></i> Finished</a>
            </div>

        </div>

    @else

        @if($field_inputs == 'yes')

            <div class="inputs-container bg-white px-3 shadow @if($left_perc > 50) right @endif">

                @foreach($user_field -> user_field_inputs as $user_field_input)

                    <div class="input-div my-3">
                        <input type="text" class="custom-form-element form-input field-input" data-id="{{ $user_field_input -> id }}" data-group-id="{{ $group_id }}" data-field-type="{{ $field_type }}" data-db-column="{{ $user_field_input -> db_column }}" data-label="{{ $user_field_input -> input_name_display }}" value="{{ $user_field_input -> input_value }}">
                    </div>

                @endforeach

                <div class="d-flex justify-content-around mb-2">
                    <a href="javascript: void(0);" class="btn btn-sm btn-success close-field-button"><i class="fa fa-check-circle mr-2"></i> Finished</a>
                </div>

            </div>

        @else

            @if($input)

                <input type="hidden" class="field-input user-field-input" data-id="{{ $input -> id }}" data-group-id="{{ $group_id }}" data-field-type="{{ $field_type }}" data-number-type="{{ $user_field -> number_type }}" data-db-column="{{ $input -> db_column }}" value="{{ $input -> input_value }}">

            @endif

            @if($field_created_by == 'user')
                <div class="field-options-holder w-100">
                    <div class="d-flex justify-content-around">
                        <div class="btn-group" role="group" aria-label="Field Options">
                            <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a>
                            <a type="button" class="btn btn-danger remove-field"><i class="fal fa-times-circle fa-lg"></i></a>
                        </div>
                    </div>
                </div>

                {{-- {!! $field_html !!} --}}
                {!! $handles !!}
            @endif

        @endif

    @endif

    <div class="data-div {{ $inline_editor_class }} {{ $data_div_classes }}" style="{{ $data_div_styles }}">{{ $value }}</div>


</div>



