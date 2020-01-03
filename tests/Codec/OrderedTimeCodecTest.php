<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Codec;

use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

class OrderedTimeCodecTest extends TestCase
{
    /**
     * @var UuidBuilderInterface & MockObject
     */
    private $builder;

    /**
     * @var UuidInterface & MockObject
     */
    private $uuid;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var string
     */
    private $uuidString = '58e0a7d7-eebc-11d8-9669-0800200c9a66';

    /**
     * @var string
     */
    private $optimizedHex = '11d8eebc58e0a7d796690800200c9a66';

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->uuid = $this->getMockBuilder(UuidInterface::class)->getMock();
        $this->fields = [
            'time_low' => '58e0a7d7',
            'time_mid' => 'eebc',
            'time_hi_and_version' => '11d8',
            'clock_seq_hi_and_reserved' => '96',
            'clock_seq_low' => '69',
            'node' => '0800200c9a66',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->builder, $this->uuid, $this->fields);
    }

    public function testEncodeUsesFieldsArray(): void
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString(): void
    {
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals($this->uuidString, $result);
    }

    public function testEncodeBinary(): void
    {
        $expected = hex2bin($this->optimizedHex);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString($this->uuidString);

        $this->assertSame($expected, $codec->encodeBinary($uuid));
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters(): void
    {
        $string = '61';
        $bytes = pack('H*', $string);
        $codec = new OrderedTimeCodec($this->builder);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeReturnsUuidFromBuilder(): void
    {
        $string = 'uuid:58e0a7d7-eebc-11d8-9669-0800200c9a66';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertEquals($this->uuid, $result);
    }

    public function testDecodeBytesRearrangesFields(): void
    {
        $bytes = (string) hex2bin($this->optimizedHex);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $expectedUuid = $factory->fromString($this->uuidString);
        $uuidReturned = $codec->decodeBytes($bytes);

        $this->assertTrue($uuidReturned->equals($expectedUuid));
    }

    public function testEncodeBinaryThrowsExceptionForNonRfc4122Uuid(): void
    {
        $nonRfc4122Uuid = '58e0a7d7-eebc-11d8-d669-0800200c9a66';

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $uuid = Mockery::mock(UuidInterface::class, [
            'getVariant' => 0,
            'toString' => $nonRfc4122Uuid,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected version 1 (time-based) UUID; received '
            . "'{$nonRfc4122Uuid}'"
        );

        $codec->encodeBinary($uuid);
    }

    public function testEncodeBinaryThrowsExceptionForNonTimeBasedUuid(): void
    {
        $nonTimeBasedUuid = '58e0a7d7-eebc-41d8-9669-0800200c9a66';

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString($nonTimeBasedUuid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected version 1 (time-based) UUID; received '
            . "'{$nonTimeBasedUuid}'"
        );

        $codec->encodeBinary($uuid);
    }

    public function testDecodeBytesThrowsExceptionsForNonRfc4122Uuid(): void
    {
        $nonRfc4122OptimizedHex = '11d8eebc58e0a7d716690800200c9a66';
        $bytes = (string) hex2bin($nonRfc4122OptimizedHex);

        $uuid = Mockery::mock(UuidInterface::class, [
            'getVariant' => Uuid::RESERVED_NCS,
        ]);

        $codec = Mockery::mock(OrderedTimeCodec::class, [
            'decode' => $uuid,
        ]);

        $codec->shouldReceive('decodeBytes')->passthru();

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage(
            'Attempting to decode a non-time-based UUID using OrderedTimeCodec'
        );

        $codec->decodeBytes($bytes);
    }

    public function testDecodeBytesThrowsExceptionsForNonTimeBasedUuid(): void
    {
        $nonTimeBasedOptimizedHex = '41d8eebc58e0a7d796690800200c9a66';
        $bytes = (string) hex2bin($nonTimeBasedOptimizedHex);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage(
            'Attempting to decode a non-time-based UUID using OrderedTimeCodec'
        );

        $codec->decodeBytes($bytes);
    }
}
