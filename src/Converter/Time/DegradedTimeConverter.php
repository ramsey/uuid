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

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * DegradedTimeConverter is chosen if all other options for large integer
 * support are unavailable. This exists to throw exceptions if these methods
 * are called on systems that do not have support for large integers.
 */
class DegradedTimeConverter implements TimeConverterInterface
{
    /**
     * Uses the provided seconds and micro-seconds to calculate the time_low,
     * time_mid, and time_high fields used by RFC 4122 version 1 UUIDs
     *
     * @param string $seconds
     * @param string $microSeconds
     * @return string[] An array guaranteed to contain `low`, `mid`, and `high` keys
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     * @link http://tools.ietf.org/html/rfc4122#section-4.2.2
     */
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call calculateTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }

    /**
     * Converts a timestamp extracted from a UUID to a unix timestamp
     *
     * @param string $timestamp A string integer representation of a timestamp;
     *     this must be a numeric string to accommodate unsigned integers
     *     greater than PHP_INT_MAX.
     * @return string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function convertTime(string $timestamp): string
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call convertTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }
}
