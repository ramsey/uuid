<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\UuidFactory;

class DegradedUuidTest extends TestCase
{
    public function testGetDateTime(): void
    {
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $numberConverter
            ->shouldReceive('fromHex')
            ->once()
            ->andReturn('aFromHexValue');

        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $timeConverter
            ->shouldReceive('convertTime')
            ->once()
            ->with('aFromHexValue')
            ->andReturn('foobar');

        $builder = new DegradedUuidBuilder($numberConverter, $timeConverter);
        $codec = new StringCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString('b1484596-25dc-11ea-978f-2e728ce88125');

        $this->assertInstanceOf(DegradedUuid::class, $uuid);

        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessage(
            'DateTimeImmutable::__construct(): Failed to parse time string '
            . '(@foobar) at position 0 (@): Unexpected character'
        );

        $uuid->getDateTime();
    }
}
