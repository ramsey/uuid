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
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
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

    public function testGetDateTimeThrowsExceptionIfUuidIsNotVersion1(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class, [
            'getVersion' => 4,
        ]);

        $degradedUuid->shouldReceive('getDateTime')->passthru();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a time-based UUID');

        $degradedUuid->getDateTime();
    }

    public function testGetTimestampThrowsExceptionIfUuidIsNotVersion1(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class, [
            'getVersion' => 4,
        ]);

        $degradedUuid->shouldReceive('getTimestamp')->passthru();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a time-based UUID');

        $degradedUuid->getTimestamp();
    }

    public function testGetFieldsThrowsException(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class);
        $degradedUuid->shouldReceive('getFields')->passthru();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call Ramsey\\Uuid\\DegradedUuid::getFields on a 32-bit '
            . 'system, since some values overflow the system max integer value; '
            . 'consider calling getFieldsHex instead'
        );

        $degradedUuid->getFields();
    }

    public function testGetNodeThrowsException(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class);
        $degradedUuid->shouldReceive('getNode')->passthru();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call Ramsey\\Uuid\\DegradedUuid::getNode on a 32-bit '
            . 'system, since node is an unsigned 48-bit integer and can '
            . 'overflow the system max integer value; consider calling '
            . 'getNodeHex instead'
        );

        $degradedUuid->getNode();
    }

    public function testGetTimeLowThrowsException(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class);
        $degradedUuid->shouldReceive('getTimeLow')->passthru();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call Ramsey\\Uuid\\DegradedUuid::getTimeLow on a 32-bit '
            . 'system, since time_low is an unsigned 32-bit integer and can '
            . 'overflow the system max integer value; consider calling '
            . 'getTimeLowHex instead'
        );

        $degradedUuid->getTimeLow();
    }

    public function testGetTimestampThrowsExceptionFor32BitSystem(): void
    {
        $degradedUuid = Mockery::mock(DegradedUuid::class, [
            'getVersion' => 1,
        ]);
        $degradedUuid->shouldReceive('getTimestamp')->passthru();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage(
            'Cannot call Ramsey\\Uuid\\DegradedUuid::getTimestamp on a 32-bit '
            . 'system, since timestamp is an unsigned 60-bit integer and can '
            . 'overflow the system max integer value; consider calling '
            . 'getTimestampHex instead'
        );

        $degradedUuid->getTimestamp();
    }
}
