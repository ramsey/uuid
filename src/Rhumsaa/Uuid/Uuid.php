<?php
/**
 * This file is part of the Rhumsaa\Uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2012 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Rhumsaa\Uuid;

/**
 * Represents a universally unique identifier (UUID)
 *
 * @see http://tools.ietf.org/html/rfc4122
 * @see http://en.wikipedia.org/wiki/Universally_unique_identifier
 */
class Uuid
{
    /**
     * When this namespace is specified, the name string is a fully-qualified domain name.
     * @see http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is a URL.
     * @see http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an ISO OID.
     * @see http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
     * @see http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.7
     */
    const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * Reserved for NCS compatibility.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_NCS = 0;

    /**
     * Specifies the UUID layout given in RFC 4122.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RFC_4122 = 2;

    /**
     * Reserved for Microsoft compatibility.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_MICROSOFT = 6;

    /**
     * Reserved for future definition.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    const RESERVED_FUTURE = 7;

    /**
     * 64-bit system override; if true, treat the system as 32-bit (for testing)
     *
     * @var bool
     */
    public static $force32Bit = false;

    /**
     * Moontoast\Math\BigNumber override; if true, treat as if BigNumber is
     * not available (for testing)
     *
     * @var bool
     */
    public static $forceNoBigNumber = false;

    /**
     * Sets time of day to a static, known value (for testing)
     *
     * @var array
     */
    public static $timeOfDayTest;

    /**
     * System override to ignore generating node from hardware (for testing)
     *
     * @var bool
     */
    public static $ignoreSystemNode = false;

    /**
     * The fields that make up this UUID
     *
     * This is initialized to the nil value.
     *
     * @var array
     * @see Rhumsaa\Uuid\Uuid::getFields
     */
    protected $fields = array(
        'time_low' => '00000000',
        'time_mid' => '0000',
        'time_hi_and_version' => '0000',
        'clock_seq_hi_and_reserved' => '00',
        'clock_seq_low' => '00',
        'node' => '000000000000',
    );

