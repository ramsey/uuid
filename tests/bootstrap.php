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

$cacheDir = __DIR__ . '/../build/cache/goaop';
if (!is_dir($cacheDir)) {
    if (mkdir($cacheDir, 0775, true) === false) {
        echo "\n[ERROR] Unable to create cache directory at {$cacheDir}\n\n";
        exit(1);
    }
}

$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => $cacheDir,
    'includePaths' => [__DIR__ . '/../src']
]);
