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
use Ramsey\Uuid\Type\IntegerValue;
use Ramsey\Uuid\Type\Time;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations
 * available to the PHP programming language to provide facilities for
 * converting parts of time into representations that may be used in UUIDs
 */
class PhpTimeConverter implements TimeConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var TimeConverterInterface
     */
    private $fallbackConverter;

    /**
     * @var int
     */
    private $phpPrecision;

    public function __construct(
        ?CalculatorInterface $calculator = null,
        ?TimeConverterInterface $fallbackConverter = null
    ) {
        if ($calculator === null) {
            $calculator = new BrickMathCalculator();
        }

        if ($fallbackConverter === null) {
            $fallbackConverter = new GenericTimeConverter($calculator);
        }

        $this->calculator = $calculator;
        $this->fallbackConverter = $fallbackConverter;
        $this->phpPrecision = (int) ini_get('precision');
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        $seconds = new IntegerValue($seconds);
        $microSeconds = new IntegerValue($microSeconds);

        // 0x01b21dd213814000 is the number of 100-nanosecond intervals between the
        // UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
        // - A nanosecond is 1/1,000,000,000 of a second.
        // - A nanosecond is 1/1,000 of a microsecond.
        // - There are 10,000,000 100-nanosecond intervals within 1 second.
        // - There are 10 100-nanosecond intervals within a microsecond.
        $uuidTime = ((int) $seconds->toString() * 10000000)
            + ((int) $microSeconds->toString() * 10)
            + 0x01b21dd213814000;

        // Check to see whether we've overflowed the max/min integer size.
        // If so, we will default to a different time converter.
        if (!is_int($uuidTime)) {
            return $this->fallbackConverter->calculateTime(
                $seconds->toString(),
                $microSeconds->toString()
            );
        }

        /** @psalm-suppress MixedArgument */
        return [
            'low' => sprintf('%08x', $uuidTime & 0xffffffff),
            'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
        ];
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        $timestamp = $this->calculator->toIntegerValue($uuidTimestamp);

        $splitTime = $this->splitTime(
            ((int) $timestamp->toString() - 0x01b21dd213814000) / 10000000
        );

        if (count($splitTime) === 0) {
            return $this->fallbackConverter->convertTime($uuidTimestamp);
        }

        return new Time($splitTime['sec'], $splitTime['usec']);
    }

    /**
     * @param int|float $time The time to split into seconds and microseconds
     *
     * @return string[]
     *
     * @psalm-pure
     */
    private function splitTime($time): array
    {
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
        // the number is greater than or equal the PHP precision , then it's
        // possible that we lost some precision for the microseconds. Return an
        // empty array, so that we can choose to use the fallback converter.
        if (strlen($split[1]) < 6 && strlen((string) $time) >= $this->phpPrecision) {
            return [];
        }

        return [
            'sec' => $split[0],
            'usec' => str_pad($split[1], 6, '0', STR_PAD_RIGHT),
        ];
    }
}
