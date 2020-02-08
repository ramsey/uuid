<?php

/**
 * PHPStan bootstrap
 *
 * @codingStandardsIgnoreFile
 */

if (!function_exists('uuid_create')) {
    /**
     * @param int $type
     * @return string
     */
    function uuid_create($type = 0)
    {
        switch ($type) {
            case 1:
                return \Ramsey\Uuid\v1();
            case 4:
            default:
                return \Ramsey\Uuid\v4();
        }
    }
}

if (!function_exists('uuid_parse')) {
    /**
     * @param string $uuid
     * @return string
     */
    function uuid_parse($uuid)
    {
        return \Ramsey\Uuid\Uuid::fromString($uuid)->getBytes();
    }
}

if (!function_exists('uuid_generate_md5')) {
    /**
     * @param string $ns
     * @param string $name
     * @return string
     */
    function uuid_generate_md5($ns, $name)
    {
        return \Ramsey\Uuid\v3($ns, $name);
    }
}

if (!function_exists('uuid_generate_sha1')) {
    /**
     * @param string $ns
     * @param string $name
     * @return string
     */
    function uuid_generate_sha1($ns, $name)
    {
        return \Ramsey\Uuid\v5($ns, $name);
    }
}

if (!defined('UUID_TYPE_TIME')) {
    define('UUID_TYPE_TIME', 1);
}

if (!defined('UUID_TYPE_RANDOM')) {
    define('UUID_TYPE_RANDOM', 4);
}
