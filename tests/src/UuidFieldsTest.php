<?php

namespace Ramsey\Uuid\Test;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\UuidFields;

class UuidFieldsTest extends PHPUnit_Framework_TestCase
{
    const TIME_LOW = '12345678';

    const TIME_MID = '1234';

    const TIME_HI_AND_VERSION = '1234';

    const CLOCK_SEQ_HI_AND_RESERVED = '12';

    const CLOCK_SEQ_LOW = '12';

    const NODE = '123456789012';

    public function testShouldFailIfFieldsAreNotStrings()
    {
        $this->expectException(InvalidArgumentException::class);

        new UuidFields(
            false,
            false,
            false,
            false,
            false,
            false
        );
    }

    /**
     * @dataProvider getFieldsWithIncorrectNumberOfCharacters
     *
     * @param string $timeLow
     * @param string $timeMid
     * @param string $timeHiAndVersion
     * @param string $clockSeqHiAndReserved
     * @param string $clockSeqLow
     * @param string $node
     */
    public function testShouldFailIfFieldsHaveIncorrectAmountOfCharacters(
        $timeLow,
        $timeMid,
        $timeHiAndVersion,
        $clockSeqHiAndReserved,
        $clockSeqLow,
        $node
    ) {
        $this->expectException(InvalidArgumentException::class);

        new UuidFields(
            $timeLow,
            $timeMid,
            $timeHiAndVersion,
            $clockSeqHiAndReserved,
            $clockSeqLow,
            $node
        );
    }

    /**
     * @dataProvider getFieldsWithNonHexadecimalCharacters
     *
     * @param string $timeLow
     * @param string $timeMid
     * @param string $timeHiAndVersion
     * @param string $clockSeqHiAndReserved
     * @param string $clockSeqLow
     * @param string $node
     */
    public function testShouldFailIfFieldsContainNonHexadecimalCharacters(
        $timeLow,
        $timeMid,
        $timeHiAndVersion,
        $clockSeqHiAndReserved,
        $clockSeqLow,
        $node
    ) {
        $this->expectException(InvalidArgumentException::class);

        new UuidFields(
            $timeLow,
            $timeMid,
            $timeHiAndVersion,
            $clockSeqHiAndReserved,
            $clockSeqLow,
            $node
        );
    }

    public function testShouldReturnFields()
    {
        $fields = new UuidFields(
            self::TIME_LOW,
            self::TIME_MID,
            self::TIME_HI_AND_VERSION,
            self::CLOCK_SEQ_HI_AND_RESERVED,
            self::CLOCK_SEQ_LOW,
            self::NODE
        );

        $this->assertSame(self::TIME_LOW, $fields->getTimeLow());
        $this->assertSame(self::TIME_MID, $fields->getTimeMid());
        $this->assertSame(self::TIME_HI_AND_VERSION, $fields->getTimeHiAndVersion());
        $this->assertSame(self::CLOCK_SEQ_HI_AND_RESERVED, $fields->getClockSeqHiAndReserved());
        $this->assertSame(self::CLOCK_SEQ_LOW, $fields->getClockSeqLow());
        $this->assertSame(self::NODE, $fields->getNode());
    }

    /**
     * @return array
     */
    public function getFieldsWithIncorrectNumberOfCharacters()
    {
        return [
            [
                '12345678a',
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                '1234a',
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                '1234a',
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                '12a',
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                '12a',
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                '123456789012a',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFieldsWithNonHexadecimalCharacters()
    {
        return [
            [
                '!!!!!!!!',
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                '12!4',
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                '123-',
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                '1G',
                self::CLOCK_SEQ_LOW,
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                'ZZ',
                self::NODE,
            ],
            [
                self::TIME_LOW,
                self::TIME_MID,
                self::TIME_HI_AND_VERSION,
                self::CLOCK_SEQ_HI_AND_RESERVED,
                self::CLOCK_SEQ_LOW,
                '1234!6789012',
            ],
        ];
    }
}
