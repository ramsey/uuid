<?php

namespace Ramsey\Uuid\Test\Builder;

use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFields;

/**
 * Class DefaultUuidBuilderTest
 * @package Ramsey\Uuid\Test\Builder
 * @covers Ramsey\Uuid\Builder\DefaultUuidBuilder
 */
class DefaultUuidBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildCreatesUuid()
    {
        $numberConverter = $this->createMock(NumberConverterInterface::class);
        $timeConverter = $this->createMock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = $this->createMock(CodecInterface::class);

        $fields = new UuidFields(
            '754cd475',
            '7e58',
            '5411',
            '73',
            '22',
            'be0725c8ce01'
        );

        $result = $builder->build($codec, $fields);
        $this->assertInstanceOf(Uuid::class, $result);
    }
}
