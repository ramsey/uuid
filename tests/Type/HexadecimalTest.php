<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

class HexadecimalTest extends TestCase
{
    /**
     * @param int|float|string $value
     *
     * @dataProvider provideHex
     */
    public function testHexadecimalType($value, string $expected): void
    {
        $hexadecimal = new Hexadecimal($value);

        $this->assertSame($expected, $hexadecimal->toString());
        $this->assertSame($expected, (string) $hexadecimal);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideHex(): array
    {
        return [
            [
                'value' => '0xFFFF',
                'expected' => 'ffff',
            ],
            [
                'value' => '0123456789abcdef',
                'expected' => '0123456789abcdef',
            ],
            [
                'value' => 'ABCDEF',
                'expected' => 'abcdef',
            ],
        ];
    }

    /**
     * @param int|float|string $value
     *
     * @dataProvider provideHexBadValues
     */
    public function testHexadecimalTypeThrowsExceptionForBadValues($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a hexadecimal number'
        );

        new Hexadecimal($value);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideHexBadValues(): array
    {
        return [
            ['-123456.789'],
            ['123456.789'],
            ['foobar'],
            ['0xfoobar'],
        ];
    }
}