    /**
     * Creates a universally unique identifier (UUID) from the most significant
     * bits and least significant bits.
     *
     * Protected to prevent direct instantiation. Use static methods to create
     * UUIDs.
     *
     * @param array $fields
     */
    protected function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Converts this UUID object to a string when the object is used in any
     * string context
     *
     * @return string
     * @see http://www.php.net/manual/en/language.oop5.magic.php#object.tostring
     */
    public function __toString()
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
     * @param Uuid $uuid UUID to which this UUID is to be compared
     * @return int -1, 0 or 1 as this UUID is less than, equal to, or greater than $uuid
     */
    public function compareTo(Uuid $uuid)
    {
        $comparison = null;

        if ($this->getMostSignificantBitsHex() < $uuid->getMostSignificantBitsHex()) {
            $comparison = -1;
        } elseif ($this->getMostSignificantBitsHex() > $uuid->getMostSignificantBitsHex()) {
            $comparison = 1;
        } elseif ($this->getLeastSignificantBitsHex() < $uuid->getLeastSignificantBitsHex()) {
            $comparison = -1;
        } elseif ($this->getLeastSignificantBitsHex() > $uuid->getLeastSignificantBitsHex()) {
            $comparison = 1;
        } else {
            $comparison = 0;
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
     * @return bool
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
        $bytes = '';

        $hex = $this->toString();
        $hex = str_replace('-', '', $hex);

        foreach (range(-2, -32, 2) as $step) {
            $bytes = chr(hexdec(substr($hex, $step, 2))) . $bytes;
        }

        return $bytes;
    }

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     * (bits 65-72 of the UUID).
     *
     * @return int
     */
    public function getClockSeqHiAndReserved()
    {
        return hexdec($this->getClockSeqHiAndReservedHex());
    }

    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     * (bits 65-72 of the UUID).
     *
     * @return string
     */
    public function getClockSeqHiAndReservedHex()
    {
        return $this->fields['clock_seq_hi_and_reserved'];
    }

    /**
     * Returns the low field of the clock sequence (bits 73-80 of the UUID).
     *
     * @return int
     */
    public function getClockSeqLow()
    {
        return hexdec($this->getClockSeqLowHex());
    }

    /**
     * Returns the low field of the clock sequence (bits 73-80 of the UUID).
     *
     * @return string
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
     * @return int
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.5
     */
    public function getClockSequence()
    {
        return (($this->getClockSeqHiAndReserved() & 0x3f) << 8)
            | $this->getClockSeqLow();
    }

    /**
     * Returns the clock sequence value associated with this UUID.
     *
     * @return string
     */
    public function getClockSequenceHex()
    {
        return sprintf('%04x', $this->getClockSequence());
    }

    /**
     * Returns a PHP DateTime object representing the timestamp associated
     * with this UUID.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1. If this UUID is not a time-based UUID then
     * this method throws UnsupportedOperationException.
     *
     * @return \DateTime
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system and Moontoast\Math\BigNumber is not present
     */
    public function getDateTime()
    {
        if ($this->getVersion() != 1) {
            throw new Exception\UnsupportedOperationException('Not a time-based UUID');
        }


        if (self::is64BitSystem()) {

            $unixTime = ($this->getTimestamp() - 0x01b21dd213814000) / 1e7;
            $unixTime = number_format($unixTime, 0, '', '');

        } elseif (self::hasBigNumber()) {

            $time = \Moontoast\Math\BigNumber::baseConvert($this->getTimestampHex(), 16, 10);

            $ts = new \Moontoast\Math\BigNumber($time, 20);
            $ts->subtract('122192928000000000');
            $ts->divide('10000000.0');
            $ts->round();
            $unixTime = $ts->getValue();

        } else {

            throw new Exception\UnsatisfiedDependencyException(
                'When calling ' . __METHOD__ . ' on a 32-bit system, '
                . 'Moontoast\Math\BigNumber must be present in order '
                . 'to extract DateTime from version 1 UUIDs'
            );

        }

        return new \DateTime("@{$unixTime}");
    }

    /**
     * Returns an array of the fields of this UUID, with keys named according
     * to the RFC 4122 names for the fields.
     *
     * @return array
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getFields()
    {
        if (!self::is64BitSystem()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' on a 32-bit system, since some '
                . 'values overflow the system max integer value'
            );
        }

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
     * @return array
     */
    public function getFieldsHex()
    {
        return $this->fields;
    }

    /**
     * Returns the least significant 64 bits of this UUID's 128 bit value
     *
     * @return \Moontoast\Math\BigNumber
     * @throws Exception\UnsatisfiedDependencyException if Moontoast\Math\BigNumber is not present
     */
    public function getLeastSignificantBits()
    {
        if (!self::hasBigNumber()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' without support for large '
                . 'integers, since least significant bits is an unsigned '
                . '64-bit integer; Moontoast\Math\BigNumber is required'
            );
        }

        $number = \Moontoast\Math\BigNumber::baseConvert(
            $this->getLeastSignificantBitsHex(),
            16,
            10
        );

        return new \Moontoast\Math\BigNumber($number);
    }

    /**
     * Returns the least significant 64 bits of this UUID's 128 bit value
     *
     * @return string
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
     * @return \Moontoast\Math\BigNumber
     * @throws Exception\UnsatisfiedDependencyException if Moontoast\Math\BigNumber is not present
     */
    public function getMostSignificantBits()
    {
        if (!self::hasBigNumber()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' without support for large '
                . 'integers, since most significant bits is an unsigned '
                . '64-bit integer; Moontoast\Math\BigNumber is required'
            );
        }

        $number = \Moontoast\Math\BigNumber::baseConvert(
            $this->getMostSignificantBitsHex(),
            16,
            10
        );

