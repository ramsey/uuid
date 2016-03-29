<?php

namespace Ramsey\Uuid\Test\Converter;

use Ramsey\Uuid\Converter\Time\PhpTimeConverter;

/**
 * Class PhpTimeConverterTest
 * @package Ramsey\Uuid\Test\Converter
 * @covers Ramsey\Uuid\Converter\Time\PhpTimeConverter
 */
class PhpTimeConverterTest extends \PHPUnit_Framework_TestCase
{

    public function testCalculateTimeReturnsArrayOfTimeSegments()
    {
        $seconds = 5;
        $microSeconds = 3;
        $calculatedTime = ($seconds * 10000000) + ($microSeconds * 10) + 0x01b21dd213814000;
        $expectedArray = [
            'low' => sprintf('%08x', $calculatedTime & 0xffffffff),
            'mid' => sprintf('%04x', ($calculatedTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($calculatedTime >> 48) & 0x0fff)
        ];

        $converter = new PhpTimeConverter();
        $returned = $converter->calculateTime($seconds, $microSeconds);
        $this->assertEquals($expectedArray, $returned);
    }
}
