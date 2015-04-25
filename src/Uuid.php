<?php
/**
 * This file is part of the Ramsey\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2012-2014 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ramsey\Uuid;

use InvalidArgumentException;
use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * Represents a universally unique identifier (UUID), according to RFC 4122
 *
 * This class provides immutable UUID objects (the Uuid class) and the static
 * methods `uuid1()`, `uuid3()`, `uuid4()`, and `uuid5()` for generating version
 * 1, 3, 4, and 5 UUIDs as specified in RFC 4122.
 *
 * If all you want is a unique ID, you should probably call `uuid1()` or `uuid4()`.
 * Note that `uuid1()` may compromise privacy since it creates a UUID containing
 * the computer’s network address. `uuid4()` creates a random UUID.
 *
 * @link http://tools.ietf.org/html/rfc4122
 * @link http://en.wikipedia.org/wiki/Universally_unique_identifier
 * @link http://docs.python.org/3/library/uuid.html
 * @link http://docs.oracle.com/javase/6/docs/api/java/util/UUID.html
 */

class Uuid implements UuidInterface, \JsonSerializable
{
    /**
     * When this namespace is specified, the name string is a fully-qualified domain name.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.7
     */
    const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * Reserved for NCS compatibility.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_NCS = 0;

    /**
     * Specifies the UUID layout given in RFC 4122.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RFC_4122 = 2;

    /**
     * Reserved for Microsoft compatibility.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_MICROSOFT = 6;

    /**
     * Reserved for future definition.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_FUTURE = 7;

    /**
     * Regular expression pattern for matching a valid UUID of any variant.
     */
    const VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Version of the Ramsey\Uuid package
     */
    const VERSION = '3.0.x-dev';

    /**
     *
     * @var UuidFactoryInterface
     */
    private static $factory = null;

    /**
     * String codec
     * @var CodecInterface
     */
    protected $codec;

    /**
     * The fields that make up this UUID
     *
     * This is initialized to the nil value.
     *
     * @var array
     * @link Ramsey.Uuid.Uuid.html#method_getFields
     */
    protected $fields = array(
        'time_low' => '00000000',
        'time_mid' => '0000',
        'time_hi_and_version' => '0000',
        'clock_seq_hi_and_reserved' => '00',
        'clock_seq_low' => '00',
        'node' => '000000000000',
    );

    protected $converter;

    /**
     * Creates a universally unique identifier (UUID) from an array of fields.
     *
     * Protected to prevent direct instantiation. Use static methods to create
     * UUIDs.
     *
     * @param array $fields
     * @param CodecInterface $codec String codec
     * @link Ramsey.Uuid.Uuid.html#method_getFields
     */
    public function __construct(array $fields, NumberConverterInterface $converter, CodecInterface $codec)
    {
        $this->fields = $fields;
        $this->codec = $codec;
        $this->converter = $converter;
    }

    /**
     * Converts this UUID object to a string when the object is used in any
     * string context
     *
     * @return string
     * @link http://www.php.net/manual/en/language.oop5.magic.php#object.tostring
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Converts this UUID object to a string when the object is serialized
     * with json_encode()
     *
     * @link http://php.net/manual/en/class.jsonserializable.php
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toString();
    }

    /**
     * Compares this UUID with the specified UUID.
     *
     * The first of two UUIDs is greater than the second if the most
     * significant field in which the UUIDs differ is greater for the first
     * UUID.
     *
     * Q. What's the value of being able to sort UUIDs?<br>
     * A. Use them as keys in a B-Tree or similar mapping.
     *
     * @param Uuid $uuid UUID to which this UUID is to be compared
     * @return int -1, 0 or 1 as this UUID is less than, equal to, or greater than $uuid
     */
    public function compareTo(UuidInterface $uuid)
    {
        $comparison = 0;

        if ($this->getMostSignificantBitsHex() < $uuid->getMostSignificantBitsHex()) {
            $comparison = -1;
        } elseif ($this->getMostSignificantBitsHex() > $uuid->getMostSignificantBitsHex()) {
            $comparison = 1;
        } elseif ($this->getLeastSignificantBitsHex() < $uuid->getLeastSignificantBitsHex()) {
            $comparison = -1;
        } elseif ($this->getLeastSignificantBitsHex() > $uuid->getLeastSignificantBitsHex()) {
            $comparison = 1;
        }

        return $comparison;
    }

