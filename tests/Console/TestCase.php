<?php
namespace Rhumsaa\Uuid\Console;

use Rhumsaa\Uuid\TestCase as UuidTestCase;

class TestCase extends UuidTestCase
{
    protected function setUp()
    {
        $this->skipIfNoSymfonyConsole();
        $this->skipIfNoMoontoastMath();
    }
}
