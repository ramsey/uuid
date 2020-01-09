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
use Ramsey\Uuid\Type\IntegerValue;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations
 * available to the PHP programming language to provide facilities for
 * converting parts of time into representations that may be used in UUIDs
 */
class PhpTimeConverter implements TimeConverterInterface
{
    /**
     * @var TimeConverterInterface
     */
    private $fallbackConverter;

    public function __construct(?TimeConverterInterface $fallbackConverter = null)
    {
        if ($fallbackConverter === null) {
            $fallbackConverter = new GenericTimeConverter(new BrickMathCalculator());
        }

        $this->fallbackConverter = $fallbackConverter;
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
    public function convertTime(string $timestamp): string
    {
        $timestamp = new IntegerValue($timestamp);

        $unixTime = ((int) $timestamp->toString() - 0x01b21dd213814000) / 10000000;

        if (!is_int($unixTime)) {
            return $this->fallbackConverter->convertTime(
                $timestamp->toString()
            );
        }

        return (string) $unixTime;
    }
}
