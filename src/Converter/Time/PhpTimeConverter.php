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

use InvalidArgumentException;
use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations
 * available to the PHP programming language to provide facilities for
 * converting parts of time into representations that may be used in UUIDs
 */
class PhpTimeConverter implements TimeConverterInterface
{
    use DependencyCheckTrait;
    use NumberStringTrait;

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
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        $this->check64BitPhp();
        $this->checkIntegerString($seconds, 'seconds');
        $this->checkIntegerString($microSeconds, 'microSeconds');

        // 0x01b21dd213814000 is the number of 100-ns intervals between the
        // UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
        $uuidTime = ((int) $seconds * 10000000) + ((int) $microSeconds * 10) + 0x01b21dd213814000;

        return [
            'low' => sprintf('%08x', $uuidTime & 0xffffffff),
            'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
        ];
    }

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
    public function convertTime(string $timestamp): string
    {
        $this->check64BitPhp();
        $this->checkIntegerString($timestamp, 'timestamp');

        $unixTime = ((int) $timestamp - 0x01b21dd213814000) / 1e7;

        return number_format($unixTime, 0, '', '');
    }
}
