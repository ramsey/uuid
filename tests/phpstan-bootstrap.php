<?php

if (!function_exists('uuid_create')) {
    /**
     * @param int $type
     * @return string
     */
    function uuid_create($type = 0)
    {
    }
}

if (!function_exists('uuid_parse')) {
    /**
     * @param string $uuid
     * @return string
     */
    function uuid_parse($uuid)
    {
    }
}

if (!defined('UUID_TYPE_TIME')) {
    define('UUID_TYPE_TIME', 1);
}

if (!defined('UUID_TYPE_RANDOM')) {
    define('UUID_TYPE_RANDOM', 4);
}
