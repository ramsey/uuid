<?php

namespace Ramsey\Uuid\Test\Builder;

use PHPUnit_Framework_TestCase;
use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class DefaultUuidBuilderTest
 * @package Ramsey\Uuid\Test\Builder
 * @covers Ramsey\Uuid\Builder\DefaultUuidBuilder
 */
class DefaultUuidBuilderTest extends PHPUnit_Framework_TestCase
{

    public function testBuildCreatesUuid()
    {
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $builder = new DefaultUuidBuilder($converter);
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
        $this->assertInstanceOf(Uuid::class, $result);
    }
}
