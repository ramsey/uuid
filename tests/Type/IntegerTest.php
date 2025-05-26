<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer as IntegerObject;

use function json_encode;
use function serialize;
use function sprintf;
use function unserialize;

class IntegerTest extends TestCase
{
    /**
     * @param int|float|string|IntegerObject $value
     *
     * @dataProvider provideInteger
     */
    public function testIntegerType($value, string $expected, bool $expectedIsNegative): void
    {
        $integer = new IntegerObject($value);

        $this->assertSame($expected, $integer->toString());
        $this->assertSame($expected, (string) $integer);
        $this->assertSame($expectedIsNegative, $integer->isNegative());
    }

    /**
     * @return array<array{value: int|float|string|IntegerObject, expected: string, expectedIsNegative: bool}>
     */
    public function provideInteger(): array
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

        new IntegerObject($value);
    }

    /**
     * @return array<array{0: int|float|string}>
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

    /**
     * @param int|float|string|IntegerObject $value
     *
     * @dataProvider provideInteger
     */
    public function testSerializeUnserializeInteger($value, string $expected): void
    {
        $integer = new IntegerObject($value);
        $serializedInteger = serialize($integer);

        /** @var IntegerObject $unserializedInteger */
        $unserializedInteger = unserialize($serializedInteger);

        $this->assertSame($expected, $unserializedInteger->toString());
    }

    /**
     * @param int|float|string|IntegerObject $value
     *
     * @dataProvider provideInteger
     */
    public function testJsonSerialize($value, string $expected): void
    {
        $integer = new IntegerObject($value);
        $expectedJson = sprintf('"%s"', $expected);

        $this->assertSame($expectedJson, json_encode($integer));
    }
}
