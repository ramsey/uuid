<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Nonstandard;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Nonstandard\DegradedUuid;
use Ramsey\Uuid\Nonstandard\DegradedUuidBuilder;
use Ramsey\Uuid\Test\TestCase;

class DegradedUuidBuilderTest extends TestCase
{
    public function testBuilderBuildsDegradedUuid(): void
    {
        $fields = [
            'b1484596',
            '25dc',
            '91ea',
            '078f',
            '2e728ce88125',
        ];

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $builder = new DegradedUuidBuilder($numberConverter, $timeConverter);
        $degradedNsUuid = $builder->build($codec, $fields);

        $this->assertInstanceOf(DegradedUuid::class, $degradedNsUuid);
        $this->assertSame($fields[0], $degradedNsUuid->getTimeLowHex());
        $this->assertSame($fields[1], $degradedNsUuid->getTimeMidHex());
        $this->assertSame($fields[2], $degradedNsUuid->getTimeHiAndVersionHex());
        $this->assertSame(
            $fields[3],
            $degradedNsUuid->getClockSeqHiAndReservedHex() . $degradedNsUuid->getClockSeqLowHex()
        );
        $this->assertSame($fields[4], $degradedNsUuid->getNodeHex());
    }
}
