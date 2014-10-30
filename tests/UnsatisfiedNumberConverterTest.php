<?php

namespace Rhumsaa\Uuid;

class UnsatisfiedNumberConverterTest extends TestCase
{
    /**
     * @expectedException Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConverterThrowsException()
    {
        $converter = new UnsatisfiedNumberConverter();

        $converter->fromHex('ffff');
    }
}
