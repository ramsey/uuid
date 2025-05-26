<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Converter\Time\UnixTimeConverter;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

class UnixTimeConverterTest extends TestCase
{
    /**
     * @dataProvider provideConvertTime
     */
    public function testConvertTime(Hexadecimal $uuidTimestamp, string $unixTimestamp, string $microseconds): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new UnixTimeConverter($calculator);

        $result = $converter->convertTime($uuidTimestamp);

        $this->assertSame($unixTimestamp, $result->getSeconds()->toString());
        $this->assertSame($microseconds, $result->getMicroseconds()->toString());
    }

    /**
     * @return array<array{uuidTimestamp: Hexadecimal, unixTimestamp: string, microseconds: string}>
     */
    public function provideConvertTime(): array
    {
        return [
            [
                'uuidTimestamp' => new Hexadecimal('017F22E279B0'),
                'unixTimestamp' => '1645557742',
                'microseconds' => '0',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('01384fc480fb'),
                'unixTimestamp' => '1341368074',
                'microseconds' => '491000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('016f8ca10161'),
                'unixTimestamp' => '1578612359',
                'microseconds' => '521000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('5dbe85111a5f'),
                'unixTimestamp' => '103072857659',
                'microseconds' => '999000',
            ],

            // This is the last possible time supported by v7 UUIDs (2 ^ 48 - 1).
            // 10889-08-02 05:31:50.655 +00:00
            [
                'uuidTimestamp' => new Hexadecimal('ffffffffffff'),
                'unixTimestamp' => '281474976710',
                'microseconds' => '655000',
            ],

            // This is the earliest possible date supported by v7 UUIDs.
            // It is the Unix Epoch (big surprise!).
            // 1970-01-01 00:00:00.0 +00:00
            [
                'uuidTimestamp' => new Hexadecimal('000000000000'),
                'unixTimestamp' => '0',
                'microseconds' => '0',
            ],

            [
                'uuidTimestamp' => new Hexadecimal('000000000001'),
                'unixTimestamp' => '0',
                'microseconds' => '1000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('00000000000f'),
                'unixTimestamp' => '0',
                'microseconds' => '15000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('000000000064'),
                'unixTimestamp' => '0',
                'microseconds' => '100000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('0000000003e7'),
                'unixTimestamp' => '0',
                'microseconds' => '999000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('0000000003e8'),
                'unixTimestamp' => '1',
                'microseconds' => '0',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('0000000003e9'),
                'unixTimestamp' => '1',
                'microseconds' => '1000',
            ],
        ];
    }

    /**
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime(string $seconds, string $microseconds, string $expected): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new UnixTimeConverter($calculator);

        $result = $converter->calculateTime($seconds, $microseconds);

        $this->assertSame($expected, $result->toString());
    }

    /**
     * @return array<array{seconds: string, microseconds: string, expected: string}>
     */
    public function provideCalculateTime(): array
    {
        return [
            [
                'seconds' => '1645557742',
                'microseconds' => '0',
                'expected' => '017f22e279b0',
            ],
            [
                'seconds' => '1341368074',
                'microseconds' => '491000',
                'expected' => '01384fc480fb',
            ],
            [
                'seconds' => '1578612359',
                'microseconds' => '521023',
                'expected' => '016f8ca10161',
            ],
            [
                'seconds' => '103072857659',
                'microseconds' => '999499',
                'expected' => '5dbe85111a5f',
            ],
            [
                'seconds' => '103072857659',
                'microseconds' => '999999',
                'expected' => '5dbe85111a5f',
            ],

            // This is the earliest possible date supported by v7 UUIDs.
            // It is the Unix Epoch (big surprise!): 1970-01-01 00:00:00.0 +00:00
            [
                'seconds' => '0',
                'microseconds' => '0',
                'expected' => '000000000000',
            ],

            // This is the last possible time supported by v7 UUIDs (2 ^ 48 - 1):
            // 10889-08-02 05:31:50.655 +00:00
            [
                'seconds' => '281474976710',
                'microseconds' => '655000',
                'expected' => 'ffffffffffff',
            ],

            [
                'seconds' => '0',
                'microseconds' => '1000',
                'expected' => '000000000001',
            ],
            [
                'seconds' => '0',
                'microseconds' => '15000',
                'expected' => '00000000000f',
            ],
            [
                'seconds' => '0',
                'microseconds' => '100000',
                'expected' => '000000000064',
            ],
            [
                'seconds' => '0',
                'microseconds' => '999000',
                'expected' => '0000000003e7',
            ],
            [
                'seconds' => '1',
                'microseconds' => '0',
                'expected' => '0000000003e8',
            ],
            [
                'seconds' => '1',
                'microseconds' => '1000',
                'expected' => '0000000003e9',
            ],
        ];
    }
}
