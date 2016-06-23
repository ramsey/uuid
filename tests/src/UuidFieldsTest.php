<?php

namespace Ramsey\Uuid\Test;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\UuidFields;

class UuidFieldsTest extends PHPUnit_Framework_TestCase
{
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
     * @param string $time_low
     * @param string $time_mid
     * @param string $time_hi_and_version
     * @param string $clock_seq_hi_and_reserved
     * @param string $clock_seq_low
     * @param string $node
     */
    public function testShouldFailIfFieldsHaveIncorrectAmountOfCharacters(
        $time_low,
        $time_mid,
        $time_hi_and_version,
        $clock_seq_hi_and_reserved,
        $clock_seq_low,
        $node
    ) {
        $this->expectException(InvalidArgumentException::class);

        new UuidFields(
            $time_low,
            $time_mid,
            $time_hi_and_version,
            $clock_seq_hi_and_reserved,
            $clock_seq_low,
            $node
        );
    }

    /**
     * @return array
     */
    public function getFieldsWithIncorrectNumberOfCharacters()
    {
        return [
            [
                '12345678a',
                '1234',
                '1234',
                '12',
                '12',
                '123456789012',
            ],
            [
                '12345678',
                '1234a',
                '1234',
                '12',
                '12',
                '123456789012',
            ],
            [
                '12345678',
                '1234',
                '1234a',
                '12',
                '12',
                '123456789012',
            ],
            [
                '12345678',
                '1234',
                '1234',
                '12a',
                '12',
                '123456789012',
            ],
            [
                '12345678',
                '1234',
                '1234',
                '12',
                '12a',
                '123456789012',
            ],
            [
                '12345678',
                '1234',
                '1234',
                '12',
                '12',
                '123456789012a',
            ],
        ];
    }
}
