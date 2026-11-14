<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function str_pad;
use function strcmp;
use function strlen;

use const STR_PAD_LEFT;

/**
 * GenericTimeConverter uses the provided calculator to calculate and convert time values
 *
 * @immutable
 */
class GenericTimeConverter implements TimeConverterInterface
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = '122192928000000000';

    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = '10000000';

    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = '10';

    /**
     * The number of microseconds in one second.
     */
    private const SECOND_MICROSECONDS = '1000000';

    /**
     * The largest count of 100-nanosecond intervals that fits in the 60-bit timestamp.
     */
    private const MAX_TIMESTAMP = '1152921504606846975';

    public function __construct(private CalculatorInterface $calculator)
    {
    }

    public function calculateTime(string $seconds, string $microseconds): Hexadecimal
    {
        /** @phpstan-ignore possiblyImpure.new */
        $timestamp = new Time($seconds, $microseconds);

        // Convert the seconds into a count of 100-nanosecond intervals.
        $sec = $this->calculator->multiply(
            $timestamp->getSeconds(),
            new IntegerObject(self::SECOND_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
        );

        // Convert the microseconds into a count of 100-nanosecond intervals.
        $usec = $this->calculator->multiply(
            $timestamp->getMicroseconds(),
            new IntegerObject(self::MICROSECOND_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
        );

        /**
         * Combine the intervals of seconds and microseconds and add the count of 100-nanosecond intervals from the
         * Gregorian calendar epoch to the Unix epoch. This gives us the correct count of 100-nanosecond intervals since
         * the Gregorian calendar epoch for the given seconds and microseconds.
         *
         * @var IntegerObject $uuidTime
         * @phpstan-ignore possiblyImpure.new
         */
        $uuidTime = $this->calculator->add($sec, $usec, new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS));

        if ($uuidTime->isNegative()) {
            throw new InvalidArgumentException(
                'The time is earlier than the Gregorian calendar epoch, 1582-10-15 00:00:00 UTC',
            );
        }

        $intervals = $uuidTime->toString();

        if (
            strlen($intervals) > strlen(self::MAX_TIMESTAMP)
            || (strlen($intervals) === strlen(self::MAX_TIMESTAMP) && strcmp($intervals, self::MAX_TIMESTAMP) > 0)
        ) {
            throw new InvalidArgumentException(
                'The time is later than 5236-03-31 21:21:00.684697 UTC, the last time that fits in a 60-bit timestamp',
            );
        }

        /**
         * PHPStan considers CalculatorInterface::toHexadecimal, Hexadecimal:toString impure.
         *
         * @phpstan-ignore possiblyImpure.new
         */
        return new Hexadecimal(str_pad($this->calculator->toHexadecimal($uuidTime)->toString(), 16, '0', STR_PAD_LEFT));
    }

    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        // From the total, subtract the number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix
        // epoch. This gives us the number of 100-nanosecond intervals from the Unix epoch, which also includes the microtime.
        $epochNanoseconds = $this->calculator->subtract(
            $this->calculator->toInteger($uuidTimestamp),
            new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
        );

        // Round the seconds down, so the remainder is never negative.
        $seconds = $this->calculator->divide(
            RoundingMode::FLOOR,
            0,
            $epochNanoseconds,
            new IntegerObject(self::SECOND_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
        );

        $remainder = $this->calculator->subtract(
            $epochNanoseconds,
            $this->calculator->multiply(
                $seconds,
                new IntegerObject(self::SECOND_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
            ),
        );

        // Round the remainder to the nearest microsecond, with halves rounded up.
        $microseconds = $this->calculator->divide(
            RoundingMode::HALF_UP,
            0,
            $remainder,
            new IntegerObject(self::MICROSECOND_INTERVALS), /** @phpstan-ignore possiblyImpure.new */
        );

        if ($microseconds->toString() === self::SECOND_MICROSECONDS) {
            $seconds = $this->calculator->add(
                $seconds,
                new IntegerObject(1), /** @phpstan-ignore possiblyImpure.new */
            );
            $microseconds = new IntegerObject(0); /** @phpstan-ignore possiblyImpure.new */
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new Time($seconds->toString(), $microseconds->toString());
    }
}
