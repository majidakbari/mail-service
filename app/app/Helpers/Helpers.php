<?php

if (!function_exists('get_class_name')) {
    function get_class_name($object)
    {
        $output = get_class($object);

        if (strpos($output, '\\') != false) {
            return substr($output, strrpos($output, '\\') + 1);
        } else {
            return $output;
        }
    }
}


if (!function_exists('get_paginate_params')) {

    function get_paginate_params($perPage, $page)
    {
        if (empty($perPage) || is_array($perPage) || is_object($perPage) || intval($perPage) < 0 || intval($perPage) > 100) {
            $perPage = 10;
        }
        if (empty($page) || is_array($page) || is_object($page) || (intval($page) < 0)) {
            $page = 1;
        }

        return [(int)$perPage, (int)$page];
    }
}

