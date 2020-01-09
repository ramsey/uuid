<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;

class GenericTimeConverterTest extends TestCase
{
    /**
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime(string $seconds, string $microSeconds, array $expected): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericTimeConverter($calculator);

        $result = $converter->calculateTime($seconds, $microSeconds);

        $this->assertSame($expected, $result);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideCalculateTime(): array
    {
        return [
            [
                'seconds' => '-12219146756',
                'microSeconds' => '0',
                'expected' => [
                    'low' => '0901e600',
                    'mid' => '0154',
                    'hi' => '0000',
                ],
            ],
            [
                'seconds' => '103072857659',
                'microseconds' => '999999',
                'expected' => [
                    'low' => 'ff9785f6',
                    'mid' => 'ffff',
                    'hi' => '0fff',
                ],
            ],

            // This is the earliest possible date supported by v1 UUIDs:
            // 1582-10-15 00:00:00.000000
            [
                'seconds' => '-12219292800',
                'microSeconds' => '0',
                'expected' => [
                    'low' => '00000000',
                    'mid' => '0000',
                    'hi' => '0000',
                ],
            ],

            // This is the last possible time supported by v1 UUIDs:
            // 60038-03-11 05:36:10.955161
            [
                'seconds' => '1832455114570',
                'microseconds' => '955161',
                'expected' => [
                    'low' => 'fffffffa',
                    'mid' => 'ffff',
                    'hi' => 'ffff',
                ],
            ]
        ];
    }

    /**
     * @dataProvider provideConvertTime
     */
    public function testConvertTime(string $uuidTimestamp, string $unixTimestamp): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericTimeConverter($calculator);

        $result = $converter->convertTime($uuidTimestamp);

        $this->assertSame($unixTimestamp, $result);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideConvertTime(): array
    {
        return [
            // This is the last possible time supported by v1 UUIDs. When
            // converted to a Unix timestamp, the microseconds are lost.
            // 60038-03-11 05:36:10.955161
            [
                'uuidTimestamp' => '18446744073709551610',
                'unixTimestamp' => '1832455114570',
            ],

            // This is the earliest possible date supported by v1 UUIDs:
            // 1582-10-15 00:00:00.000000
            [
                'uuidTimestamp' => '0',
                'unixTimestamp' => '-12219292800',
            ],

            // This is the Unix epoch:
            // 1970-01-01 00:00:00.000000
            [
                'uuidTimestamp' => '122192928000000000',
                'unixTimestamp' => '0',
            ],
        ];
    }
}
