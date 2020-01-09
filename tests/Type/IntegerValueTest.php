<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\IntegerValue;

class IntegerValueTest extends TestCase
{
    /**
     * @param int|float|string $value
     *
     * @dataProvider provideInteger
     */
    public function testIntegerValueType($value, string $expected): void
    {
        $integer = new IntegerValue($value);

        $this->assertSame($expected, $integer->toString());
        $this->assertSame($expected, (string) $integer);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideInteger(): array
    {
        return [
            [
                'value' => '-11386878954224802805705605120',
                'expected' => '-11386878954224802805705605120',
            ],
            [
                'value' => '-9223372036854775808',
                'expected' => '-9223372036854775808',
            ],
            [
                'value' => -99986838650880,
                'expected' => '-99986838650880',
            ],
            [
                'value' => -4294967296,
                'expected' => '-4294967296',
            ],
            [
                'value' => -2147483649,
                'expected' => '-2147483649',
            ],
            [
                'value' => -123456.0,
                'expected' => '-123456',
            ],
            [
                'value' => -1.00000000000001,
                'expected' => '-1',
            ],
            [
                'value' => -1,
                'expected' => '-1',
            ],
            [
                'value' => '-1',
                'expected' => '-1',
            ],
            [
                'value' => 0,
                'expected' => '0',
            ],
            [
                'value' => '0',
                'expected' => '0',
            ],
            [
                'value' => -0,
                'expected' => '0',
            ],
            [
                'value' => '-0',
                'expected' => '0',
            ],
            [
                'value' => '+0',
                'expected' => '0',
            ],
            [
                'value' => 1,
                'expected' => '1',
            ],
            [
                'value' => '1',
                'expected' => '1',
            ],
            [
                'value' => '+1',
                'expected' => '1',
            ],
            [
                'value' => 1.00000000000001,
                'expected' => '1',
            ],
            [
                'value' => 123456.0,
                'expected' => '123456',
            ],
            [
                'value' => 2147483648,
                'expected' => '2147483648',
            ],
            [
                'value' => 4294967294,
                'expected' => '4294967294',
            ],
            [
                'value' => 99965363767850,
                'expected' => '99965363767850',
            ],
            [
                'value' => '9223372036854775808',
                'expected' => '9223372036854775808',
            ],
            [
                'value' => '11386878954224802805705605120',
                'expected' => '11386878954224802805705605120',
            ],
        ];
    }

    /**
     * @param int|float|string $value
     *
     * @dataProvider provideIntegerBadValues
     */
    public function testIntegerTypeThrowsExceptionForBadValues($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only '
            . 'digits 0-9 and, optionally, a sign (+ or -)'
        );

        new IntegerValue($value);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideIntegerBadValues(): array
    {
        return [
            [-9223372036854775809], // String value is "-9.2233720368548E+18"
            [-123456.789],
            [-1.0000000000001],
            [-0.5],
            [0.5],
            [1.0000000000001],
            [123456.789],
            [9223372036854775808], // String value is "9.2233720368548E+18"
            ['123abc'],
            ['abc123'],
            ['foobar'],
        ];
    }
}
