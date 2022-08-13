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

use Ramsey\Uuid\Rfc4122\Version;

use function pack;
use function substr;
use function substr_replace;
use function unpack;

/**
 * Provides binary math utilities
 */
class BinaryUtils
{
    /**
     * Applies the RFC 4122 variant field to the 16-bit clock sequence
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     *
     * @param int $clockSeq The 16-bit clock sequence value before the RFC 4122
     *     variant is applied
     *
     * @return int The 16-bit clock sequence multiplexed with the UUID variant
     *
     * @psalm-pure
     */
    public static function applyVariant(int $clockSeq, Variant $variant = Variant::Rfc4122): int
    {
        return match ($variant) {
            Variant::ReservedNcs => $clockSeq & 0x7fff,
            Variant::Rfc4122 => $clockSeq & 0x3fff | 0x8000,
            Variant::ReservedMicrosoft => $clockSeq & 0x1fff | 0xc000,
            Variant::ReservedFuture => $clockSeq & 0x1fff | 0xe000,
        };
    }

    /**
     * Applies the RFC 4122 version number to the 16-bit `time_hi_and_version` field
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     *
     * @param int $timeHi The value of the 16-bit `time_hi_and_version` field
     *     before the RFC 4122 version is applied
     * @param Version $version The RFC 4122 version to apply to the `time_hi` field
     *
     * @return int The 16-bit time_hi field of the timestamp multiplexed with
     *     the UUID version number
     *
     * @psalm-pure
     */
    public static function applyVersion(int $timeHi, Version $version): int
    {
        $timeHi = $timeHi & 0x0fff;
        $timeHi |= $version->value << 12;

        return $timeHi;
    }

    /**
     * Applies the RFC 4122 version number and variant field to the 128-bit
     * integer (as a 16-byte string) provided
     *
     * @param non-empty-string $bytes A 128-bit integer (16-byte string) to
     *     which the RFC 4122 version number and variant field will be applied,
     *     making the number a valid UUID
     * @param Version $version The RFC 4122 version to apply
     *
     * @return non-empty-string A 16-byte string with the UUID version and variant applied
     *
     * @psalm-pure
     */
    public static function applyVersionAndVariant(
        string $bytes,
        Version $version,
        Variant $variant = Variant::Rfc4122
    ): string {
        /** @var array $unpackedTime */
        $unpackedTime = unpack('n*', substr($bytes, 6, 2));
        $timeHi = (int) $unpackedTime[1];
        $timeHiAndVersion = pack('n*', self::applyVersion($timeHi, $version));

        /** @var array $unpackedClockSeq */
        $unpackedClockSeq = unpack('n*', substr($bytes, 8, 2));
        $clockSeqHi = (int) $unpackedClockSeq[1];
        $clockSeqHiAndReserved = pack('n*', self::applyVariant($clockSeqHi, $variant));

        $bytes = substr_replace($bytes, $timeHiAndVersion, 6, 2);

        /** @var non-empty-string */
        return substr_replace($bytes, $clockSeqHiAndReserved, 8, 2);
    }
}
