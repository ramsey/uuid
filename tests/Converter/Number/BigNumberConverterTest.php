<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Converter\Number\BigNumberConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;

class BigNumberConverterTest extends TestCase
{
    public function testFromHexThrowsExceptionWhenStringDoesNotContainOnlyHexadecimalCharacters(): void
    {
        $converter = new BigNumberConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"." is not a valid character in base 16');

        $converter->fromHex('123.34');
    }

    public function testToHexThrowsExceptionWhenStringDoesNotContainOnlyDigits(): void
    {
        $converter = new BigNumberConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->toHex('123.34');
    }

    public function testFromHex(): void
    {
        $converter = new BigNumberConverter();

        $this->assertSame('65535', $converter->fromHex('ffff'));
    }

    public function testToHex(): void
    {
        $converter = new BigNumberConverter();

        $this->assertSame('ffff', $converter->toHex('65535'));
    }
}
