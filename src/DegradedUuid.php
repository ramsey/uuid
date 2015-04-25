<?php

namespace Ramsey\Uuid;

use Ramsey\Uuid\Converter\NumberConverterInterface;

class DegradedUuid extends Uuid
{

    public function __construct(array $fields, NumberConverterInterface $converter, CodecInterface $codec)
    {
        parent::__construct($fields, $converter, $codec);
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

        $time = $this->converter->fromHex($this->getTimestampHex());

        $ts = new \Moontoast\Math\BigNumber($time, 20);
        $ts->subtract('122192928000000000');
        $ts->divide('10000000.0');
        $ts->round();
        $unixTime = $ts->getValue();

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
        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since some '
            . 'values overflow the system max integer value'
            . '; consider calling getFieldsHex instead'
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
        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since node '
            . 'is an unsigned 48-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getNodeHex instead'
        );
    }

    /**
     * Returns the low field of the timestamp (the first 32 bits of the UUID).
     *
     * @return int Unsigned 32-bit integer value of time_low
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system
     */
    public function getTimeLow()
    {
        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since time_low '
            . 'is an unsigned 32-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getTimeLowHex instead'
        );
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

        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since timestamp '
            . 'is an unsigned 60-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getTimestampHex instead'
        );
    }
}
