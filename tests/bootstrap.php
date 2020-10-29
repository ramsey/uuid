<?php

/**
 * Test bootstrap
 *
 * @codingStandardsIgnoreFile
 */

error_reporting(E_ALL & ~E_DEPRECATED);

// Ensure floating-point precision is set to 14 (the default) for tests.
ini_set('precision', '14');

require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload
require_once __DIR__ . '/phpstan-bootstrap.php';