        return new \Moontoast\Math\BigNumber($number);
    }

    /**
     * Returns the most significant 64 bits of this UUID's 128 bit value
     *
     * @return string
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
     * @return int
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.6
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getNode()
    {
        if (!self::is64BitSystem()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' on a 32-bit system, since node '
                . 'is an unsigned 48-bit integer and can overflow the system '
                . 'max integer value'
            );
        }

        return hexdec($this->getNodeHex());
    }

    /**
     * Returns the node value associated with this UUID
     *
     * @return string
     */
    public function getNodeHex()
    {
        return $this->fields['node'];
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     * number (bits 49-64 of the UUID).
     *
     * @return int
     */
    public function getTimeHiAndVersion()
    {
        return hexdec($this->getTimeHiAndVersionHex());
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     * number (bits 49-64 of the UUID).
     *
     * @return string
     */
    public function getTimeHiAndVersionHex()
    {
        return $this->fields['time_hi_and_version'];
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return int
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getTimeLow()
    {
        if (!self::is64BitSystem()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' on a 32-bit system, since time_low '
                . 'is an unsigned 32-bit integer and can overflow the system '
                . 'max integer value'
            );
        }

        return hexdec($this->getTimeLowHex());
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return string
     */
    public function getTimeLowHex()
    {
        return $this->fields['time_low'];
    }

    /**
     * Returns the middle field of the timestamp (bits 33-48 of the UUID).
     *
     * @return int
     */
    public function getTimeMid()
    {
        return hexdec($this->getTimeMidHex());
    }

    /**
     * Returns the middle field of the timestamp (bits 33-48 of the UUID).
     *
     * @return string
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
     * @return int
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    public function getTimestamp()
    {
        if ($this->getVersion() != 1) {
            throw new Exception\UnsupportedOperationException('Not a time-based UUID');
        }

        if (!self::is64BitSystem()) {
            throw new Exception\UnsatisfiedDependencyException(
                'Cannot call ' . __METHOD__ . ' on a 32-bit system, since timestamp '
                . 'is an unsigned 60-bit integer and can overflow the system '
                . 'max integer value'
            );
        }

        return hexdec($this->getTimestampHex());
    }

    /**
     * The timestamp value associated with this UUID
     *
     * @return string
     * @throws Exception\UnsupportedOperationException If this UUID is not a version 1 UUID
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
     * * 2 - The RFC 4122 variant (used by this class
     * * 6 - Reserved, Microsoft Corporation backward compatibility
     * * 7 - Reserved for future definition
     *
     * @return int
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    public function getVariant()
    {
        if (0 === ($this->getClockSeqHiAndReserved() & 0x80)) {
            $variant = self::RESERVED_NCS;
        } elseif (0 === ($this->getClockSeqHiAndReserved() & 0x40)) {
            $variant = self::RFC_4122;
        } elseif (0 === ($this->getClockSeqHiAndReserved() & 0x20)) {
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
     * @return int
     */
    public function getVersion()
    {
        return (($this->getTimeHiAndVersion() >> 12) & 0x0f);
    }

    /**
     * Converts this UUID into a string representation
     *
     * @return string
     */
    public function toString()
    {
        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $this->fields
        );
    }

    /**
     * Creates a UUID from the string standard representation as described
     * in the toString() method.
     *
     * @param string $name A string that specifies a UUID
     * @return Uuid
     * @throws \InvalidArgumentException If the $name isn't a valid UUID
     */
    public static function fromString($name)
    {
        $name = str_replace(array('urn:', 'uuid:', '{', '}'), '', $name);
        $components = explode('-', $name);

        if (count($components) != 5) {
            throw new \InvalidArgumentException('Invalid UUID string: ' . $name);
        }

        $fields = array(
            'time_low' => sprintf('%08s', $components[0]),
            'time_mid' => sprintf('%04s', $components[1]),
            'time_hi_and_version' => sprintf('%04s', $components[2]),
            'clock_seq_hi_and_reserved' => sprintf('%02s', substr($components[3], 0, 2)),
            'clock_seq_low' => sprintf('%02s', substr($components[3], 2)),
            'node' => sprintf('%012s', $components[4]),
        );

        return new self($fields);
    }

    /**
     * Generate a UUID from a host ID, sequence number, and the current time.
     * If $node is not given, getMacAddress() is used to obtain the hardware
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
     * @throws \InvalidArgumentException if the $node is invalid
     */
    public static function uuid1($node = null, $clockSeq = null)
    {
        if ($node === null && !self::$ignoreSystemNode) {
            $node = self::getNodeFromSystem();
        }

        // if $node is still null (couldn't get from system), randomly generate
        // a node value, according to RFC 4122, Section 4.5
        if ($node === null) {
            $node = sprintf('%06x%06x', mt_rand(0, 1 << 24), mt_rand(0, 1 << 24));
        }

        // Convert the node to hex, if it is still an integer
        if (is_int($node)) {
            $node = sprintf('%012x', $node);
        }

        if (ctype_xdigit($node) && strlen($node) <= 12) {
            $node = sprintf('%012s', $node);
        } else {
            throw new \InvalidArgumentException('Invalid node value');
        }

        if ($clockSeq === null) {
            // Not using "stable storage"; see RFC 4122, Section 4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }

        // Create a 60-bit time value as a count of 100-nanosecond intervals
        // since 00:00:00.00, 15 October 1582
        if (self::$timeOfDayTest === null) {
            $timeOfDay = gettimeofday();
        } else {
            $timeOfDay = self::$timeOfDayTest;
        }
        $uuidTime = self::calculateUuidTime($timeOfDay['sec'], $timeOfDay['usec']);

        // Set the version number to 1
        $timeHi = hexdec($uuidTime['hi']) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 1 << 12;

        // Set the variant to RFC 4122
        $clockSeqHi = ($clockSeq >> 8) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $fields = array(
            'time_low' => $uuidTime['low'],
            'time_mid' => $uuidTime['mid'],
            'time_hi_and_version' => sprintf('%04x', $timeHi),
            'clock_seq_hi_and_reserved' => sprintf('%02x', $clockSeqHi),
            'clock_seq_low' => sprintf('%02x', $clockSeq & 0xff),
            'node' => $node,
        );

        return new self($fields);
    }

    /**
     * Generate a UUID based on the MD5 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param Uuid|string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public static function uuid3($ns, $name)
    {
        if (!($ns instanceof Uuid)) {
            $ns = self::fromString($ns);
        }

        $hash = md5($ns->getBytes() . $name);

        return self::uuidFromHashedName($hash, 3);
    }

    /**
     * Generate a random UUID.
     *
     * @return Uuid
     */
    public static function uuid4()
    {
        // Generate a random 16-byte binary string
        $bytes = '';
        foreach (range(1, 16) as $i) {
            $bytes = chr(mt_rand(0, 256)) . $bytes;
        }

        // When converting the bytes to hex, it turns into a 32-character
        // hexadecimal string that looks a lot like an MD5 hash, so at this
        // point, we can just pass it to uuidFromHashedName.
        $hex = bin2hex($bytes);
        return self::uuidFromHashedName($hex, 4);
    }

    /**
     * Generate a UUID based on the SHA-1 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param Uuid|string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public static function uuid5($ns, $name)
    {
        if (!($ns instanceof Uuid)) {
            $ns = self::fromString($ns);
        }

        $hash = sha1($ns->getBytes() . $name);

        return self::uuidFromHashedName($hash, 5);
    }

    /**
     * Calculates the UUID time fields from a UNIX timestamp
     *
     * UUID time is a 60-bit time value as a count of 100-nanosecond intervals
     * since 00:00:00.00, 15 October 1582.
     *
     * @return array
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system and Moontoast\Math\BigNumber is not present
     */
    protected static function calculateUuidTime($sec, $usec)
    {
        if (self::is64BitSystem()) {

            $uuidTime = ($sec * 10000000) + ($usec * 10) + 0x01b21dd213814000;

            return array(
                'low' => sprintf('%08x', $uuidTime & 0xffffffff),
                'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
                'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
            );
        }

        if (self::hasBigNumber()) {

            $uuidTime = new \Moontoast\Math\BigNumber('0');

            $sec = new \Moontoast\Math\BigNumber($sec);
            $sec->multiply('10000000');

            $usec = new \Moontoast\Math\BigNumber($usec);
            $usec->multiply('10');

            $uuidTime->add($sec)
                ->add($usec)
                ->add('122192928000000000');

            $uuidTimeHex = sprintf('%016s', $uuidTime->convertToBase(16));

            return array(
                'low' => substr($uuidTimeHex, 8),
                'mid' => substr($uuidTimeHex, 4, 4),
                'hi' => substr($uuidTimeHex, 0, 4),
            );
        }

        throw new Exception\UnsatisfiedDependencyException(
            'When calling ' . __METHOD__ . ' on a 32-bit system, '
            . 'Moontoast\Math\BigNumber must be present in order '
            . 'to generate version 1 UUIDs'
        );
    }

    /**
     * Get the hardware address as a 48-bit positive integer. If all attempts to
     * obtain the hardware address fail, we choose a random 48-bit number with
     * its eighth bit set to 1 as recommended in RFC 4122. "Hardware address"
     * means the MAC address of a network interface, and on a machine with
     * multiple network interfaces the MAC address of any one of them may be
     * returned.
     *
     * @return string
     * @todo Needs evaluation and possibly modification to ensure this works
     *       well across multiple platforms.
     */
    protected static function getNodeFromSystem()
    {
        // If we're on Windows, use ipconfig; otherwise use ifconfig
        if (strtoupper(substr(php_uname('a'), 0, 3)) == 'WIN') {
            $ifconfig = `ipconfig /all 2>&1`;
        } else {
            $ifconfig = `ifconfig 2>&1`;
        }

        $node = null;
        $pattern = '/[^:]([0-9A-Fa-f]{2}([:-])[0-9A-Fa-f]{2}(\2[0-9A-Fa-f]{2}){4})[^:]/';
        $matches = array();

        // Search the ifconfig output for all MAC addresses and return
        // the first one found
        if (preg_match_all($pattern, $ifconfig, $matches, PREG_PATTERN_ORDER)) {
            $node = $matches[1][0];
            $node = str_replace(':', '', $node);
            $node = str_replace('-', '', $node);
        }

        return $node;
    }

    /**
     * Returns true if the system has Moontoast\Math\BigNumber
     *
     * @return bool
     */
    protected static function hasBigNumber()
    {
        return (class_exists('Moontoast\Math\BigNumber') && !self::$forceNoBigNumber);
    }

    /**
     * Returns true if the system is 64-bit, false otherwise
     *
     * @return bool
     */
    protected static function is64BitSystem()
    {
        return (PHP_INT_SIZE == 8 && !self::$force32Bit);
    }

    /**
     * Returns a version 3 or 5 UUID based on the hash (md5 or sha1) of a
     * namespace identifier (which is a UUID) and a name (which is a string)
     *
     * @param string $hash
     * @return Uuid
     */
    protected static function uuidFromHashedName($hash, $version)
    {
        // Set the version number
        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= $version << 12;

        // Set the variant to RFC 4122
        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $fields = array(
            'time_low' => substr($hash, 0, 8),
            'time_mid' => substr($hash, 8, 4),
            'time_hi_and_version' => sprintf('%04x', $timeHi),
            'clock_seq_hi_and_reserved' => sprintf('%02x', $clockSeqHi),
            'clock_seq_low' => substr($hash, 18, 2),
            'node' => substr($hash, 20, 12),
        );

        return new self($fields);
    }
}
