<?php

namespace Ramsey\Uuid;

use Assert\Assertion;

/**
 * @see https://www.ietf.org/rfc/rfc4122.txt
 */
class UuidFields
{
    /**
     * The low field of the timestamp
     *
     * @var string
     */
    private $time_low;

    /**
     * The middle field of the timestamp
     *
     * @var string
     */
    private $time_mid;

    /**
     * The high field of the timestamp multiplexed with the version number
     *
     * @var string
     */
    private $time_hi_and_version;

    /**
     * The high field of the clock sequence multiplexed with the variant
     *
     * @var string
     */
    private $clock_seq_hi_and_reserved;

    /**
     * The low field of the clock sequence
     *
     * @var string
     */
    private $clock_seq_low;

    /**
     * The spatially unique node identifier
     *
     * @var string
     */
    private $node;

    /**
     * @param string $time_low
     * @param string $time_mid
     * @param string $time_hi_and_version
     * @param string $clock_seq_hi_and_reserved
     * @param string $clock_seq_low
     * @param string $node
     */
    public function __construct(
        $time_low,
        $time_mid,
        $time_hi_and_version,
        $clock_seq_hi_and_reserved,
        $clock_seq_low,
        $node
    ) {
        Assertion::string($time_low);
        Assertion::string($time_mid);
        Assertion::string($time_hi_and_version);
        Assertion::string($clock_seq_hi_and_reserved);
        Assertion::string($clock_seq_low);
        Assertion::string($node);

        Assertion::length($time_low, 8);
        Assertion::length($time_mid, 4);
        Assertion::length($time_hi_and_version, 4);
        Assertion::length($clock_seq_hi_and_reserved, 2);
        Assertion::length($clock_seq_low, 2);
        Assertion::length($node, 12);

        $this->time_low = $time_low;
        $this->time_mid = $time_mid;
        $this->time_hi_and_version = $time_hi_and_version;
        $this->clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved;
        $this->clock_seq_low = $clock_seq_low;
        $this->node = $node;
    }

    /**
     * @return string
     */
    public function getTimeLow()
    {
        return $this->time_low;
    }

    /**
     * @return string
     */
    public function getTimeMid()
    {
        return $this->time_mid;
    }

    /**
     * @return string
     */
    public function getTimeHiAndVersion()
    {
        return $this->time_hi_and_version;
    }

    /**
     * @return string
     */
    public function getClockSeqHiAndReserved()
    {
        return $this->clock_seq_hi_and_reserved;
    }

    /**
     * @return string
     */
    public function getClockSeqLow()
    {
        return $this->clock_seq_low;
    }

    /**
     * @return string
     */
    public function getNode()
    {
        return $this->node;
    }
}
