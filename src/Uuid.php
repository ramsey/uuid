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
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Throwable;

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
 *
 * @psalm-immutable
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
     * @var UuidFactoryInterface|null
     */
    private static $factory = null;

    /**
     * @var CodecInterface
     */
    protected $codec;

    /**
     * The fields that make up this UUID
     *
     * @var Rfc4122FieldsInterface
     */
    protected $fields;

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
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use
     *     for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding
     *     UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use
     *     for converting timestamps extracted from a UUID to unix timestamps
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
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
        $this->numberConverter = $uuid->numberConverter;
        $this->fields = $uuid->fields;
    }

    public function compareTo(UuidInterface $other): int
    {
        $compare = strcmp($this->getInteger(), $other->getInteger());

        if ($compare < 0) {
            return -1;
        }

        if ($compare > 0) {
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
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqHiAndReserved()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSeqHiAndReserved(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqHiAndReserved()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqHiAndReserved()}.
     */
    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->fields->getClockSeqHiAndReserved()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqLow()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSeqLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeqLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqLow()}.
     */
    public function getClockSeqLowHex(): string
    {
        return $this->fields->getClockSeqLow()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeq()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSequence(): string
    {
        return $this->numberConverter->fromHex($this->fields->getClockSeq()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeq()}.
     */
    public function getClockSequenceHex(): string
    {
        return $this->fields->getClockSeq()->toString();
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no alternative
     *     recommendation, so plan accordingly.
     */
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }

    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed.
     *     It is available at {@see UuidV1::getDateTime()}.
     *
     * @return DateTimeImmutable An immutable instance of DateTimeInterface
     *
     * @throws UnsupportedOperationException if UUID is not time-based
     * @throws DateTimeException if DateTime throws an exception/error
     */
    public function getDateTime(): DateTimeInterface
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        $unixTime = $this->timeConverter->convertTime(
            $this->numberConverter->fromHex($this->fields->getTimestamp()->toString())
        );

        try {
            return new DateTimeImmutable("@{$unixTime}");
        } catch (Throwable $exception) {
            throw new DateTimeException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Returns the fields that comprise this UUID
     */
    public function getFields(): FieldsInterface
    {
        return $this->fields;
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance.
     *
     * @return string[]
     */
    public function getFieldsHex(): array
    {
        return [
            'time_low' => $this->fields->getTimeLow()->toString(),
            'time_mid' => $this->fields->getTimeMid()->toString(),
            'time_hi_and_version' => $this->fields->getTimeHiAndVersion()->toString(),
            'clock_seq_hi_and_reserved' => $this->fields->getClockSeqHiAndReserved()->toString(),
            'clock_seq_low' => $this->fields->getClockSeqLow()->toString(),
            'node' => $this->fields->getNode()->toString(),
        ];
    }

    public function getHex(): string
    {
        return str_replace('-', '', $this->toString());
    }

    public function getInteger(): string
    {
        return $this->numberConverter->fromHex($this->getHex());
    }

    /**
     * Returns the least significant 64 bits of the UUID
     */
    public function getLeastSignificantBits(): string
    {
        return $this->numberConverter->fromHex($this->getLeastSignificantBitsHex());
    }

    public function getLeastSignificantBitsHex(): string
    {
        return sprintf(
            '%02s%02s%012s',
            $this->fields->getClockSeqHiAndReserved()->toString(),
            $this->fields->getClockSeqLow()->toString(),
            $this->fields->getNode()->toString()
        );
    }

    /**
     * Returns the most significant 64 bits of the UUID
     */
    public function getMostSignificantBits(): string
    {
        return $this->numberConverter->fromHex($this->getMostSignificantBitsHex());
    }

    public function getMostSignificantBitsHex(): string
    {
        return sprintf(
            '%08s%04s%04s',
            $this->fields->getTimeLow()->toString(),
            $this->fields->getTimeMid()->toString(),
            $this->fields->getTimeHiAndVersion()->toString()
        );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getNode()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getNode(): string
    {
        return $this->numberConverter->fromHex($this->fields->getNode()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getNode()}.
     */
    public function getNodeHex(): string
    {
        return $this->fields->getNode()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeHiAndVersion()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeHiAndVersion(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeHiAndVersion()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeHiAndVersion()}.
     */
    public function getTimeHiAndVersionHex(): string
    {
        return $this->fields->getTimeHiAndVersion()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeLow()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeLow(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeLow()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeLow()}.
     */
    public function getTimeLowHex(): string
    {
        return $this->fields->getTimeLow()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeMid()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeMid(): string
    {
        return $this->numberConverter->fromHex($this->fields->getTimeMid()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeMid()}.
     */
    public function getTimeMidHex(): string
    {
        return $this->fields->getTimeMid()->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimestamp()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimestamp(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $this->numberConverter->fromHex($this->fields->getTimestamp()->toString());
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimestamp()}.
     */
    public function getTimestampHex(): string
    {
        if ($this->fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $this->fields->getTimestamp()->toString();
    }

    /**
     * @deprecated This has moved to {@see Rfc4122FieldsInterface::getUrn()} and
     *     is available on {@see \Ramsey\Uuid\Rfc4122\UuidV1},
     *     {@see \Ramsey\Uuid\Rfc4122\UuidV3}, {@see \Ramsey\Uuid\Rfc4122\UuidV4},
     *     and {@see \Ramsey\Uuid\Rfc4122\UuidV5}.
     */
    public function getUrn(): string
    {
        return 'urn:uuid:' . $this->toString();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVariant()}.
     */
    public function getVariant(): ?int
    {
        return $this->fields->getVariant();
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVersion()}.
     */
    public function getVersion(): ?int
    {
        return $this->fields->getVersion();
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
        if (self::$factory === null) {
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
