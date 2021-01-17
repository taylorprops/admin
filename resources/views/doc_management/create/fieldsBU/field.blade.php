<?php
$common_name = '';
$custom_name = '';

if($field -> field_name_type == 'common') {
    $common_name = $field -> field_name_display;
} else if($field -> field_name_type == 'custom') {
    $custom_name = $field -> field_name_display;
}

$label = 'Custom Name';
$show_options = true;
if($field -> field_type == 'textline' || $field -> field_type == 'address' || $field -> field_type == 'name' || $field -> field_type == 'number') {
    $field_class = 'standard textline';
    $add_items = 'yes';
    $field_html = '<div class="textline-html"></div>';
    $icon = '<i class="fas fa-horizontal-rule fa-lg"></i>';
} else if($field -> field_type == 'radio') {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'yes';
    $field_html = '<div class="radio-html"></div>';
    $icon = '<i class="fas fa-circle fa-lg"></i>';
    $label = 'Radio Button Group Name';
} else if($field -> field_type == 'checkbox') {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'yes';
    $field_html = '<div class="checkbox-html"></div>';
    $icon = '<i class="fal fa-square-full fa-lg"></i>';
    $show_options = false;
} else {
    $field_class = 'standard '.$field -> field_type;
    $add_items = 'no';
    $field_html = '';
    $icon = '';
}
?>
<div
    class="field-div {{ $field_class }}-div group_{{ $field -> group_id }}"
    style="position: absolute;
    top: {{ $field -> top_perc }}%;
    left: {{ $field -> left_perc }}%;
    height: {{ $field -> height_perc }}%;
    width: {{ $field -> width_perc }}%;"
    id="field_{{ $field -> field_id }}"
    data-field-id="{{ $field -> field_id }}"
    data-group-id="{{ $field -> group_id }}"
    data-type="{{ $field -> field_type }}"
    data-page="{{ $field -> page }}"
    data-x="{{ $field -> left }}"
    data-y="{{ $field -> top }}"
    data-h="{{ $field -> height }}"
    data-w="{{ $field -> width }}"
    data-xp="{{ $field -> left_perc }}"
    data-yp="{{ $field -> top_perc }}"
    data-hp="{{ $field -> height_perc }}"
    data-wp="{{ $field -> width_perc }}"
    data-commonname="{{ $common_name }}"
    data-customname="{{ $custom_name }}">
    <div class="field-status-div d-flex justify-content-left">
        <div class="field-name-display-div"></div>
        <div class="field-status-group-div float-right"></div>
    </div>

    <div class="field-options-holder focused">

        <div>
            <a href="javascript: void(0)" class="btn btn-sm btn-danger m-0 ml-2 close-field-options"><i class="fal fa-times fa-lg mr-2"></i> Hide</a>
        </div>
        <div class="btn-group" role="group" aria-label="Field Options">
            <a type="button" class="btn btn-primary field-handle"><i class="fal fa-arrows fa-lg"></i></a>
            <a type="button" class="btn btn-primary mini-slider-button"><i class="fal fa-arrows-v fa-lg"></i></a>
            @if($add_items == 'yes')
                <a type="button" class="btn btn-primary field-add-item" data-group-id="{{ $field -> group_id }}"><i class="fal fa-plus fa-lg"></i></a>
            @endif
            @if($show_options)
                <a type="button" class="btn btn-primary field-properties" data-group-id="{{ $field -> group_id }}" data-field-id="{{ $field -> field_id }}" data-field-type="{{ $field -> field_type }}" data-toggle="collapse" href="#properties_container_{{ $field -> field_id }}" role="button" aria-expanded="false" aria-controls="properties_container_{{ $field -> field_id }}"><i class="fad fa-info-circle fa-lg"></i></a>
            @endif
            <a type="button" class="btn btn-primary remove-field"><i class="fal fa-times-circle fa-lg"></i></a>
        </div>

        <div class="mini-slider-div">
            <ul class="mini-slider list-group list-group-flush border border-primary p-0">
                <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="up"><i class="fal fa-arrow-up text-primary"></i></a></li>
                <li class="list-group-item text-center p-0"><a href="javascript:void(0);" class="mini-slider-option w-100 h-100 d-block p-2" data-direction="down"><i class="fal fa-arrow-down text-primary"></i></a></li>
            </ul>
        </div>




    </div>


    @php
    $field_id = $field -> field_id;
    $field_type = $field -> field_type;
    $field_number_type = $field -> number_type;
    $field_textline_type = $field -> textline_type;
    $field_address_type = $field -> address_type;
    $field_name_type = $field -> name_type;
    $group_id = $field -> group_id;
    @endphp
    <div id="properties_container_{{ $field -> field_id }}" class="collapse edit-properties-container bg-white border rounded shadow @if($field_type == 'date' || $field_type == 'radio') w-400 @else w-600 @endif" data-field-id="{{ $field -> field_id }}" data-parent="#file_viewer">
        @include('/doc_management/create/fields/edit_properties_html')
    </div>


    @if($field -> field_type != 'checkbox' && $field -> field_type != 'radio')
        <div class="ui-resizable-handle ui-resizable-e focused"></div>
        <div class="ui-resizable-handle ui-resizable-w focused"></div>
    @endif

    {!! $field_html !!}

</div> <!-- end field-div -->
