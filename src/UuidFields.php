<?php

namespace Ramsey\Uuid;

use Assert\Assertion;

/**
 * @see https://www.ietf.org/rfc/rfc4122.txt
 */
class UuidFields
{
    const HEXADECIMAL_REGEX = '(^[0-9a-fA-F]+$)';

    /**
     * The low field of the timestamp (time_low)
     *
     * @var string
     */
    private $timeLow;

    /**
     * The middle field of the timestamp (time_mid)
     *
     * @var string
     */
    private $timeMid;

    /**
     * The high field of the timestamp multiplexed with the version number (time_hi_and_version)
     *
     * @var string
     */
    private $timeHiAndVersion;

    /**
     * The high field of the clock sequence multiplexed with the variant (clock_seq_hi_and_reserved)
     *
     * @var string
     */
    private $clockSeqHiAndReserved;

    /**
     * The low field of the clock sequence (clock_seq_low)
     *
     * @var string
     */
    private $clockSeqLow;

    /**
     * The spatially unique node identifier (node)
     *
     * @var string
     */
    private $node;

    /**
     * @param string $timeLow
     * @param string $timeMid
     * @param string $timeHiAndVersion
     * @param string $clockSeqHiAndReserved
     * @param string $clockSeqLow
     * @param string $node
     */
    public function __construct(
        $timeLow,
        $timeMid,
        $timeHiAndVersion,
        $clockSeqHiAndReserved,
        $clockSeqLow,
        $node
    ) {
        Assertion::string($timeLow);
        Assertion::string($timeMid);
        Assertion::string($timeHiAndVersion);
        Assertion::string($clockSeqHiAndReserved);
        Assertion::string($clockSeqLow);
        Assertion::string($node);

        Assertion::length($timeLow, 8);
        Assertion::length($timeMid, 4);
        Assertion::length($timeHiAndVersion, 4);
        Assertion::length($clockSeqHiAndReserved, 2);
        Assertion::length($clockSeqLow, 2);
        Assertion::length($node, 12);

        Assertion::regex($timeLow, self::HEXADECIMAL_REGEX);
        Assertion::regex($timeMid, self::HEXADECIMAL_REGEX);
        Assertion::regex($timeHiAndVersion, self::HEXADECIMAL_REGEX);
        Assertion::regex($clockSeqHiAndReserved, self::HEXADECIMAL_REGEX);
        Assertion::regex($clockSeqLow, self::HEXADECIMAL_REGEX);
        Assertion::regex($node, self::HEXADECIMAL_REGEX);

        $this->timeLow = $timeLow;
        $this->timeMid = $timeMid;
        $this->timeHiAndVersion = $timeHiAndVersion;
        $this->clockSeqHiAndReserved = $clockSeqHiAndReserved;
        $this->clockSeqLow = $clockSeqLow;
        $this->node = $node;
    }

    /**
     * @return string
     */
    public function getTimeLow()
    {
        return $this->timeLow;
    }

    /**
     * @return string
     */
    public function getTimeMid()
    {
        return $this->timeMid;
    }

    /**
     * @return string
     */
    public function getTimeHiAndVersion()
    {
        return $this->timeHiAndVersion;
    }

    /**
     * @return string
     */
    public function getClockSeqHiAndReserved()
    {
        return $this->clockSeqHiAndReserved;
    }

    /**
     * @return string
     */
    public function getClockSeqLow()
    {
        return $this->clockSeqLow;
    }

    /**
     * @return string
     */
    public function getNode()
    {
        return $this->node;
    }
}
