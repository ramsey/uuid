<?php
// @codingStandardsIgnoreFile

include __DIR__ . '/../vendor/autoload.php'; // composer autoload

if (!function_exists('uuid_create')) {
    // Create stub of method so AspectMock can mock this function
    // if it doesn't exist in PHP.
    function uuid_create()
    {
    }
}

if (!function_exists('uuid_parse')) {
    // Create stub of method so AspectMock can mock this function
    // if it doesn't exist in PHP.
    function uuid_parse()
    {
    }
}

if (!defined('UUID_TYPE_TIME')) {
    define('UUID_TYPE_TIME', 1);
}

if (!defined('UUID_TYPE_RANDOM')) {
    define('UUID_TYPE_RANDOM', 4);
}

$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [__DIR__ . '/../src']
]);
