<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Builder;

use Mockery;
use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

use function hex2bin;
use function implode;

class DefaultUuidBuilderTest extends TestCase
{
    public function testBuildCreatesUuid(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);

        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);

        $fields = [
            'time_low' => '754cd475',
            'time_mid' => '7e58',
            'time_hi_and_version' => '4411',
            'clock_seq_hi_and_reserved' => '93',
            'clock_seq_low' => '22',
            'node' => 'be0725c8ce01',
        ];

        $bytes = (string) hex2bin(implode('', $fields));

        $result = $builder->build($codec, $bytes);
        $this->assertInstanceOf(Uuid::class, $result);
    }
}
