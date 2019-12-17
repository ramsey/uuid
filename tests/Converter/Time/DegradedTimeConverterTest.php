<?php

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

class DegradedTimeConverterTest extends TestCase
{
    public function testCalculateTimeThrowsException(): void
    {
        $converter = new DegradedTimeConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call calculateTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );

        $converter->calculateTime('123', '123');
    }

    public function testConvertTimeThrowsException(): void
    {
        $converter = new DegradedTimeConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call convertTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );

        $converter->convertTime('123');
    }
}
