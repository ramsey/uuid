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
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $this->expectException('Ramsey\Uuid\Exception\UnsatisfiedDependencyException');

        $converter->fromHex('ffff');
    }

    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedNumberConverter();

        $this->expectException('Ramsey\Uuid\Exception\UnsatisfiedDependencyException');

        $converter->toHex(0);
    }
}
