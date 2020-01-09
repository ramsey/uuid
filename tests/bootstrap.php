<?php

/**
 * Test bootstrap
 *
 * @codingStandardsIgnoreFile
 */

error_reporting(E_ALL & ~E_DEPRECATED);

// Ensure floating-point precision is set to 14 (the default) for tests.
ini_set('precision', '14');

use AspectMock\Kernel;

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload
require_once __DIR__ . '/phpstan-bootstrap.php';

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => sys_get_temp_dir(),
    'includePaths' => [__DIR__ . '/../src']
]);
