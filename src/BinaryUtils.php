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

namespace Ramsey\Uuid;

use function is_int;

/**
 * Provides binary math utilities
 */
class BinaryUtils
{
    /**
     * Applies the RFC 4122 variant field to the `clock_seq_hi_and_reserved` field
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     *
     * @param int $clockSeqHi The value of `clock_seq_hi_and_reserved` field
     *     before the RFC 4122 variant is applied
     *
     * @return int The high field of the clock sequence multiplexed with the variant
     *
     * @psalm-pure
     */
    public static function applyVariant(int $clockSeqHi): int
    {
        // Set the variant to RFC 4122.
        $clockSeqHi = $clockSeqHi & 0x3f;
        $clockSeqHi |= 0x80;

        return $clockSeqHi;
    }

    /**
     * Applies the RFC 4122 version number to the `time_hi_and_version` field
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     *
     * @param string $timeHi The value of the `time_hi_and_version` field before
     *     the RFC 4122 version is applied
     * @param int $version The RFC 4122 version to apply to the `time_hi` field
     *
     * @return int The high field of the timestamp multiplexed with the version number
     *
     * @psalm-pure
     */
    public static function applyVersion(string $timeHi, int $version): int
    {
        $timeHi = hexdec($timeHi) & 0x0fff;
        $timeHi |= $version << 12;

        assert(is_int($timeHi));

        return $timeHi;
    }
}
