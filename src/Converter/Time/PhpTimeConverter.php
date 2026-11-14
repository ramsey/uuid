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
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function dechex;
use function intdiv;
use function is_int;
use function str_pad;

use const STR_PAD_LEFT;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations available to the PHP programming language
 * to provide facilities for converting parts of time into representations that may be used in UUIDs
 *
 * @immutable
 */
class PhpTimeConverter implements TimeConverterInterface
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = 0x01b21dd213814000;

    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = 10_000_000;

    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = 10;

    /**
     * The number of microseconds in one second.
     */
    private const SECOND_MICROSECONDS = 1_000_000;

    /**
     * The largest count of 100-nanosecond intervals that fits in the 60-bit timestamp.
     */
    private const MAX_TIMESTAMP = 0x0fffffffffffffff;

    private CalculatorInterface $calculator;
    private TimeConverterInterface $fallbackConverter;

    public function __construct(
        ?CalculatorInterface $calculator = null,
        ?TimeConverterInterface $fallbackConverter = null,
    ) {
        if ($calculator === null) {
            $calculator = new BrickMathCalculator();
        }

        if ($fallbackConverter === null) {
            $fallbackConverter = new GenericTimeConverter($calculator);
        }

        $this->calculator = $calculator;
        $this->fallbackConverter = $fallbackConverter;
    }

    public function calculateTime(string $seconds, string $microseconds): Hexadecimal
    {
        $seconds = new IntegerObject($seconds); /** @phpstan-ignore possiblyImpure.new */
        $microseconds = new IntegerObject($microseconds); /** @phpstan-ignore possiblyImpure.new */

        // Calculate the count of 100-nanosecond intervals since the Gregorian calendar epoch
        // for the given seconds and microseconds.
        $uuidTime = ((int) $seconds->toString() * self::SECOND_INTERVALS)
            + ((int) $microseconds->toString() * self::MICROSECOND_INTERVALS)
            + self::GREGORIAN_TO_UNIX_INTERVALS;

        // Check to see whether we've overflowed the max/min integer size.
        // If so, we will default to a different time converter.
        // @phpstan-ignore function.alreadyNarrowedType (the integer value might have overflowed)
        if (!is_int($uuidTime)) {
            return $this->fallbackConverter->calculateTime(
                $seconds->toString(),
                $microseconds->toString(),
            );
        }

        if ($uuidTime < 0) {
            throw new InvalidArgumentException(
                'The time is earlier than the Gregorian calendar epoch, 1582-10-15 00:00:00 UTC',
            );
        }

        if ($uuidTime > self::MAX_TIMESTAMP) {
            throw new InvalidArgumentException(
                'The time is later than 5236-03-31 21:21:00.684697 UTC, the last time that fits in a 60-bit timestamp',
            );
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new Hexadecimal(
            str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT)
        );
    }

    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        $timestamp = $this->calculator->toInteger($uuidTimestamp)->toString();
        $intTimestamp = (int) $timestamp;

        // If the timestamp does not fit in a PHP integer (as on 32-bit systems), use the fallback converter.
        if ((string) $intTimestamp !== $timestamp) {
            return $this->fallbackConverter->convertTime($uuidTimestamp);
        }

        // The count of 100-nanosecond intervals from the Unix epoch, which is negative for times before the epoch.
        $intervals = $intTimestamp - self::GREGORIAN_TO_UNIX_INTERVALS;

        // Round the seconds down, so the remainder is never negative.
        $seconds = intdiv($intervals, self::SECOND_INTERVALS);
        $remainder = $intervals % self::SECOND_INTERVALS;

        if ($remainder < 0) {
            $seconds--;
            $remainder += self::SECOND_INTERVALS;
        }

        // Round the remainder to the nearest microsecond, with halves rounded up.
        $microseconds = intdiv($remainder + intdiv(self::MICROSECOND_INTERVALS, 2), self::MICROSECOND_INTERVALS);

        if ($microseconds === self::SECOND_MICROSECONDS) {
            $seconds++;
            $microseconds = 0;
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new Time($seconds, $microseconds);
    }
}
