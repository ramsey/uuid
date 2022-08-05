<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Decimal;

use function json_encode;
use function serialize;
use function sprintf;
use function unserialize;

class DecimalTest extends TestCase
{
    /**
     * @param int|float|string|Decimal $value
     *
     * @dataProvider provideDecimal
     */
    public function testDecimalValueType($value, string $expected, bool $expectedIsNegative): void
    {
        $decimal = new Decimal($value);

        $this->assertSame($expected, $decimal->toString());
        $this->assertSame($expected, (string) $decimal);
        $this->assertSame($expectedIsNegative, $decimal->isNegative());
    }

    /**
     * @return array<array{value: int|float|string|Decimal, expected: string, expectedIsNegative: bool}>
     */
    public function provideDecimal(): array
    {
        return [
            [
                'value' => '-11386878954224802805705605120',
                'expected' => '-11386878954224802805705605120',
                'expectedIsNegative' => true,
            ],
            [
                'value' => '-9223372036854775808',
                'expected' => '-9223372036854775808',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -99986838650880,
                'expected' => '-99986838650880',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -4294967296,
                'expected' => '-4294967296',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -2147483649,
                'expected' => '-2147483649',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -123456.0,
                'expected' => '-123456',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -1.00000000000001,
                'expected' => '-1',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -1,
                'expected' => '-1',
                'expectedIsNegative' => true,
            ],
            [
                'value' => '-1',
                'expected' => '-1',
                'expectedIsNegative' => true,
            ],
            [
                'value' => 0,
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '0',
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -0,
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '-0',
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '+0',
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 1,
                'expected' => '1',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '1',
                'expected' => '1',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '+1',
                'expected' => '1',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 1.00000000000001,
                'expected' => '1',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 123456.0,
                'expected' => '123456',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 2147483648,
                'expected' => '2147483648',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 4294967294,
                'expected' => '4294967294',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 99965363767850,
                'expected' => '99965363767850',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '9223372036854775808',
                'expected' => '9223372036854775808',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '11386878954224802805705605120',
                'expected' => '11386878954224802805705605120',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -9223372036854775809,
                'expected' => '-9.2233720368548E+18',
                'expectedIsNegative' => true,
            ],
            [
                'value' => 9223372036854775808,
                'expected' => '9.2233720368548E+18',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -123456.789,
                'expected' => '-123456.789',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -1.0000000000001,
                'expected' => '-1.0000000000001',
                'expectedIsNegative' => true,
            ],
            [
                'value' => -0.5,
                'expected' => '-0.5',
                'expectedIsNegative' => true,
            ],
            [
                'value' => 0.5,
                'expected' => '0.5',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 1.0000000000001,
                'expected' => '1.0000000000001',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 123456.789,
                'expected' => '123456.789',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '-0',
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '+0',
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -0.00000000,
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => 0.00000000,
                'expected' => '0',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -0.0001,
                'expected' => '-0.0001',
                'expectedIsNegative' => true,
            ],
            [
                'value' => 0.0001,
                'expected' => '0.0001',
                'expectedIsNegative' => false,
            ],
            [
                'value' => -0.00001,
                'expected' => '-1.0E-5',
                'expectedIsNegative' => true,
            ],
            [
                'value' => 0.00001,
                'expected' => '1.0E-5',
                'expectedIsNegative' => false,
            ],
            [
                'value' => '+1234.56',
                'expected' => '1234.56',
                'expectedIsNegative' => false,
            ],
        ];
    }

    /**
     * @param int|float|string $value
     *
     * @dataProvider provideDecimalBadValues
     */
    public function testDecimalTypeThrowsExceptionForBadValues($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed decimal or a string containing only '
            . 'digits 0-9 and, optionally, a decimal point or sign (+ or -)'
        );

        new Decimal($value);
    }

    /**
     * @return array<array{0: int|float|string}>
     */
    public function provideDecimalBadValues(): array
    {
        return [
            ['123abc'],
            ['abc123'],
            ['foobar'],
            ['123.456a'],
            ['+abcd'],
            ['-0012.a'],
        ];
    }

    /**
     * @param float|int|string|Decimal $value
     *
     * @dataProvider provideDecimal
     */
    public function testSerializeUnserializeDecimal($value, string $expected): void
    {
        $decimal = new Decimal($value);
        $serializedDecimal = serialize($decimal);

        /** @var Decimal $unserializedDecimal */
        $unserializedDecimal = unserialize($serializedDecimal);

        $this->assertSame($expected, $unserializedDecimal->toString());
    }

    /**
     * @param float|int|string|Decimal $value
     *
     * @dataProvider provideDecimal
     */
    public function testJsonSerialize($value, string $expected): void
    {
        $decimal = new Decimal($value);
        $expectedJson = sprintf('"%s"', $expected);

        $this->assertSame($expectedJson, json_encode($decimal));
    }
}