    /**
     * Compares this object to the specified object.
     *
     * The result is true if and only if the argument is not null, is a UUID
     * object, has the same variant, and contains the same value, bit for bit,
     * as this UUID.
     *
     * @param object $obj
     * @return bool True if $obj is equal to this UUID
     */
    public function equals($obj)
    {
        if (!($obj instanceof Uuid)) {
            return false;
        }

        return ($this->compareTo($obj) == 0);
    }

    /**
     * Returns the UUID as a 16-byte string (containing the six integer fields
     * in big-endian byte order)
     *
     * @return string
     */
    public function getBytes()
    {
        return $this->codec->encodeBinary($this);
    }

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     * (bits 65-72 of the UUID).
     *
     * @return int Unsigned 8-bit integer value of clock_seq_hi_and_reserved
     */
    public function getClockSeqHiAndReserved()
    {
        return hexdec($this->getClockSeqHiAndReservedHex());
    }

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     * (bits 65-72 of the UUID).
     *
     * @return string Hexadecimal value of clock_seq_hi_and_reserved
     */
    public function getClockSeqHiAndReservedHex()
    {
        return $this->fields['clock_seq_hi_and_reserved'];
    }

    /**
     * Returns the low field of the clock sequence (bits 73-80 of the UUID).
     *
     * @return int Unsigned 8-bit integer value of clock_seq_low
     */
    public function getClockSeqLow()
    {
        return hexdec($this->getClockSeqLowHex());
    }

    /**
     * Returns the low field of the clock sequence (bits 73-80 of the UUID).
     *
     * @return string Hexadecimal value of clock_seq_low
     */
    public function getClockSeqLowHex()
    {
        return $this->fields['clock_seq_low'];
    }

    /**
     * Returns the clock sequence value associated with this UUID.
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
     * @return int Unsigned 14-bit integer value of clock sequence
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.5
     */
    public function getClockSequence()
    {
        return (($this->getClockSeqHiAndReserved() & 0x3f) << 8)
            | $this->getClockSeqLow();
    }

    /**
     * Returns the clock sequence value associated with this UUID.
     *
     * @return string Hexadecimal value of clock sequence
     */
    public function getClockSequenceHex()
    {
        return sprintf('%04x', $this->getClockSequence());
    }

    public function getNumberConverter()
    {
        return $this->converter;
    }

