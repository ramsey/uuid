<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Brick\Math\BigInteger;
use Ramsey\Uuid\Converter\Time\BigNumberTimeConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

use function sprintf;

class BigNumberTimeConverterTest extends TestCase
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

        $converter = new BigNumberTimeConverter();
        $returned = $converter->calculateTime((string) $seconds, (string) $microseconds);

        $this->assertSame($expected, $returned->toString());
    }

    public function testConvertTime(): void
    {
        $converter = new BigNumberTimeConverter();
        $returned = $converter->convertTime(new Hexadecimal('1e1c57dff6f8cb0'));

        $this->assertSame('1341368074', $returned->getSeconds()->toString());
    }

    public function testCalculateTimeThrowsExceptionWhenSecondsIsNotOnlyDigits(): void
    {
        $converter = new BigNumberTimeConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->calculateTime('12.34', '5678');
    }

    public function testCalculateTimeThrowsExceptionWhenMicrosecondsIsNotOnlyDigits(): void
    {
        $converter = new BigNumberTimeConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value must be a signed integer or a string containing only digits '
            . '0-9 and, optionally, a sign (+ or -)'
        );

        $converter->calculateTime('1234', '56.78');
    }
}
