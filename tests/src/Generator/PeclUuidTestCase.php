<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Test\TestCase;

if (!defined('UUID_TYPE_TIME')) {
    define('UUID_TYPE_TIME', 1);
}
if (!defined('UUID_TYPE_RANDOM')) {
    define('UUID_TYPE_RANDOM', 4);
}

class PeclUuidTestCase extends TestCase
{
    protected $uuidString = 'b08c6fff-7dc5-e111-9b21-0800200c9a66';
    protected $uuidBinary = '62303863366666662d376463352d653131312d396232312d303830303230306339613636';

    protected function setUp()
    {
        $this->skipIfHhvm();
        parent::setUp();
    }
}
