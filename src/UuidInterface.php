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

use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Serializable;

/**
 * A UUID is a universally unique identifier adhering to an agreed-upon
 * representation format and standard for generation
 *
 * @psalm-immutable
 */
interface UuidInterface extends JsonSerializable, Serializable
{
    /**
     * Returns -1, 0, or 1 if the UUID is less than, equal to, or greater than
     * the other UUID
     *
     * The first of two UUIDs is greater than the second if the most
     * significant field in which the UUIDs differ is greater for the first
     * UUID.
     *
     * * Q. What's the value of being able to sort UUIDs?
     * * A. Use them as keys in a B-Tree or similar mapping.
     *
     * @param UuidInterface $other The UUID to compare
     *
     * @return int -1, 0, or 1 if the UUID is less than, equal to, or greater than $other
     */
    public function compareTo(UuidInterface $other): int;

    /**
     * Returns true if the UUID is equal to the provided object
     *
     * The result is true if and only if the argument is not null, is a UUID
     * object, has the same variant, and contains the same value, bit for bit,
     * as the UUID.
     *
     * @param object|null $other An object to test for equality with this UUID
     *
     * @return bool True if the other object is equal to this UUID
     */
    public function equals(?object $other): bool;

    /**
     * Returns the binary string representation of the UUID
     */
    public function getBytes(): string;

    /**
     * Returns the number converter to use when converting hex values to/from integers
     */
    public function getNumberConverter(): NumberConverterInterface;

    /**
     * Returns the hexadecimal string representation of the UUID
     */
    public function getHex(): string;

    /**
     * Returns an array of the fields of the UUID, with keys named according
     * to the RFC 4122 names for the fields
     *
     * * **time_low**: The low field of the timestamp, an unsigned 32-bit integer
     * * **time_mid**: The middle field of the timestamp, an unsigned 16-bit integer
     * * **time_hi_and_version**: The high field of the timestamp multiplexed with
     *   the version number, an unsigned 16-bit integer
     * * **clock_seq_hi_and_reserved**: The high field of the clock sequence
     *   multiplexed with the variant, an unsigned 8-bit integer
     * * **clock_seq_low**: The low field of the clock sequence, an unsigned
     *   8-bit integer
     * * **node**: The spatially unique node identifier, an unsigned 48-bit
     *   integer
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.2 RFC 4122, § 4.1.2: Layout and Byte Order
     *
     * @return string[]
     */
    public function getFieldsHex(): array;

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     */
    public function getClockSeqHiAndReservedHex(): string;

    /**
     * Returns the low field of the clock sequence
     */
    public function getClockSeqLowHex(): string;

    /**
     * Returns the full clock sequence value
     *
     * For UUID version 1, the clock sequence is used to help avoid
     * duplicates that could arise when the clock is set backwards in time
     * or if the node ID changes.
     *
     * For UUID version 3 or 5, the clock sequence is a 14-bit value
     * constructed from a name as described in RFC 4122, Section 4.3.
     *
     * For UUID version 4, clock sequence is a randomly or pseudo-randomly
     * generated 14-bit value as described in RFC 4122, Section 4.4.
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.5 RFC 4122, § 4.1.5: Clock Sequence
     */
    public function getClockSequenceHex(): string;

    /**
     * Returns a DateTimeInterface object representing the timestamp associated
     * with the UUID
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1.
     *
     * @return DateTimeInterface A PHP DateTimeInterface instance representing
     *     the timestamp of a version 1 UUID
     */
    public function getDateTime(): DateTimeInterface;

    /**
     * Returns the 128-bit integer value of the UUID as a string
     */
    public function getInteger(): string;

    /**
     * Returns the least significant 64 bits of the UUID
     */
    public function getLeastSignificantBitsHex(): string;

    /**
     * Returns the most significant 64 bits of the UUID
     */
    public function getMostSignificantBitsHex(): string;

    /**
     * Returns the node value
     *
     * For UUID version 1, the node field consists of an IEEE 802 MAC
     * address, usually the host address. For systems with multiple IEEE
     * 802 addresses, any available one can be used. The lowest addressed
     * octet (octet number 10) contains the global/local bit and the
     * unicast/multicast bit, and is the first octet of the address
     * transmitted on an 802.3 LAN.
     *
     * For systems with no IEEE address, a randomly or pseudo-randomly
     * generated value may be used; see RFC 4122, Section 4.5. The
     * multicast bit must be set in such addresses, in order that they
     * will never conflict with addresses obtained from network cards.
     *
     * For UUID version 3 or 5, the node field is a 48-bit value constructed
     * from a name as described in RFC 4122, Section 4.3.
     *
     * For UUID version 4, the node field is a randomly or pseudo-randomly
     * generated 48-bit value as described in RFC 4122, Section 4.4.
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.6 RFC 4122, § 4.1.6: Node
     */
    public function getNodeHex(): string;

    /**
     * Returns the high field of the timestamp multiplexed with the version
     */
    public function getTimeHiAndVersionHex(): string;

    /**
     * Returns the low field of the timestamp
     */
    public function getTimeLowHex(): string;

    /**
     * Returns the middle field of the timestamp
     */
    public function getTimeMidHex(): string;

    /**
     * Returns the full timestamp value
     *
     * The 60 bit timestamp value is constructed from the time_low,
     * time_mid, and time_hi fields of the UUID. The resulting
     * timestamp is measured in 100-nanosecond units since midnight,
     * October 15, 1582 UTC.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1.
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.4 RFC 4122, § 4.1.4: Timestamp
     */
    public function getTimestampHex(): string;

    /**
     * Returns the string representation of the UUID as a URN
     *
     * @link http://en.wikipedia.org/wiki/Uniform_Resource_Name Uniform Resource Name
     */
    public function getUrn(): string;

    /**
     * Returns the variant
     *
     * The variant number describes the layout of the UUID. The variant
     * number has the following meaning:
     *
     * * 0 - Reserved for NCS backward compatibility
     * * 2 - The RFC 4122 variant
     * * 6 - Reserved, Microsoft Corporation backward compatibility
     * * 7 - Reserved for future definition
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     */
    public function getVariant(): ?int;

    /**
     * Returns the version
     *
     * The version number describes how the UUID was generated and has the
     * following meaning:
     *
     * * 1 - Time-based UUID
     * * 2 - DCE security UUID
     * * 3 - Name-based UUID hashed with MD5
     * * 4 - Randomly generated UUID
     * * 5 - Name-based UUID hashed with SHA-1
     *
     * This returns null if the UUID is not an RFC 4122 variant, since version
     * is only meaningful for this variant.
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public function getVersion(): ?int;

    /**
     * Returns a string representation of the UUID
     */
    public function toString(): string;

    /**
     * Casts the UUID to a string representation
     */
    public function __toString(): string;
}
