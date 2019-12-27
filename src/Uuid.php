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

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;

/**
 * Represents a RFC 4122 universally unique identifier (UUID)
 *
 * This class provides immutable UUID objects (the Uuid class) and the static
 * methods `uuid1()`, `uuid3()`, `uuid4()`, and `uuid5()` for generating version
 * 1, 3, 4, and 5 UUIDs as specified in RFC 4122.
 *
 * If all you want is a unique ID, you should probably call `uuid1()` or `uuid4()`.
 * Note that `uuid1()` may compromise privacy since it creates a UUID containing
 * the computer’s network address. `uuid4()` creates a random UUID.
 *
 * @link http://tools.ietf.org/html/rfc4122 RFC 4122
 */
class Uuid implements UuidInterface
{
    /**
     * When this namespace is specified, the name string is a fully-qualified
     * domain name
     *
     * @link http://tools.ietf.org/html/rfc4122#appendix-C RFC 4122, Appendix C: Some Name Space IDs
     */
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL
     *
     * @link http://tools.ietf.org/html/rfc4122#appendix-C RFC 4122, Appendix C: Some Name Space IDs
     */
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID
     *
     * @link http://tools.ietf.org/html/rfc4122#appendix-C RFC 4122, Appendix C: Some Name Space IDs
     */
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an X.500 DN in DER
     * or a text output format
     *
     * @link http://tools.ietf.org/html/rfc4122#appendix-C RFC 4122, Appendix C: Some Name Space IDs
     */
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * The nil UUID is a special form of UUID that is specified to have all 128
     * bits set to zero
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.7 RFC 4122, § 4.1.7: Nil UUID
     */
    public const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * Variant: reserved, NCS backward compatibility
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     */
    public const RESERVED_NCS = 0;

    /**
     * Variant: the UUID layout specified in RFC 4122
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     */
    public const RFC_4122 = 2;

    /**
     * Variant: reserved, Microsoft Corporation backward compatibility
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     */
    public const RESERVED_MICROSOFT = 6;

    /**
     * Variant: reserved for future definition
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     */
    public const RESERVED_FUTURE = 7;

    /**
     * Version 1 (time-based) UUID
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public const UUID_TYPE_TIME = 1;

    /**
     * Version 2 (identifier-based) UUID
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public const UUID_TYPE_IDENTIFIER = 2;

    /**
     * Version 3 (name-based and hashed with MD5) UUID
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public const UUID_TYPE_HASH_MD5 = 3;

    /**
     * Version 4 (random) UUID
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public const UUID_TYPE_RANDOM = 4;

    /**
     * Version 5 (name-based and hashed with SHA1) UUID
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
     */
    public const UUID_TYPE_HASH_SHA1 = 5;

    /**
     * @var UuidFactoryInterface
     */
    private static $factory = null;

    /**
     * @var CodecInterface
     */
    private $codec;

    /**
     * The fields that make up this UUID
     *
     * This is initialized to the nil value.
     *
     * @see UuidInterface::getFieldsHex()
     *
     * @var string[]
     */
    private $fields = [
        'time_low' => '00000000',
        'time_mid' => '0000',
        'time_hi_and_version' => '0000',
        'clock_seq_hi_and_reserved' => '00',
        'clock_seq_low' => '00',
        'node' => '000000000000',
    ];

    /**
     * @var NumberConverterInterface
     */
    protected $numberConverter;

    /**
     * @var TimeConverterInterface
     */
    protected $timeConverter;

