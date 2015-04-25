<?php

namespace Ramsey\Uuid;

use Ramsey\Uuid\Converter\NumberConverterInterface;

interface UuidInterface
{

    /**
     * @return integer
     */
    public function compareTo(UuidInterface $other);

    /**
     * @return boolean
     */
    public function equals($other);

    /**
     * @return NumberConverterInterface
     */
    public function getNumberConverter();

    /**
     * @return string
     */
    public function getHex();

    public function getFieldsHex();

    /**
     * @return string
     */
    public function getClockSeqHiAndReservedHex();

    /**
     * @return string
     */
    public function getClockSeqLowHex();

    /**
     * @return string
     */
    public function getClockSequenceHex();

    /**
     * @return \DateTime
     */
    public function getDateTime();

    /**
     * @return \Moontoast\Math\BigNumber
     */
    public function getInteger();

    /**
     * @return string
     */
    public function getLeastSignificantBitsHex();

    /**
     * @return string
     */
    public function getMostSignificantBitsHex();

    /**
     * @return string
     */
    public function getNodeHex();

    /**
     * @return string
     */
    public function getTimeHiAndVersionHex();

    /**
     * @return string
     */
    public function getTimeLowHex();

    /**
     * @return string
     */
    public function getTimeMidHex();

    /**
     * @return string
     */
    public function getTimestampHex();

    /**
     * @return string
     */
    public function getUrn();

    /**
     * @return integer
     */
    public function getVariant();

    /**
     * @return integer|null
     */
    public function getVersion();

    /**
     * @return string
     */
    public function toString();
}
