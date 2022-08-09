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
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function count;
use function dechex;
use function explode;
use function is_float;
use function is_int;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations
 * available to the PHP programming language to provide facilities for
 * converting parts of time into representations that may be used in UUIDs
 *
 * @psalm-immutable
 */
class PhpTimeConverter implements TimeConverterInterface
{
    /**
     * The number of 100-nanosecond intervals from the Gregorian calendar epoch
     * to the Unix epoch.
     */
    private const GREGORIAN_TO_UNIX_INTERVALS = 0x01b21dd213814000;

    /**
     * The number of 100-nanosecond intervals in one second.
     */
    private const SECOND_INTERVALS = 10000000;

    /**
     * The number of 100-nanosecond intervals in one microsecond.
     */
    private const MICROSECOND_INTERVALS = 10;

    private readonly int $phpPrecision;

    public function __construct(
        private readonly CalculatorInterface $calculator = new BrickMathCalculator(),
        private readonly TimeConverterInterface $fallbackConverter = new GenericTimeConverter(new BrickMathCalculator())
    ) {
        $this->phpPrecision = (int) ini_get('precision');
    }

    public function calculateTime(string $seconds, string $microseconds): Hexadecimal
    {
        $seconds = new IntegerObject($seconds);
        $microseconds = new IntegerObject($microseconds);

        // Calculate the count of 100-nanosecond intervals since the Gregorian
        // calendar epoch for the given seconds and microseconds.
        $uuidTime = ((int) $seconds->toString() * self::SECOND_INTERVALS)
            + ((int) $microseconds->toString() * self::MICROSECOND_INTERVALS)
            + self::GREGORIAN_TO_UNIX_INTERVALS;

        // Check to see whether we've overflowed the max/min integer size.
        // If so, we will default to a different time converter.
        /** @psalm-suppress RedundantCondition */
        if (!is_int($uuidTime)) {
            return $this->fallbackConverter->calculateTime(
                $seconds->toString(),
                $microseconds->toString()
            );
        }

        return new Hexadecimal(str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT));
    }

    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        $timestamp = $this->calculator->toInteger($uuidTimestamp);

        // Convert the 100-nanosecond intervals into seconds and microseconds.
        $splitTime = $this->splitTime(
            ((int) $timestamp->toString() - self::GREGORIAN_TO_UNIX_INTERVALS)
            / self::SECOND_INTERVALS
        );

        if (!isset($splitTime['sec']) || !isset($splitTime['usec'])) {
            return $this->fallbackConverter->convertTime($uuidTimestamp);
        }

        return new Time($splitTime['sec'], $splitTime['usec']);
    }

    /**
     * @param float|int $time The time to split into seconds and microseconds
     *
     * @return array{sec?: numeric-string, usec?: numeric-string}
     */
    private function splitTime(float | int $time): array
    {
        /** @var numeric-string[] $split */
        $split = explode('.', (string) $time, 2);

        // If the $time value is a float but $split only has 1 element, then the
        // float math was rounded up to the next second, so we want to return
        // an empty array to allow use of the fallback converter.
        if (is_float($time) && count($split) === 1) {
            return [];
        }

        if (count($split) === 1) {
            return [
                'sec' => $split[0],
                'usec' => '0',
            ];
        }

        // If the microseconds are less than six characters AND the length of
        // the number is greater than or equal to the PHP precision, then it's
        // possible that we lost some precision for the microseconds. Return an
        // empty array, so that we can choose to use the fallback converter.
        if (strlen($split[1]) < 6 && strlen((string) $time) >= $this->phpPrecision) {
            return [];
        }

        $microseconds = $split[1];

        // Ensure the microseconds are no longer than 6 digits. If they are,
        // truncate the number to the first 6 digits and round up, if needed.
        if (strlen($microseconds) > 6) {
            $roundingDigit = (int) substr($microseconds, 6, 1);
            $microseconds = (int) substr($microseconds, 0, 6);

            if ($roundingDigit >= 5) {
                $microseconds++;
            }
        }

        /** @var numeric-string $microseconds */
        $microseconds = str_pad((string) $microseconds, 6, '0');

        return [
            'sec' => $split[0],
            'usec' => $microseconds,
        ];
    }
}
