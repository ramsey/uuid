<?php

declare(strict_types=1);

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
        $this->expectExceptionMessage(
            'Cannot call fromHex using the DegradedNumberConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );

        $converter->fromHex('ffff');
    }

    public function testConvertingToHexThrowsException(): void
    {
        $converter = new DegradedNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call toHex using the DegradedNumberConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );

        $converter->toHex('0');
    }
}
