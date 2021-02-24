<?php

/**
 * Function: sanitize
 * Returns a sanitized string, typically for URLs.
 *
 * Parameters:
 *     $string - The string to sanitize.
 *     $force_lowercase - Force the string to lowercase?
 *     $anal - If set to *true*, will remove all non-alphanumeric characters.
 */
function sanitize($string, $force_lowercase = false, $anal = false) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
    return ($force_lowercase) ?
    (function_exists('mb_strtolower')) ?
    mb_strtolower($clean, 'UTF-8') :
    strtolower($clean) :
    $clean;
}

function directory($directory) {
    $results = array();
    $handler = opendir($directory);

    while ($file = readdir($handler)) {

        if ($file != '.' && $file != '..') {
            $results[] = $file;
        }

    }

    closedir($handler);
    return $results;
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) return NULL;
    return (count(scandir($dir)) == 2);
}

function shorten_text($text, $length) {

    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }

    return $text;
}

function format_phone($phone) {
    $phone = preg_replace('/[\s\(\)-]+/', '', $phone);
    return "(".substr($phone, 0, 3).") ".substr($phone, 3, 3)."-".substr($phone,6);
}

function date_mdy($date) {
    return date('n/j/Y', strtotime($date));
}

function get_mb($size) {
    return sprintf("%4.2f", $size/1048576);
}

function get_initials($string) {

    if(strlen($string) > 0) {
        $words = explode(' ', $string);
        $initials = '';

        foreach ($words as $w) {
            $initials .= $w[0];
        }

        return $initials;
    }
    return '';

}


?>
