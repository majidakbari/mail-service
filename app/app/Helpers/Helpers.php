<?php

if (!function_exists('get_class_name')) {
    function get_class_name($object)
    {
        $output = get_class($object);

        return substr($output, strrpos($output, '\\') + 1);
    }
}
