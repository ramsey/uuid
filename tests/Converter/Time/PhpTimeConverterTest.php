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

class PhpTimeConverterTest extends TestCase
{
    public function testCalculateTimeReturnsArrayOfTimeSegments(): void
    {
        $seconds = BigInteger::of(5);
        $microSeconds = BigInteger::of(3);

        $calculatedTime = BigInteger::zero()
            ->plus($seconds->multipliedBy(10000000))
            ->plus($microSeconds->multipliedBy(10))
            ->plus(BigInteger::fromBase('01b21dd213814000', 16));

        $maskLow = BigInteger::fromBase('ffffffff', 16);
        $maskMid = BigInteger::fromBase('ffff', 16);
        $maskHi = BigInteger::fromBase('0fff', 16);

        $expectedArray = [
            'low' => sprintf('%08s', $calculatedTime->and($maskLow)->toBase(16)),
            'mid' => sprintf('%04s', $calculatedTime->shiftedRight(32)->and($maskMid)->toBase(16)),
            'hi' => sprintf('%04s', $calculatedTime->shiftedRight(48)->and($maskHi)->toBase(16)),
        ];

        $converter = new PhpTimeConverter();
        $returned = $converter->calculateTime((string) $seconds, (string) $microSeconds);

        $this->assertSame($expectedArray, $returned);
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

    public function testCalculateTimeThrowsExceptionWhenMicroSecondsIsNotOnlyDigits(): void
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

    public function testConvertTimeThrowsExceptionWhenTimestampIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->convertTime('1234.56');
    }

    /**
     * @dataProvider provideConvertTime
     */
    public function testConvertTime(string $uuidTimestamp, string $unixTimestamp): void
    {
        $calculator = new BrickMathCalculator();
        $fallbackConverter = new GenericTimeConverter($calculator);
        $converter = new PhpTimeConverter($fallbackConverter);

        $result = $converter->convertTime($uuidTimestamp);

        $this->assertSame($unixTimestamp, $result);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideConvertTime(): array
    {
        return [
            [
                'uuidTimestamp' => '135606608744910000',
                'unixTimestamp' => '1341368074',
            ],
            [
                'uuidTimestamp' => '137979051595210230',
                'unixTimestamp' => '1578612359',
            ],
            [
                'uuidTimestamp' => '1152921504599999990',
                'unixTimestamp' => '103072857659',
            ],

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

    /**
     * @param string[] $expected
     *
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime(string $seconds, string $microSeconds, array $expected): void
    {
        $calculator = new BrickMathCalculator();
        $fallbackConverter = new GenericTimeConverter($calculator);
        $converter = new PhpTimeConverter($fallbackConverter);

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
            [
                'seconds' => '1578612359',
                'microseconds' => '521023',
                'expected' => [
                    'low' => '64c71df6',
                    'mid' => '3337',
                    'hi' => '01ea',
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
            ],
        ];
    }
}
