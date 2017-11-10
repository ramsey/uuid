<?php

namespace Ramsey\Uuid\Test\Builder;

use Ramsey\Uuid\Builder\DegradedUuidBuilder;

/**
 * Class DegradedUuidBuilderTest
 * @package Ramsey\Uuid\Test\Builder
 * @covers Ramsey\Uuid\Builder\DegradedUuidBuilder
 */
class DegradedUuidBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildCreatesUuid()
    {
        $converter = $this->getMockBuilder('Ramsey\Uuid\Converter\NumberConverterInterface')->getMock();
        $builder = new DegradedUuidBuilder($converter);
        $codec = $this->getMockBuilder('Ramsey\Uuid\Codec\CodecInterface')->getMock();

        $fields = [
            'time_low' => '754cd475',
            'time_mid' => '7e58',
            'time_hi_and_version' => '5411',
            'clock_seq_hi_and_reserved' => '73',
            'clock_seq_low' => '22',
            'node' => 'be0725c8ce01'
        ];

        $result = $builder->build($codec, $fields);
        $this->assertInstanceOf('Ramsey\Uuid\DegradedUuid', $result);
    }
}