    /**
     * Creates a universally unique identifier (UUID) from an array of fields
     *
     * Unless you're making advanced use of this library to generate identifiers
     * that deviate from RFC 4122, you probably do not want to instantiate a
     * UUID directly. Use the static methods, instead:
     *
     * ```
     * use Ramsey\Uuid\Uuid;
     *
     * $timeBasedUuid     = Uuid::uuid1();
     * $namespaceMd5Uuid  = Uuid::uuid3(Uuid::NAMESPACE_URL, 'http://php.net/');
     * $randomUuid        = Uuid::uuid4();
     * $namespaceSha1Uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, 'http://php.net/');
     * ```
     *
     * @param string[] $fields An array of fields from which to construct a UUID;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure
     * @param NumberConverterInterface $numberConverter The number converter to use
     *     for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding
     *     UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use
     *     for converting timestamps extracted from a UUID to unix timestamps
     */
    public function __construct(
        array $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        $this->fields = $fields;
        $this->codec = $codec;
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for JSON serialization
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Converts the UUID to a string for PHP serialization
     */
    public function serialize(): string
    {
        return $this->toString();
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into
     *     a UuidInterface instance
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function unserialize($serialized): void
    {
        /** @var \Ramsey\Uuid\Uuid $uuid */
        $uuid = self::fromString($serialized);
        $this->codec = $uuid->codec;
        $this->numberConverter = $uuid->getNumberConverter();
        $this->fields = $uuid->fields;
    }

    public function compareTo(UuidInterface $other): int
    {
        if ($this->getMostSignificantBitsHex() < $other->getMostSignificantBitsHex()) {
            return -1;
        }

        if ($this->getMostSignificantBitsHex() > $other->getMostSignificantBitsHex()) {
            return 1;
        }

        if ($this->getLeastSignificantBitsHex() < $other->getLeastSignificantBitsHex()) {
            return -1;
        }

        if ($this->getLeastSignificantBitsHex() > $other->getLeastSignificantBitsHex()) {
            return 1;
        }

        return 0;
    }

    public function equals(?object $other): bool
    {
        if (!$other instanceof UuidInterface) {
            return false;
        }

        return $this->compareTo($other) === 0;
    }

    public function getBytes(): string
    {
        return $this->codec->encodeBinary($this);
    }

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     */
    public function getClockSeqHiAndReserved(): int
    {
        return (int) hexdec($this->getClockSeqHiAndReservedHex());
    }

    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->fields['clock_seq_hi_and_reserved'];
    }

    /**
     * Returns the low field of the clock sequence
     */
    public function getClockSeqLow(): int
    {
        return (int) hexdec($this->getClockSeqLowHex());
    }

    public function getClockSeqLowHex(): string
    {
        return $this->fields['clock_seq_low'];
    }

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
    public function getClockSequence(): int
    {
        return ($this->getClockSeqHiAndReserved() & 0x3f) << 8 | $this->getClockSeqLow();
    }

    public function getClockSequenceHex(): string
    {
        return sprintf('%04x', $this->getClockSequence());
    }

    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }

