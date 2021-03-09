<?php

if ($field['field_type'] == 'number') {
    // hide written field since automatically filled in after numeric typed in
    if ($field['number_type'] == 'written') {
        $class = 'fillable-field-container-hidden';
    }
} elseif ($field['field_type'] == 'textline' || $field['field_type'] == 'address' || $field['field_type'] == 'name') {
    $class = 'standard textline';
} elseif ($field['field_type'] == 'radio') {
    $class = 'standard';
} elseif ($field['field_type'] == 'checkbox') {
    $class = 'standard';
} elseif ($field['field_type'] == 'date') {
    $class = 'fillable-field-container standard';
}
$class .= ' '.$field['field_type'];

$common_name = '';
$custom_name = '';
if ($field['field_name_type'] == 'common') {
    $common_name = $field['field_name_display'];
} elseif ($field['field_name_type'] == 'custom') {
    $custom_name = $field['field_name_display'];
}
