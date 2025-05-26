<?php

/**
 * Stubs for static analysis
 *
 * @codingStandardsIgnoreFile
 */

if (!defined('UUID_TYPE_DEFAULT')) {
    define('UUID_TYPE_DEFAULT', 0);
}
if (!defined('UUID_TYPE_TIME')) {
    define('UUID_TYPE_TIME', 1);
}
if (!defined('UUID_TYPE_RANDOM')) {
    define('UUID_TYPE_RANDOM', 4);
}
if (!function_exists('uuid_create')) {
    function uuid_create(int $uuid_type=UUID_TYPE_DEFAULT): string {} // @phpstan-ignore-line
}
if (!function_exists('uuid_generate_md5')) {
    function uuid_generate_md5(string $uuid_ns, string $name): string {} // @phpstan-ignore-line
}
if (!function_exists('uuid_generate_sha1')) {
    function uuid_generate_sha1(string $uuid_ns, string $name): string {} // @phpstan-ignore-line
}
if (!function_exists('uuid_parse')) {
    function uuid_parse(string $uuid): string {} // @phpstan-ignore-line
}
