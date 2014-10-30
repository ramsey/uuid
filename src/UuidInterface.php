<?php

namespace Rhumsaa\Uuid;

interface UuidInterface
{

    public function compareTo(UuidInterface $other);

    public function equals($other);

    public function getHex();

    public function getFieldsHex();

    public function getClockSeqHiAndReservedHex();

    public function getClockSeqLowHex();

    public function getClockSequenceHex();

    public function getDateTime();

    public function getInteger();

    public function getLeastSignificantBitsHex();

    public function getMostSignificantBitsHex();

    public function getNodeHex();

    public function getTimeHiAndVersionHex();

    public function getTimeLowHex();

    public function getTimeMidHex();

    public function getTimestampHex();

    public function getUrn();

    public function getVariant();

    public function getVersion();

    public function toString();

}
