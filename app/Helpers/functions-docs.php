<?php

function get_width_height($doc_location)
{
    exec('identify '.$doc_location, $output, $return);

    $width = '';
    $height = '';
    $pages = count($output);
    if ($output) {
        preg_match('/[0-9]+x[0-9]+/', $output[0], $match);
        $size = $match[0];
        $width = substr($size, 0, strpos($size, 'x'));
        $height = substr($size, strpos($size, 'x') + 1);
    }

    return ['width' => $width, 'height' => $height, 'pages' => $pages];
}

function get_value($values, $id)
{
    foreach ($values as $value) {
        if ($value['input_id'] == $id) {
            return $value['input_value'];
        }
    }
}

function get_value_radio_checkbox($values, $id)
{
    foreach ($values as $value) {
        if ($value['input_id'] == $id) {
            if ($value['input_value'] == 'checked') {
                return 'checked';
            } else {
                return '';
            }
        }
    }
}

function address_type($val)
{
    if (stristr($val, 'Full Address')) {
        return 'full';
    } elseif (stristr($val, 'Street')) {
        return 'street';
    } elseif (stristr($val, 'City')) {
        return 'city';
    } elseif (stristr($val, 'State')) {
        return 'state';
    } elseif (stristr($val, 'County')) {
        return 'county';
    } elseif (stristr($val, 'Zip Code')) {
        return 'zip';
    }
}
function name_type($val)
{
    if ($val == 'Seller or Landlord One Name') {
        $type = 'one';
    } elseif ($val == 'Seller or Landlord Two Name') {
        $type = 'two';
    } elseif ($val == 'Buyer or Renter One Name') {
        $type = 'one';
    } elseif ($val == 'Buyer or Renter Two Name') {
        $type = 'two';
    } else {
        $type = 'both';
    }

    return $type;
}

function bright_mls_search($ListingId)
{
    $rets_config = new \PHRETS\Configuration;
    $rets_config -> setLoginUrl(config('rets.rets.url'))
        -> setUsername(config('rets.rets.username'))
        -> setPassword(config('rets.rets.password'))
        -> setRetsVersion('RETS/1.8')
        -> setUserAgent('Bright RETS Application/1.0')
        -> setHttpAuthenticationMethod('digest')
        -> setOption('disable_follow_location', false); // or 'basic' if required
    // -> setOption('use_post_method', true)

    $rets = new \PHRETS\Session($rets_config);
    $connect = $rets -> Login();
    $resource = 'Property';
    $class = 'ALL';
    $query = '(ListingId='.$ListingId.')';
    $select_columns_bright = config('global.select_columns_bright');

    $bright_db_search = $rets -> Search(
        $resource,
        $class,
        $query,
        [
            'Count' => 0,
            'Select' => $select_columns_bright,
        ]
    );

    if (isset($bright_db_search[0])) {
        $bright_db_search = $bright_db_search[0]-> toArray();
        if (count($bright_db_search) > 0) {
            return $bright_db_search;
        }
    }

    return null;
}
