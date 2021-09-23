<?php
// @codingStandardsIgnoreFile

use AspectMock\Kernel;

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload
require_once __DIR__ . '/phpstan-bootstrap.php';

if (PHP_MAJOR_VERSION < 8) {
    $kernel = Kernel::getInstance();
    $kernel->init([
        'debug' => true,
        'cacheDir' => sys_get_temp_dir(),
        'includePaths' => [__DIR__ . '/../src']
    ]);
}
