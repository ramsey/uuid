<?php

namespace Ramsey\Uuid;

use Ramsey\Uuid\Converter\NumberConverterInterface;

class LazyUuid extends Uuid
{

    private $isParsed = false;

    private $uuidString;

    public function __construct($uuidString, NumberConverterInterface $numberConverter, CodecInterface $codec)
    {
        $this->uuidString = $uuidString;

        parent::__construct(array(), $numberConverter, $codec);
    }

    private function parse()
    {
        if ($this->isParsed) {
            return;
        }

        $this->fields = $this->codec->decode($this->uuidString)->getFieldsHex();
    }

    public function compareTo(UuidInterface $uuid)
    {
        $this->parse();

        return parent::compareTo($uuid);
    }

    public function getBytes()
    {
        $this->parse();

        return parent::getBytes();
    }

    public function getClockSeqHiAndReserved()
    {
        $this->parse();

        return parent::getClockSeqHiAndReserved();
    }

    public function getClockSeqHiAndReservedHex()
    {
        $this->parse();

        return parent::getClockSeqHiAndReservedHex();
    }

    public function getClockSeqLow()
    {
        $this->parse();

        return parent::getClockSeqLow();
    }

    public function getClockSeqLowHex()
    {
        $this->parse();

        return parent::getClockSeqLowHex();
    }

    public function getClockSequence()
    {
        $this->parse();

        return parent::getClockSequence();
    }

    public function getClockSequenceHex()
    {
        $this->parse();

        return parent::getClockSequenceHex();
    }

    public function getDateTime()
    {
        $this->parse();

        return parent::getDateTime();
    }

    public function getFields()
    {
        $this->parse();

        return parent::getFields();
    }

    public function getFieldsHex()
    {
        $this->parse();

        return parent::getFieldsHex();
    }

    public function getHex()
    {
        $this->parse();

        return parent::getHex();
    }

    public function getInteger()
    {
        $this->parse();

        return parent::getInteger();
    }

    public function getLeastSignificantBits()
    {
        $this->parse();

        return parent::getLeastSignificantBits();
    }

    public function getLeastSignificantBitsHex()
    {
        $this->parse();

        return parent::getLeastSignificantBitsHex();
    }

    public function getMostSignificantBits()
    {
        $this->parse();

        return parent::getMostSignificantBits();
    }

    public function getMostSignificantBitsHex()
    {
        $this->parse();

        return parent::getMostSignificantBitsHex();
    }

    public function getNode()
    {
        $this->parse();

        return parent::getNode();
    }

    public function getNodeHex()
    {
        $this->parse();

        return parent::getNodeHex();
    }

    public function getTimeHiAndVersion()
    {
        $this->parse();

        return parent::getTimeHiAndVersion();
    }

    public function getTimeHiAndVersionHex()
    {
        $this->parse();

        return parent::getTimeHiAndVersionHex();
    }

    public function getTimeLow()
    {
        $this->parse();

        return parent::getTimeLow();
    }

    public function getTimeLowHex()
    {
        $this->parse();

        return parent::getTimeLowHex();
    }

    public function getTimeMid()
    {
        $this->parse();

        return parent::getTimeMid();
    }

    public function getTimeMidHex()
    {
        $this->parse();

        return parent::getTimeMidHex();
    }

    public function getTimestamp()
    {
        $this->parse();

        return parent::getTimestamp();
    }

    public function getTimestampHex()
    {
        $this->parse();

        return parent::getTimestampHex();
    }

    public function getUrn()
    {
        $this->parse();

        return parent::getUrn();
    }

    public function getVariant()
    {
        $this->parse();

        return parent::getVariant();
    }

    public function getVersion()
    {
        $this->parse();

        return parent::getVersion();
    }

    public function toString()
    {
        return $this->uuidString;
    }
}
