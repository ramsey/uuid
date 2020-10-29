<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Codec;

use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Guid\Fields;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

use function hex2bin;
use function pack;

class GuidStringCodecTest extends TestCase
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
     * @var Fields
     */
    private $fields;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->uuid = $this->getMockBuilder(UuidInterface::class)->getMock();
        $this->fields = new Fields((string) hex2bin('785634123412cd4babef1234abcd4321'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->builder, $this->fields, $this->uuid);
    }

    public function testEncodeUsesFieldsArray(): void
    {
        $this->uuid->expects($this->once())
            ->method('getFields')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString(): void
    {
        $this->uuid->method('getFields')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertSame('12345678-1234-4bcd-abef-1234abcd4321', $result);
    }

    public function testEncodeBinary(): void
    {
        $expectedBytes = (string) hex2bin('785634123412cd4babef1234abcd4321');

        $fields = new Fields($expectedBytes);
        $codec = new GuidStringCodec($this->builder);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $uuid = new Guid($fields, $numberConverter, $codec, $timeConverter);

        $bytes = $codec->encodeBinary($uuid);

        $this->assertSame($expectedBytes, $bytes);
    }

    public function testDecodeReturnsGuid(): void
    {
        $string = 'uuid:12345678-1234-4bcd-abef-1234abcd4321';

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new GuidBuilder($numberConverter, $timeConverter);
        $codec = new GuidStringCodec($builder);
        $guid = $codec->decode($string);

        $this->assertInstanceOf(Guid::class, $guid);
        $this->assertSame('12345678-1234-4bcd-abef-1234abcd4321', $guid->toString());
    }

    public function testDecodeReturnsUuidFromBuilder(): void
    {
        $string = 'uuid:78563412-3412-cd4b-abef-1234abcd4321';
        $this->builder->method('build')
            ->willReturn($this->uuid);

        $codec = new GuidStringCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertSame($this->uuid, $result);
    }

    public function testDecodeBytesReturnsUuid(): void
    {
        $string = '1234567812344bcd4bef1234abcd4321';
        $bytes = pack('H*', $string);
        $codec = new GuidStringCodec($this->builder);
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertSame($this->uuid, $result);
    }
}
