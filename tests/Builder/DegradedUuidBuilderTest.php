<?php

namespace Ramsey\Uuid\Test\Builder;

use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class DegradedUuidBuilderTest
 * @package Ramsey\Uuid\Test\Builder
 * @covers Ramsey\Uuid\Builder\DegradedUuidBuilder
 */
class DegradedUuidBuilderTest extends TestCase
{

    public function testBuildCreatesUuid()
    {
        $numberConverter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();
        $builder = new DegradedUuidBuilder($numberConverter, $timeConverter);
        $codec = $this->getMockBuilder(CodecInterface::class)->getMock();

        $fields = [
            'time_low' => '754cd475',
            'time_mid' => '7e58',
            'time_hi_and_version' => '5411',
            'clock_seq_hi_and_reserved' => '73',
            'clock_seq_low' => '22',
            'node' => 'be0725c8ce01'
        ];

        $result = $builder->build($codec, $fields);
        $this->assertInstanceOf(DegradedUuid::class, $result);
    }
}
