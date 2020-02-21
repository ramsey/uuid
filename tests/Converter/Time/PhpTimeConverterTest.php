<?php

namespace Ramsey\Uuid\Test\Converter;

use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class PhpTimeConverterTest
 * @package Ramsey\Uuid\Test\Converter
 * @covers Ramsey\Uuid\Converter\Time\PhpTimeConverter
 */
class PhpTimeConverterTest extends TestCase
{
    public function testCalculateTimeReturnsArrayOfTimeSegments()
    {
        $this->skip64BitTest();

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

    /**
     * @dataProvider provideCalculateTime
     */
    public function testCalculateTime($seconds, $microSeconds, $expected)
    {
        $this->skip64BitTest();

        $converter = new PhpTimeConverter();

        $result = $converter->calculateTime($seconds, $microSeconds);

        $this->assertSame($expected, $result);
    }

    public function provideCalculateTime()
    {
        return [
            [
                'seconds' => '-12219146756',
                'microSeconds' => '0',
                'expected' => [
                    'low' => '0901e600',
                    'mid' => '0154',
                    'hi' => '0000',
                ],
            ],
            [
                'seconds' => '103072857659',
                'microseconds' => '999999',
                'expected' => [
                    'low' => 'ff9785f6',
                    'mid' => 'ffff',
                    'hi' => '0fff',
                ],
            ],
            [
                'seconds' => '1578612359',
                'microseconds' => '521023',
                'expected' => [
                    'low' => '64c71df6',
                    'mid' => '3337',
                    'hi' => '01ea',
                ],
            ],

            // This is the earliest possible date supported by v1 UUIDs:
            // 1582-10-15 00:00:00.000000
            [
                'seconds' => '-12219292800',
                'microSeconds' => '0',
                'expected' => [
                    'low' => '00000000',
                    'mid' => '0000',
                    'hi' => '0000',
                ],
            ],
        ];
    }
}
