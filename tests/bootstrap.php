<?php
// @codingStandardsIgnoreFile

error_reporting(E_ALL & ~E_DEPRECATED);

use AspectMock\Kernel;

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload
require_once __DIR__ . '/phpstan-bootstrap.php';

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

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => sys_get_temp_dir(),
    'includePaths' => [__DIR__ . '/../src']
]);
