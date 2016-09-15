<?php

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;

/**
 * Class DegradedNumberConverterTest
 * @package Ramsey\Uuid\Test\Converter\Number
 * @covers Ramsey\Uuid\Converter\Number\DegradedNumberConverter
 */
class DegradedNumberConverterTest extends TestCase
{
    /**
     * @expectedException Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $converter->fromHex('ffff');
    }

    /**
     * @expectedException Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $converter->toHex(0);
    }
}
