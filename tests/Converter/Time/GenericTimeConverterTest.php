<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

class GenericTimeConverterTest extends TestCase
{
    /**
     * @param numeric-string $seconds
     * @param numeric-string $microseconds
     * @param non-empty-string $expected
     *
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime(string $seconds, string $microseconds, string $expected): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericTimeConverter($calculator);

        $result = $converter->calculateTime($seconds, $microseconds);

        $this->assertSame($expected, $result->toString());
    }

    /**
     * @return array<array{seconds: numeric-string, microseconds: numeric-string, expected: non-empty-string}>
     */
    public function provideCalculateTime(): array
    {
        return [
            [
                'seconds' => '-12219146756',
                'microseconds' => '0',
                'expected' => '000001540901e600',
            ],
            [
                'seconds' => '103072857659',
                'microseconds' => '999999',
                'expected' => '0fffffffff9785f6',
            ],
            [
                'seconds' => '1578612359',
                'microseconds' => '521023',
                'expected' => '01ea333764c71df6',
            ],

            // This is the earliest possible date supported by v1 UUIDs:
            // 1582-10-15 00:00:00.000000
            [
                'seconds' => '-12219292800',
                'microseconds' => '0',
                'expected' => '0000000000000000',
            ],

            // This is the last possible time supported by v1 UUIDs:
            // 5236-03-31 21:21:00.684697
            // It is the last time that fits in the 60 bits of the timestamp.
            [
                'seconds' => '103072857660',
                'microseconds' => '684697',
                'expected' => '0ffffffffffffffa',
            ],
        ];
    }

    /**
     * @param numeric-string $unixTimestamp
     * @param numeric-string $microseconds
     *
     * @dataProvider provideConvertTime
     */
    public function testConvertTime(Hexadecimal $uuidTimestamp, string $unixTimestamp, string $microseconds): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericTimeConverter($calculator);

        $result = $converter->convertTime($uuidTimestamp);

        $this->assertSame($unixTimestamp, $result->getSeconds()->toString());
        $this->assertSame($microseconds, $result->getMicroseconds()->toString());
    }

    /**
     * @return array<array{uuidTimestamp: Hexadecimal, unixTimestamp: numeric-string, microseconds: numeric-string}>
     */
    public function provideConvertTime(): array
    {
        return [
            [
                'uuidTimestamp' => new Hexadecimal('1e1c57dff6f8cb0'),
                'unixTimestamp' => '1341368074',
                'microseconds' => '491000',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('1ea333764c71df6'),
                'unixTimestamp' => '1578612359',
                'microseconds' => '521023',
            ],
            [
                'uuidTimestamp' => new Hexadecimal('fffffffff9785f6'),
                'unixTimestamp' => '103072857659',
                'microseconds' => '999999',
            ],

            // This is the last possible time supported by v1 UUIDs. When
            // converted to a Unix timestamp, the microseconds are lost.
            // 60038-03-11 05:36:10.955161
            [
                'uuidTimestamp' => new Hexadecimal('fffffffffffffffa'),
                'unixTimestamp' => '1832455114570',
                'microseconds' => '955161',
            ],

            // This is the earliest possible date supported by v1 UUIDs:
            // 1582-10-15 00:00:00.000000
            [
                'uuidTimestamp' => new Hexadecimal('000000000000'),
                'unixTimestamp' => '-12219292800',
                'microseconds' => '0',
            ],

            // This is the Unix epoch:
            // 1970-01-01 00:00:00.000000
            [
                'uuidTimestamp' => new Hexadecimal('1b21dd213814000'),
                'unixTimestamp' => '0',
                'microseconds' => '0',
            ],

            // Microseconds that begin with a zero: 1700000000.0123456 rounds to 1700000000.012346.
            [
                'uuidTimestamp' => new Hexadecimal('1ee833b04b1a240'),
                'unixTimestamp' => '1700000000',
                'microseconds' => '12346',
            ],

            // Rounding to the nearest microsecond moves to the next second: 1700000000.9999995.
            [
                'uuidTimestamp' => new Hexadecimal('1ee833b0548567b'),
                'unixTimestamp' => '1700000001',
                'microseconds' => '0',
            ],

            // Before the epoch, the seconds are rounded down and the microseconds count forward from them. One
            // microsecond before the epoch is -1 seconds and 999999 microseconds.
            [
                'uuidTimestamp' => new Hexadecimal('1b21dd213813ff6'),
                'unixTimestamp' => '-1',
                'microseconds' => '999999',
            ],

            // One and a half seconds before the epoch.
            [
                'uuidTimestamp' => new Hexadecimal('1b21dd2129c5e40'),
                'unixTimestamp' => '-2',
                'microseconds' => '500000',
            ],

            // Six hundred nanoseconds before the epoch rounds to one microsecond before it.
            [
                'uuidTimestamp' => new Hexadecimal('1b21dd213813ffa'),
                'unixTimestamp' => '-1',
                'microseconds' => '999999',
            ],

            // Exactly half a microsecond before the epoch rounds up to the epoch.
            [
                'uuidTimestamp' => new Hexadecimal('1b21dd213813ffb'),
                'unixTimestamp' => '0',
                'microseconds' => '0',
            ],
        ];
    }

    /**
     * @dataProvider provideTimeBeforeGregorianEpoch
     */
    public function testCalculateTimeThrowsExceptionForTimeBeforeGregorianEpoch(
        string $seconds,
        string $microseconds
    ): void {
        $converter = new GenericTimeConverter(new BrickMathCalculator());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The time is earlier than the Gregorian calendar epoch, 1582-10-15 00:00:00 UTC',
        );

        $converter->calculateTime($seconds, $microseconds);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function provideTimeBeforeGregorianEpoch(): array
    {
        return [
            'one microsecond before' => ['-12219292801', '999999'],
            'the year 1200' => ['-24298358400', '0'],
            'beyond the range of a 64-bit integer' => ['-9223372036854775808', '0'],
        ];
    }

    public function testCalculateTimeAcceptsGregorianEpoch(): void
    {
        $converter = new GenericTimeConverter(new BrickMathCalculator());

        $this->assertSame('0000000000000000', $converter->calculateTime('-12219292800', '0')->toString());
    }

    /**
     * @dataProvider provideTimeAfterLastTimestamp
     */
    public function testCalculateTimeThrowsExceptionForTimeAfterLastTimestamp(
        string $seconds,
        string $microseconds
    ): void {
        $converter = new GenericTimeConverter(new BrickMathCalculator());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The time is later than 5236-03-31 21:21:00.684697 UTC, the last time that fits in a 60-bit timestamp',
        );

        $converter->calculateTime($seconds, $microseconds);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function provideTimeAfterLastTimestamp(): array
    {
        return [
            'one microsecond after' => ['103072857660', '684698'],
            'the year 5237' => ['103096632000', '0'],
            'a timestamp of 61 bits' => ['218365008121', '0'],
            'the last time that fits in 64 bits' => ['1832455114570', '955161'],
            'a timestamp of more than 64 bits' => ['1900000000000', '0'],
            'beyond the range of a 64-bit integer' => ['9223372036854775807', '0'],
        ];
    }
}
