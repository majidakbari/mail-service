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
