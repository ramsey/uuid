<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Converter;

use InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * TimeConverterInterface provides facilities for converting parts of time into
 * representations that may be used in UUIDs
 */
interface TimeConverterInterface
{
    /**
     * Uses the provided seconds and micro-seconds to calculate the time_low,
     * time_mid, and time_high fields used by RFC 4122 version 1 UUIDs
     *
     * @param string $seconds
     * @param string $microSeconds
     * @return string[] An array guaranteed to contain `low`, `mid`, and `high` keys
     * @throws InvalidArgumentException if $seconds or $microseconds are not integer strings
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     * @link http://tools.ietf.org/html/rfc4122#section-4.2.2
     */
    public function calculateTime(string $seconds, string $microSeconds): array;

    /**
     * Converts a timestamp extracted from a UUID to a unix timestamp
     *
     * @param string $timestamp A string integer representation of a timestamp;
     *     this must be a numeric string to accommodate unsigned integers
     *     greater than PHP_INT_MAX.
     * @return string
     * @throws InvalidArgumentException if $timestamp is not an integer string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function convertTime(string $timestamp): string;
}
