<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Builder;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Test\TestCase;

class DegradedUuidBuilderTest extends TestCase
{
    public function testBuildCreatesUuid(): void
    {
        /** @var MockObject & NumberConverterInterface $numberConverter */
        $numberConverter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();

        /** @var MockObject & TimeConverterInterface $timeConverter */
        $timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();

        $builder = new DegradedUuidBuilder($numberConverter, $timeConverter);

        /** @var MockObject & CodecInterface $codec */
        $codec = $this->getMockBuilder(CodecInterface::class)->getMock();

        $fields = [
            'time_low' => '754cd475',
            'time_mid' => '7e58',
            'time_hi_and_version' => '4411',
            'clock_seq_hi_and_reserved' => '93',
            'clock_seq_low' => '22',
            'node' => 'be0725c8ce01',
        ];

        $result = $builder->build($codec, $fields);
        $this->assertInstanceOf(DegradedUuid::class, $result);
    }
}
