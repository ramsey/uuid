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
final class Uuid
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
     * The least significant 64 bits of this UUID's 128 bit value
     * @var int
     */
    protected $lsb = 0;

    /**
     * The most significant 64 bits of this UUID's 128 bit value
     * @var int
     */
    protected $msb = 0;

    /**
     * Creates a universally unique identifier (UUID) from the most significant
     * bits and least significant bits.
     *
     * Protected to prevent direct instantiation. Use static methods to create
     * UUIDs.
     *
     * @param int $msb The most significant 64 bits of this UUID's 128 bit value
     * @param int $lsb The least significant 64 bits of this UUID's 128 bit value
     */
    protected function __construct($msb, $lsb)
    {
        if (PHP_INT_SIZE == 4) {
            throw new \OverflowException(
                'Attempting to create a UUID on a 32-bit build of PHP. This '
                . 'library requires a 64-bit build of PHP.'
            );
        }

        $this->msb = $msb;
        $this->lsb = $lsb;
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

        if ($this->getMostSignificantBits() < $uuid->getMostSignificantBits()) {
            $comparison = -1;
        } elseif ($this->getMostSignificantBits() > $uuid->getMostSignificantBits()) {
            $comparison = 1;
        } elseif ($this->getLeastSignificantBits() < $uuid->getLeastSignificantBits()) {
            $comparison = -1;
        } elseif ($this->getLeastSignificantBits() > $uuid->getLeastSignificantBits()) {
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
     * @returns bool
     */
    public function equals($obj)
    {
        if (!($obj instanceof Uuid)) {
            return false;
        }

        return ($this->getMostSignificantBits() == $obj->getMostSignificantBits()
            && $this->getLeastSignificantBits() == $obj->getLeastSignificantBits());
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
        foreach (range(0, 63, 8) as $shift) {
            $bytes = chr(($this->getLeastSignificantBits() >> $shift) & 0xff) . $bytes;
        }
        foreach (range(64, 127, 8) as $shift) {
            $bytes = chr(($this->getMostSignificantBits() >> $shift) & 0xff) . $bytes;
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
        return ($this->getLeastSignificantBits() >> 56) & 0xff;
    }

    /**
     * Returns the low field of the clock sequence (bits 73-80 of the UUID).
     *
     * @return int
     */
    public function getClockSeqLow()
    {
        return ($this->getLeastSignificantBits() >> 48) & 0xff;
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
     * Returns a PHP DateTime object representing the timestamp associated
     * with this UUID.
     *
     * The timestamp value is only meaningful in a time-based UUID, which
     * has version type 1. If this UUID is not a time-based UUID then
     * this method throws UnsupportedOperationException.
     *
     * @return \DateTime
     * @throws UnsupportedOperationException If this UUID is not a version 1 UUID
     */
    public function getDateTime()
    {
        if ($this->getVersion() != 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        $unixTime = ($this->getTimestamp() - 0x01b21dd213814000) / 1e7;
        return new \DateTime('@' . number_format($unixTime, 0, '', ''));
    }

    /**
     * Returns an array of the fields of this UUID, with keys named according
     * to the RFC 4122 names for the fields.
     *
     * @return array
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
     * Returns the least significant 64 bits of this UUID's 128 bit value
     *
     * @return int
     */
    public function getLeastSignificantBits()
    {
        return $this->lsb;
    }

    /**
     * Returns the most significant 64 bits of this UUID's 128 bit value
     *
     * @return int
     */
    public function getMostSignificantBits()
    {
        return $this->msb;
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
     */
    public function getNode()
    {
        return $this->getLeastSignificantBits() & 0x0000ffffffffffff;
    }

    /**
     * Returns the high field of the timestamp multiplexed with the version
     * number (bits 49-64 of the UUID).
     *
     * @return int
     */
    public function getTimeHiAndVersion()
    {
        return $this->getMostSignificantBits() & 0xffff;
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return int
     */
    public function getTimeLow()
    {
        return ($this->getMostSignificantBits() >> 32) & 0xffffffff;
    }

    /**
     * Returns the middle field of the timestamp (bits 33-48 of the UUID).
     *
     * @return int
     */
    public function getTimeMid()
    {
        return ($this->getMostSignificantBits() >> 16) & 0xffff;
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
     * @throws UnsupportedOperationException If this UUID is not a version 1 UUID
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    public function getTimestamp()
    {
        if ($this->getVersion() != 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return ($this->getTimeHiAndVersion() & 0x0fff) << 48
            | ($this->getTimeMid() & 0xffff) << 32
            | $this->getTimeLow();
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
        if (0 === ($this->getLeastSignificantBits() & (0x8000 << 48))) {
            $variant = self::RESERVED_NCS;
        } elseif (0 === ($this->getLeastSignificantBits() & (0x4000 << 48))) {
            $variant = self::RFC_4122;
        } elseif (0 === ($this->getLeastSignificantBits() & (0x2000 << 48))) {
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
        return (($this->getMostSignificantBits() >> 12) & 0x0f);
    }

    /**
     * Converts this UUID into a string representation
     *
     * @return string
     */
    public function toString()
    {
        $hex = sprintf(
            '%016x%016x',
            $this->getMostSignificantBits(),
            $this->getLeastSignificantBits()
        );

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20)
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

        // Put together most significant bits
        $msb = hexdec($components[0]);
        $msb <<= 16;
        $msb |= hexdec($components[1]);
        $msb <<= 16;
        $msb |= hexdec($components[2]);

        // Put together least significant bits
        $lsb = hexdec($components[3]);
        $lsb <<= 48;
        $lsb |= hexdec($components[4]);

        return new self($msb, $lsb);
    }

    /**
     * Generate a UUID from a host ID, sequence number, and the current time.
     * If $node is not given, getMacAddress() is used to obtain the hardware
     * address. If $clockSeq is given, it is used as the sequence number;
     * otherwise a random 14-bit sequence number is chosen.
     *
     * @param int $node A 48-bit number representing the hardware address.
     * @param int $clockSeq A 14-bit number used to help avoid duplicates that
     *                      could arise when the clock is set backwards in time
     *                      or if the node ID changes.
     * @param bool $useRandomNode For testing purposes only; to force method to
     *                            generate a random node.
     * @return Uuid
     */
    public static function uuid1($node = null, $clockSeq = null, $useRandomNode = false)
    {
        if ($node === null && !$useRandomNode) {
            $node = self::getNodeFromSystem();
        }

        // if $node is still null (couldn't get from system), randomly generate
        // a node value, according to RFC 4122, Section 4.5
        if ($node === null) {
            $node = mt_rand(0, 1 << 48) | 0x010000000000;
        }

        if ($clockSeq === null) {
            // Not using "stable storage"; see RFC 4122, Section 4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }

        // Create a 60-bit time value as a count of 100-nanosecond intervals
        // since 00:00:00.00, 15 October 1582
        $timeOfDay = gettimeofday();
        $uuidTime = ($timeOfDay['sec'] * 10000000)
            + ($timeOfDay['usec'] * 10)
            + 0x01b21dd213814000;

        $timeLow = $uuidTime & 0xffffffff;
        $timeMid = ($uuidTime >> 32) & 0xffff;
        $timeHi = ($uuidTime >> 48) & 0x0fff;
        $clockSeqHi = ($clockSeq >> 8) & 0x3f;
        $clockSeqLow = $clockSeq & 0xff;
        $clockSeq = ($clockSeqHi << 8) | $clockSeqLow;

        $msb = ($timeLow << 32) | ($timeMid << 16) | $timeHi;
        $lsb = ($clockSeq << 48) | $node;

        // Set the version number to 1
        $msb &= ~(0xf000);
        $msb |= 1 << 12;

        // Set the variant to RFC 4122
        $lsb &= ~(0xc000 << 48);
        $lsb |= 0x8000 << 48;

        return new self($msb, $lsb);
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
     * Returns a version 3 or 5 UUID based on the hash (md5 or sha1) of a
     * namespace identifier (which is a UUID) and a name (which is a string)
     *
     * @param string $hash
     * @return Uuid
     */
    protected static function uuidFromHashedName($hash, $version)
    {
        $timeLow = hexdec(substr($hash, 0, 8)) & 0xffffffff;
        $timeMid = hexdec(substr($hash, 8, 4)) & 0xffff;
        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
        $clockSeqLow = hexdec(substr($hash, 18, 2)) & 0xff;
        $clockSeq = ($clockSeqHi << 8) | $clockSeqLow;
        $node = hexdec(substr($hash, 20, 12));

        $msb = ($timeLow << 32) | ($timeMid << 16) | $timeHi;
        $lsb = ($clockSeq << 48) | $node;

        // Set the version number
        $msb &= ~(0xf000);
        $msb |= $version << 12;

        // Set the variant to RFC 4122
        $lsb &= ~(0xc000 << 48);
        $lsb |= 0x8000 << 48;

        return new self($msb, $lsb);
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
            $node = hexdec($node);
        }

        return $node;
    }
}
