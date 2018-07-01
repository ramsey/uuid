<?php

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;

/**
 * Class DegradedTimeConverterTest
 * @package Ramsey\Uuid\Test\Converter\Time
 * @covers Ramsey\Uuid\Converter\Time\DegradedTimeConverter
 */
class DegradedTimeConverterTest extends TestCase
{
    /**
     * @expectedException Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedTimeConverter();

        $converter->calculateTime(123, 123);
    }

    /**
     * @expectedException Ramsey\Uuid\Exception\UnsatisfiedDependencyException
     */
    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedTimeConverter();

        $converter->convertTime(123);
    }
}
