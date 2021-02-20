<?php
$common_name = $field -> field_name_type == 'common' ? $field -> field_name_display : '';
$custom_name = $field -> field_name_type == 'custom' ? $field -> field_name_display : '';

$handles = '
    <div class="ui-resizable-handle ui-resizable-e focused field-div-options"></div>
    <div class="ui-resizable-handle ui-resizable-w focused field-div-options"></div>
';

$position = $field -> left_perc > 50 ? 'right' : '';

$field_div_properties = '
    <div class="field-div-properties bg-white border shadow pt-0 pb-2 px-2 '.$position.'">
        <div class="field-div-properties-html"></div>
    </div>
';
$field_class = '';
$field_data = '';
$field_name_display = $common_name.$custom_name;
$group_bg_style = '';

$hex = substr((strrev(substr($field -> group_id, -6))) / 1.3455, -6);
$find = ['/1/', '/4/', '/6/', '/9/'];
$replace = ['a', 'b', 'f', 'd'];
$hex = preg_replace($find, $replace, $hex);
$r = hexdec(substr($hex,0,2));
$g = hexdec(substr($hex,2,2));
$b = hexdec(substr($hex,4,2));
$rgba = 'rgba('.$r.', '.$g.', '.$b.', 0.5)';

if ($field -> field_category == 'textline' || $field -> field_category == 'number') {
    $field_class = 'textline-div standard';
    $field_data = '<div class="textline-html"></div>';
    $group_bg_style = '';
} else if ($field -> field_category == 'radio') {
    $handles = '';
    $field_class = 'radio-div standard';
    $field_data = '<div class="radio-html"></div>';
    $field_name_display = '';
    $field_div_properties = '';
    $group_bg_style = 'background: '.$rgba.'; border-radius: 50%; border: 1px solid #'.$hex.';';
} else if ($field -> field_category == 'checkbox') {
    $handles = '';
    $field_class = 'checkbox-div standard';
    $field_data = '<div class="checkbox-html"></div>';
    $field_div_properties = '';
    $field_name_display = '';
} else if ($field -> field_category == 'date') {
    $field_class = 'textline-div standard';
    $field_data = '<div class="textline-html"></div>';
}

?>

<div class="field-div-container" id="field_container_{{ $field -> field_id }}" style="position: absolute; top: {{ $field -> top_perc }}%; left: {{ $field -> left_perc }}%; height: {{ $field -> height_perc }}%; width: {{ $field -> width_perc }}%; {{ $group_bg_style }}">

    <div class="field-div {{ $field_class }} group_{{ $field -> group_id }}"
        id="field_{{ $field -> field_id }}"
        data-field-id="{{ $field -> field_id }}"
        data-group-id="{{ $field -> group_id }}"
        data-field-category="{{ $field -> field_category }}"
        data-page="{{ $field -> page }}"
        data-xp="{{ $field -> left_perc }}"
        data-yp="{{ $field -> top_perc }}"
        data-hp="{{ $field -> height_perc }}"
        data-wp="{{ $field -> width_perc }}"
        data-commonname="{{ $common_name }}"
        data-customname="{{ $custom_name }}">

        <div class="field-name-display-div">{{ $field_name_display }}</div>

    </div>

    <div class="field-div-options">
        <a type="button" href="javascript: void(0)" class="btn btn-danger mx-0 remove-field {{ $position }}"><i class="fal fa-ban fa-lg mr-1"></i> Delete</a>
        <div class="d-flex justify-content-start field-div-controls {{ $position }}">
            <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 field-handle"><i class="fal fa-arrows fa-lg"></i></a>
            <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="up"><i class="fal fa-arrow-up fa-lg"></i></a>
            <a type="button" href="javascript: void(0)" class="btn btn-primary ml-0 mr-1 mini-slider-option" data-direction="down"><i class="fal fa-arrow-down fa-lg"></i></a>
            @if($field -> field_category != 'date')
                <a type="button" href="javascript: void(0)" class="btn btn-primary mx-0 add-field-button"><i class="fal fa-plus fa-lg"></i></a>
            @endif
        </div>
        {!! $field_div_properties !!}
    </div>
    {!! $handles !!}
    {!! $field_data !!}
</div>
