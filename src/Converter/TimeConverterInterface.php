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

namespace Ramsey\Uuid\Converter;

/**
 * A time converter converts timestamps into representations that may be used
 * in UUIDs
 */
interface TimeConverterInterface
{
    /**
     * Uses the provided seconds and micro-seconds to calculate the time_low,
     * time_mid, and time_high fields used by RFC 4122 version 1 UUIDs
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.2.2 RFC 4122, § 4.2.2: Generation Details
     *
     * @param string $seconds A string representation of the number of seconds
     *     since the Unix epoch for the time to calculate
     * @param string $microSeconds A string representation of the micro-seconds
     *     associated with the time to calculate
     *
     * @return string[] An array guaranteed to contain `low`, `mid`, and `hi` keys
     *
     * @psalm-pure
     */
    public function calculateTime(string $seconds, string $microSeconds): array;

    /**
     * Converts a timestamp extracted from a UUID to a Unix timestamp
     *
     * @param string $timestamp A string integer representation of a UUID
     *     timestamp; a UUID timestamp is a count of 100-nanosecond intervals
     *     since UTC 00:00:00.00, 15 October 1582; this must be a numeric string
     *     to accommodate unsigned integers greater than PHP_INT_MAX.
     *
     * @return string String representation of an integer
     *
     * @psalm-pure
     */
    public function convertTime(string $timestamp): string;
}
