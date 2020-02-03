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
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\IntegerValue;

use function str_replace;
use function strcmp;

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
    use DeprecatedUuidMethodsTrait;

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
     * DCE Security principal domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_PERSON = 0;

    /**
     * DCE Security group domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_GROUP = 1;

    /**
     * DCE Security organization domain
     *
     * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
     */
    public const DCE_DOMAIN_ORG = 2;

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

    /** @psalm-return non-empty-string */
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

    /** @psalm-return non-empty-string */
    public function getBytes(): string
    {
        return $this->codec->encodeBinary($this);
    }

    public function getFields(): FieldsInterface
    {
        return $this->fields;
    }

    /**
     * @psalm-return non-empty-string
     * @psalm-suppress MoreSpecificReturnType we know that the retrieved `string` is never empty
     * @psalm-suppress LessSpecificReturnStatement we know that the retrieved `string` is never empty
     */
    public function getHex(): string
    {
        return str_replace('-', '', $this->toString());
    }

    /** @psalm-return non-empty-string */
    public function getInteger(): string
    {
        return $this->numberConverter->fromHex($this->getHex());
    }

    /** @psalm-return non-empty-string */
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
     * Creates a UUID from a DateTimeInterface instance
     *
     * @param DateTimeInterface $dateTime The date and time
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int|null $clockSeq A 14-bit number used to help avoid duplicates
     *     that could arise when the clock is set backwards in time or if the
     *     node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 1 UUID created from a DateTimeInterface instance
     */
    public static function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return self::getFactory()->fromDateTime($dateTime, $node, $clockSeq);
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
     * Returns a version 2 (DCE Security) UUID from a local domain, local
     * identifier, host ID, clock sequence, and the current time
     *
     * @param int $localDomain The local domain to use when generating bytes,
     *     according to DCE Security
     * @param IntegerValue|null $localIdentifier The local identifier for the
     *     given domain; this may be a UID or GID on POSIX systems, if the local
     *     domain is person or group, or it may be a site-defined identifier
     *     if the local domain is org
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int|null $clockSeq A 14-bit number used to help avoid duplicates
     *     that could arise when the clock is set backwards in time or if the
     *     node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     version 2 UUID
     */
    public static function uuid2(
        int $localDomain,
        ?IntegerValue $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface {
        return self::getFactory()->uuid2($localDomain, $localIdentifier, $node, $clockSeq);
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
