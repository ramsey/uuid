<?php

namespace Rhumsaa\Uuid\Converter\Number;

use Rhumsaa\Uuid\TestCase;

class DegradedNumberConverterTest extends TestCase
{
    /**
     * @expectedException Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $converter->fromHex('ffff');
    }

    /**
     * @expectedException Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $converter->toHex(0);
    }
}
