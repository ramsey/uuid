<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Brick\Math\BigInteger;
use Mockery;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

use function sprintf;

class PhpTimeConverterTest extends TestCase
{
    public function testCalculateTimeReturnsArrayOfTimeSegments(): void
    {
        $seconds = BigInteger::of(5);
        $microseconds = BigInteger::of(3);

        $calculatedTime = BigInteger::zero()
            ->plus($seconds->multipliedBy(10000000))
            ->plus($microseconds->multipliedBy(10))
            ->plus(BigInteger::fromBase('01b21dd213814000', 16));

        $maskLow = BigInteger::fromBase('ffffffff', 16);
        $maskMid = BigInteger::fromBase('ffff', 16);
        $maskHi = BigInteger::fromBase('0fff', 16);

        $expected = sprintf('%04s', $calculatedTime->shiftedRight(48)->and($maskHi)->toBase(16));
        $expected .= sprintf('%04s', $calculatedTime->shiftedRight(32)->and($maskMid)->toBase(16));
        $expected .= sprintf('%08s', $calculatedTime->and($maskLow)->toBase(16));

        $converter = new PhpTimeConverter();
        $returned = $converter->calculateTime((string) $seconds, (string) $microseconds);

        $this->assertSame($expected, $returned->toString());
    }

    public function testCalculateTimeThrowsExceptionWhenSecondsIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->calculateTime('12.34', '5678');
    }

    public function testCalculateTimeThrowsExceptionWhenMicrosecondsIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->calculateTime('1234', '56.78');
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
        $fallbackConverter = new GenericTimeConverter($calculator);
        $converter = new PhpTimeConverter($calculator, $fallbackConverter);

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
        ];
    }

    /**
     * @param non-empty-string $seconds
     * @param non-empty-string $microseconds
     * @param non-empty-string $expected
     *
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime(string $seconds, string $microseconds, string $expected): void
    {
        $calculator = new BrickMathCalculator();
        $fallbackConverter = new GenericTimeConverter($calculator);
        $converter = new PhpTimeConverter($calculator, $fallbackConverter);

        $result = $converter->calculateTime($seconds, $microseconds);

        $this->assertSame($expected, $result->toString());
    }

    /**
     * @return array<array{seconds: non-empty-string, microseconds: non-empty-string, expected: non-empty-string}>
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
            // 60038-03-11 05:36:10.955161
            [
                'seconds' => '1832455114570',
                'microseconds' => '955161',
                'expected' => 'fffffffffffffffa',
            ],
        ];
    }
}
