<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

use function json_encode;
use function serialize;
use function sprintf;
use function unserialize;

class HexadecimalTest extends TestCase
{
    /**
     * @dataProvider provideHex
     */
    public function testHexadecimalType(string $value, string $expected): void
    {
        $hexadecimal = new Hexadecimal($value);

        $this->assertSame($expected, $hexadecimal->toString());
        $this->assertSame($expected, (string) $hexadecimal);
    }

    /**
     * @return array<array{value: string, expected: string}>
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
     * @dataProvider provideHexBadValues
     */
    public function testHexadecimalTypeThrowsExceptionForBadValues(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a hexadecimal number'
        );

        new Hexadecimal($value);
    }

    /**
     * @return array<array{0: string}>
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

    /**
     * @dataProvider provideHex
     */
    public function testSerializeUnserializeHexadecimal(string $value, string $expected): void
    {
        $hexadecimal = new Hexadecimal($value);
        $serializedHexadecimal = serialize($hexadecimal);

        /** @var Hexadecimal $unserializedHexadecimal */
        $unserializedHexadecimal = unserialize($serializedHexadecimal);

        $this->assertSame($expected, $unserializedHexadecimal->toString());
    }

    /**
     * @dataProvider provideHex
     */
    public function testJsonSerialize(string $value, string $expected): void
    {
        $hexadecimal = new Hexadecimal($value);
        $expectedJson = sprintf('"%s"', $expected);

        $this->assertSame($expectedJson, json_encode($hexadecimal));
    }
}
