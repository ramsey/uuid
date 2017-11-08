<?php

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class DegradedNumberConverterTest
 * @package Ramsey\Uuid\Test\Converter\Number
 * @covers Ramsey\Uuid\Converter\Number\DegradedNumberConverter
 */
class DegradedNumberConverterTest extends TestCase
{
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->fromHex('ffff');
    }

    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->toHex(0);
    }
}
