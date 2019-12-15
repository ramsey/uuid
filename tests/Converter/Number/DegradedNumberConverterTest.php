<?php

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

class DegradedNumberConverterTest extends TestCase
{
    public function testConvertingFromHexThrowsException(): void
    {
        $converter = new DegradedNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->fromHex('ffff');
    }

    public function testConvertingToHexThrowsException(): void
    {
        $converter = new DegradedNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);

        $converter->toHex(0);
    }
}
