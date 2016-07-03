<?php

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class DegradedTimeConverterTest
 * @covers DegradedTimeConverter
 */
class DegradedTimeConverterTest extends TestCase
{
    public function testConvertingFromHexThrowsException()
    {
        $converter = new DegradedTimeConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->calculateTime('123', '123');
    }

    public function testConvertingToHexThrowsException()
    {
        $converter = new DegradedTimeConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->convertTime(123);
    }
}