    /**
     * @return DateTimeImmutable An immutable instance of DateTimeInterface
     *
     * @throws UnsupportedOperationException if UUID is not time-based
     * @throws DateTimeException if DateTime throws an exception/error
     */
    public function getDateTime(): DateTimeInterface
    {
        if ($this->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        $unixTime = $this->timeConverter->convertTime((string) $this->getTimestamp());

        try {
            return new DateTimeImmutable("@{$unixTime}");
        } catch (\Throwable $exception) {
            throw new DateTimeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Returns an array of the fields of this UUID, with keys named according
     * to the RFC 4122 names for the fields.
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
     * @return int[] The UUID fields represented as integer values
     */
    public function getFields(): array
    {
        return [
            'time_low' => $this->getTimeLow(),
            'time_mid' => $this->getTimeMid(),
            'time_hi_and_version' => $this->getTimeHiAndVersion(),
            'clock_seq_hi_and_reserved' => $this->getClockSeqHiAndReserved(),
            'clock_seq_low' => $this->getClockSeqLow(),
            'node' => $this->getNode(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldsHex(): array
    {
        return $this->fields;
    }

    public function getHex(): string
    {
        return str_replace('-', '', $this->toString());
    }

    /**
     * @throws UnsatisfiedDependencyException if large integer support is not available
     *
     * @inheritDoc
     */
    public function getInteger()
    {
        return $this->numberConverter->fromHex($this->getHex());
    }

    /**
     * Returns the least significant 64 bits of the UUID
     *
     * @return mixed
     *
     * @throws UnsatisfiedDependencyException if large integer support is not available
     */
    public function getLeastSignificantBits()
    {
        return $this->numberConverter->fromHex($this->getLeastSignificantBitsHex());
    }

    public function getLeastSignificantBitsHex(): string
    {
        return sprintf(
            '%02s%02s%012s',
            $this->fields['clock_seq_hi_and_reserved'],
            $this->fields['clock_seq_low'],
            $this->fields['node']
        );
    }

    /**
     * Returns the most significant 64 bits of the UUID
     *
     * @return mixed
     *
     * @throws UnsatisfiedDependencyException if large integer support is not available
     */
    public function getMostSignificantBits()
    {
        return $this->numberConverter->fromHex($this->getMostSignificantBitsHex());
    }

    public function getMostSignificantBitsHex(): string
    {
        return sprintf(
            '%08s%04s%04s',
            $this->fields['time_low'],
            $this->fields['time_mid'],
            $this->fields['time_hi_and_version']
        );
    }

    /**
     * Returns the node value associated with this UUID
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
     *
     * @return int Unsigned 48-bit integer value of node
     *
     * @throws UnsatisfiedDependencyException if large integer support is not available
     */
    public function getNode(): int
    {
        return (int) hexdec($this->getNodeHex());
    }

    public function getNodeHex(): string
    {
        return $this->fields['node'];
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     */
    public function getTimeHiAndVersion(): int
    {
        return (int) hexdec($this->getTimeHiAndVersionHex());
    }

    public function getTimeHiAndVersionHex(): string
    {
        return $this->fields['time_hi_and_version'];
    }

    /**
     * Returns the low field of the timestamp
     */
    public function getTimeLow(): int
    {
        return (int) hexdec($this->getTimeLowHex());
    }

    public function getTimeLowHex(): string
    {
        return $this->fields['time_low'];
    }

    /**
     * Returns the middle field of the timestamp
     */
    public function getTimeMid(): int
    {
        return (int) hexdec($this->getTimeMidHex());
    }

    public function getTimeMidHex(): string
    {
        return $this->fields['time_mid'];
    }

    /**
     * Returns the full timestamp value
     *
     * The 60 bit timestamp value is constructed from the time_low,
     * time_mid, and time_hi fields of this UUID. The resulting
     * timestamp is measured in 100-nanosecond units since midnight,
     * October 15, 1582 UTC.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1.
     *
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.4 RFC 4122, § 4.1.4: Timestamp
     *
     * @throws UnsatisfiedDependencyException if large integer support is not available
     * @throws UnsupportedOperationException if UUID is not time-based
     */
    public function getTimestamp(): int
    {
        if ($this->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return (int) hexdec($this->getTimestampHex());
    }

    public function getTimestampHex(): string
    {
        if ($this->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return sprintf(
            '%03x%04s%08s',
            ($this->getTimeHiAndVersion() & 0x0fff),
            $this->fields['time_mid'],
            $this->fields['time_low']
        );
    }

    public function getUrn(): string
    {
        return 'urn:uuid:' . $this->toString();
    }

    public function getVariant(): int
    {
        $clockSeq = $this->getClockSeqHiAndReserved();

        if (($clockSeq & 0x80) === 0) {
            return self::RESERVED_NCS;
        }

        if (($clockSeq & 0x40) === 0) {
            return self::RFC_4122;
        }

        if (($clockSeq & 0x20) === 0) {
            return self::RESERVED_MICROSOFT;
        }

        return self::RESERVED_FUTURE;
    }

    public function getVersion(): ?int
    {
        if ($this->getVariant() === self::RFC_4122) {
            return (int) (($this->getTimeHiAndVersion() >> 12) & 0x0f);
        }

        return null;
    }

    public function toString(): string
    {
        return $this->codec->encode($this);
    }

    /**
     * Returns the factory used to create UUIDs
     */
    public static function getFactory(): UuidFactoryInterface
    {
        if (!self::$factory) {
            self::$factory = new UuidFactory();
        }

        return self::$factory;
    }

    /**
     * Sets the factory used to create UUIDs
     *
     * @param UuidFactoryInterface $factory A factory that will be used by this
     *     class to create UUIDs
     */
    public static function setFactory(UuidFactoryInterface $factory): void
    {
        self::$factory = $factory;
    }

    /**
     * Creates a UUID from a byte string
     *
     * @param string $bytes A binary string
     *
     * @return UuidInterface A UuidInterface instance created from a binary
     *     string representation
     *
     * @psalm-pure note: changing the internal factory is an edge case not covered by purity invariants,
     *             but under constant factory setups, this method operates in functionally pure manners
     */
    public static function fromBytes(string $bytes): UuidInterface
    {
        return self::getFactory()->fromBytes($bytes);
    }

    /**
     * Creates a UUID from the string standard representation
     *
     * @param string $uuid A hexadecimal string
     *
     * @return UuidInterface A UuidInterface instance created from a hexadecimal
     *     string representation
     *
     * @psalm-pure note: changing the internal factory is an edge case not covered by purity invariants,
     *             but under constant factory setups, this method operates in functionally pure manners
     */
    public static function fromString(string $uuid): UuidInterface
    {
        return self::getFactory()->fromString($uuid);
    }

    /**
     * Creates a UUID from a 128-bit integer string
     *
     * @param string $integer String representation of 128-bit integer
     *
     * @return UuidInterface A UuidInterface instance created from the string
     *     representation of a 128-bit integer
     *
     * @psalm-pure note: changing the internal factory is an edge case not covered by purity invariants,
     *             but under constant factory setups, this method operates in functionally pure manners
     */
    public static function fromInteger(string $integer): UuidInterface
    {
        return self::getFactory()->fromInteger($integer);
    }

    /**
     * Returns true if the provided string is a valid UUID
     *
     * @param string $uuid A string to validate as a UUID
     *
     * @return bool True if the string is a valid UUID, false otherwise
     *
     * @psalm-pure note: changing the internal factory is an edge case not covered by purity invariants,
     *             but under constant factory setups, this method operates in functionally pure manners
     */
    public static function isValid(string $uuid): bool
    {
        return self::getFactory()->getValidator()->validate($uuid);
    }

    /**
     * Returns a version 1 (time-based) UUID from a host ID, sequence number,
     * and the current time
     *
     * @param int|string $node A 48-bit number representing the hardware address;
     *     this number may be represented as an integer or a hexadecimal string
     * @param int $clockSeq A 14-bit number used to help avoid duplicates that
     *     could arise when the clock is set backwards in time or if the node ID
     *     changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 1 UUID
     */
    public static function uuid1($node = null, ?int $clockSeq = null): UuidInterface
    {
        return self::getFactory()->uuid1($node, $clockSeq);
    }

    /**
     * Returns a version 3 (name-based) UUID based on the MD5 hash of a
     * namespace ID and a name
     *
     * @param string|UuidInterface $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 3 UUID
     */
    public static function uuid3($ns, string $name): UuidInterface
    {
        return self::getFactory()->uuid3($ns, $name);
    }

    /**
     * Returns a version 4 (random) UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 4 UUID
     */
    public static function uuid4(): UuidInterface
    {
        return self::getFactory()->uuid4();
    }

    /**
     * Returns a version 5 (name-based) UUID based on the SHA-1 hash of a
     * namespace ID and a name
     *
     * @param string|UuidInterface $ns The namespace (must be a valid UUID)
     * @param string $name The name to use for creating a UUID
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 5 UUID
     */
    public static function uuid5($ns, string $name): UuidInterface
    {
        return self::getFactory()->uuid5($ns, $name);
    }
}
