<?php
namespace Ramsey\Uuid\Console;

use Ramsey\Uuid\TestCase as UuidTestCase;

class TestCase extends UuidTestCase
{
    protected function setUp()
    {
        $this->skipIfNoSymfonyConsole();
        $this->skipIfNoMoontoastMath();
    }
}
