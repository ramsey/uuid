<?php
include __DIR__ . '/../vendor/autoload.php'; // composer autoload

$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => sys_get_temp_dir(),
    'includePaths' => [__DIR__ . '/../src']
]);