    /**
     * Returns a PHP DateTime object representing the timestamp associated
     * with this UUID.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1. If this UUID is not a time-based UUID then
     * this method throws UnsupportedOperationException.
     *
     * @return \DateTime A PHP DateTime representation of the date
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system and
     *         Moontoast\Math\BigNumber is not present
     */
    public function getDateTime()
    {
        if ($this->getVersion() != 1) {
            throw new Exception\UnsupportedOperationException('Not a time-based UUID');
        }

        $unixTime = ($this->getTimestamp() - 0x01b21dd213814000) / 1e7;
        $unixTime = number_format($unixTime, 0, '', '');

        return new \DateTime("@{$unixTime}");
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
     * @return array The UUID fields represented as integer values
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.2
     */
    public function getFields()
    {
        return array(
            'time_low' => $this->getTimeLow(),
            'time_mid' => $this->getTimeMid(),
            'time_hi_and_version' => $this->getTimeHiAndVersion(),
            'clock_seq_hi_and_reserved' => $this->getClockSeqHiAndReserved(),
            'clock_seq_low' => $this->getClockSeqLow(),
            'node' => $this->getNode(),
        );
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
     * @return array The UUID fields represented as hexadecimal values
     */
    public function getFieldsHex()
    {
        return $this->fields;
    }

    /**
     * Returns the hexadecimal value of the UUID
     *
     * @return string
     */
    public function getHex()
    {
        return str_replace('-', '', $this->toString());
    }

    /**
     * Returns the integer value of the UUID, represented as a BigNumber
     *
     * @return \Moontoast\Math\BigNumber BigNumber representation of the unsigned 128-bit integer value
     * @throws Exception\UnsatisfiedDependencyException if Moontoast\Math\BigNumber is not present
     */
    public function getInteger()
    {
        return $this->converter->fromHex($this->getHex());
    }

    /**
     * Returns the least significant 64 bits of this UUID's 128 bit value
     *
     * @return \Moontoast\Math\BigNumber BigNumber representation of the unsigned 64-bit integer value
     * @throws Exception\UnsatisfiedDependencyException if Moontoast\Math\BigNumber is not present
     */
    public function getLeastSignificantBits()
    {
        return $this->converter->fromHex($this->getLeastSignificantBitsHex());
    }

    /**
     * Returns the least significant 64 bits of this UUID's 128 bit value
     *
     * @return string Hexadecimal value of least significant bits
     */
    public function getLeastSignificantBitsHex()
    {
        return sprintf(
            '%02s%02s%012s',
            $this->fields['clock_seq_hi_and_reserved'],
            $this->fields['clock_seq_low'],
            $this->fields['node']
        );
    }

    /**
     * Returns the most significant 64 bits of this UUID's 128 bit value
     *
     * @return \Moontoast\Math\BigNumber BigNumber representation of the unsigned 64-bit integer value
     * @throws Exception\UnsatisfiedDependencyException if Moontoast\Math\BigNumber is not present
     */
    public function getMostSignificantBits()
    {
        return $this->converter->fromHex($this->getMostSignificantBitsHex());
    }

    /**
     * Returns the most significant 64 bits of this UUID's 128 bit value
     *
     * @return string Hexadecimal value of most significant bits
     */
    public function getMostSignificantBitsHex()
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
     * @return int Unsigned 48-bit integer value of node
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.6
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getNode()
    {
        return hexdec($this->getNodeHex());
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
     * @return string Hexadecimal value of node
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.6
     */
    public function getNodeHex()
    {
        return $this->fields['node'];
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     * number (bits 49-64 of the UUID).
     *
     * @return int Unsigned 16-bit integer value of time_hi_and_version
     */
    public function getTimeHiAndVersion()
    {
        return hexdec($this->getTimeHiAndVersionHex());
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     * number (bits 49-64 of the UUID).
     *
     * @return string Hexadecimal value of time_hi_and_version
     */
    public function getTimeHiAndVersionHex()
    {
        return $this->fields['time_hi_and_version'];
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return int Unsigned 32-bit integer value of time_low
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getTimeLow()
    {
        return hexdec($this->getTimeLowHex());
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return string Hexadecimal value of time_low
     */
    public function getTimeLowHex()
    {
        return $this->fields['time_low'];
    }

    /**
     * Returns the middle field of the timestamp (bits 33-48 of the UUID).
     *
     * @return int Unsigned 16-bit integer value of time_mid
     */
    public function getTimeMid()
    {
        return hexdec($this->getTimeMidHex());
    }

    /**
     * Returns the middle field of the timestamp (bits 33-48 of the UUID).
     *
     * @return string Hexadecimal value of time_mid
     */
    public function getTimeMidHex()
    {
        return $this->fields['time_mid'];
    }

    /**
     * The timestamp value associated with this UUID
     *
     * The 60 bit timestamp value is constructed from the time_low,
     * time_mid, and time_hi fields of this UUID. The resulting
     * timestamp is measured in 100-nanosecond units since midnight,
     * October 15, 1582 UTC.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1. If this UUID is not a time-based UUID then
     * this method throws UnsupportedOperationException.
     *
     * @return int Unsigned 60-bit integer value of the timestamp
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    public function getTimestamp()
    {
        if ($this->getVersion() != 1) {
            throw new Exception\UnsupportedOperationException('Not a time-based UUID');
        }

        return hexdec($this->getTimestampHex());
    }

    /**
     * The timestamp value associated with this UUID
     *
     * The 60 bit timestamp value is constructed from the time_low,
     * time_mid, and time_hi fields of this UUID. The resulting
     * timestamp is measured in 100-nanosecond units since midnight,
     * October 15, 1582 UTC.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1. If this UUID is not a time-based UUID then
     * this method throws UnsupportedOperationException.
     *
     * @return string Hexadecimal value of the timestamp
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    public function getTimestampHex()
    {
        if ($this->getVersion() != 1) {
            throw new Exception\UnsupportedOperationException('Not a time-based UUID');
        }

        return sprintf(
            '%03x%04s%08s',
            ($this->getTimeHiAndVersion() & 0x0fff),
            $this->fields['time_mid'],
            $this->fields['time_low']
        );
    }

    /**
     * Returns the string representation of the UUID as a URN.
     *
     * @return string
     * @link http://en.wikipedia.org/wiki/Uniform_Resource_Name
     */
    public function getUrn()
    {
        return 'urn:uuid:' . $this->toString();
    }

    /**
     * Returns the variant number associated with this UUID.
     *
     * The variant number describes the layout of the UUID. The variant
     * number has the following meaning:
     *
     * * 0 - Reserved for NCS backward compatibility
     * * 2 - The RFC 4122 variant (used by this class)
     * * 6 - Reserved, Microsoft Corporation backward compatibility
     * * 7 - Reserved for future definition
     *
     * @return int
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    public function getVariant()
    {
        $clockSeq = $this->getClockSeqHiAndReserved();
        if (0 === ($clockSeq & 0x80)) {
            $variant = self::RESERVED_NCS;
        } elseif (0 === ($clockSeq & 0x40)) {
            $variant = self::RFC_4122;
        } elseif (0 === ($clockSeq & 0x20)) {
            $variant = self::RESERVED_MICROSOFT;
        } else {
            $variant = self::RESERVED_FUTURE;
        }

        return $variant;
    }

    /**
     * The version number associated with this UUID. The version
     * number describes how this UUID was generated.
     *
     * The version number has the following meaning:
     *
     * * 1 - Time-based UUID
     * * 2 - DCE security UUID
     * * 3 - Name-based UUID hashed with MD5
     * * 4 - Randomly generated UUID
     * * 5 - Name-based UUID hashed with SHA-1
     *
     * Returns null if this UUID is not an RFC 4122 variant, since version
     * is only meaningful for this variant.
     *
     * @return int|null
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.3
     */
    public function getVersion()
    {
        if ($this->getVariant() == self::RFC_4122) {
            return (int) (($this->getTimeHiAndVersion() >> 12) & 0x0f);
        }

        return null;
    }

    /**
     * Converts this UUID into a string representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->codec->encode($this);
    }

    public static function getFactory()
    {
        if (! self::$factory) {
            self::$factory = new PeclUuidFactory(new UuidFactory());
        }

        return self::$factory;
    }

    public static function setFactory(UuidFactoryInterface $factory)
    {
        self::$factory = $factory;
    }

    /**
     * Creates a UUID from a byte string.
     *
     * @param string $bytes
     * @return Uuid
     * @throws InvalidArgumentException If the $bytes string does not contain 16 characters
     */
    public static function fromBytes($bytes)
    {
        return self::getFactory()->fromBytes($bytes);
    }

    /**
     * Creates a UUID from the string standard representation as described
     * in the toString() method.
     *
     * @param string $name A string that specifies a UUID
     * @return Uuid
     * @throws InvalidArgumentException If the $name isn't a valid UUID
     */
    public static function fromString($name)
    {
        return self::getFactory()->fromString($name);
    }

    /**
     * Creates a UUID from either the UUID as a 128-bit integer string or a Moontoast\Math\BigNumber object.
     *
     * @param string|\Moontoast\Math\BigNumber $integer String/BigNumber representation of UUID integer
     * @throws Exception\UnsatisfiedDependencyException If Moontoast\Math\BigNumber is not present
     * @return \Ramsey\Uuid\Uuid
     */
    public static function fromInteger($integer)
    {
        return self::getFactory()->fromInteger($integer);
    }

    /**
     * Check if a string is a valid uuid
     *
     * @param string $uuid The uuid to test
     * @return boolean
     */
    public static function isValid($uuid)
    {
        $uuid = str_replace(array('urn:', 'uuid:', '{', '}'), '', $uuid);

        if ($uuid == self::NIL) {
            return true;
        }

        if (!preg_match('/' . self::VALID_PATTERN . '/', $uuid)) {
            return false;
        }

        return true;
    }

    /**
     * Generate a version 1 UUID from a host ID, sequence number, and the current time.
     * If $node is not given, we will attempt to obtain the local hardware
     * address. If $clockSeq is given, it is used as the sequence number;
     * otherwise a random 14-bit sequence number is chosen.
     *
     * @param int|string $node A 48-bit number representing the hardware
     *                         address. This number may be represented as
     *                         an integer or a hexadecimal string.
     * @param int $clockSeq A 14-bit number used to help avoid duplicates that
     *                      could arise when the clock is set backwards in time
     *                      or if the node ID changes.
     * @return Uuid
     * @throws InvalidArgumentException if the $node is invalid
     */
    public static function uuid1($node = null, $clockSeq = null)
    {
        return self::getFactory()->uuid1($node, $clockSeq);
    }

    /**
     * Generate a version 3 UUID based on the MD5 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public static function uuid3($ns, $name)
    {
        return self::getFactory()->uuid3($ns, $name);
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return Uuid
     */
    public static function uuid4()
    {
        return self::getFactory()->uuid4();
    }

    /**
     * Generate a version 5 UUID based on the SHA-1 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public static function uuid5($ns, $name)
    {
        return self::getFactory()->uuid5($ns, $name);
    }
}
