<?php

namespace Ramsey\Uuid\Test\Codec;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Test\TestCase;
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
        $this->fields = ['time_low' => '58e0a7d7',
            'time_mid' => 'eebc',
            'time_hi_and_version' => '11d8',
            'clock_seq_hi_and_reserved' => '96',
            'clock_seq_low' => '69',
            'node' => '0800200c9a66'];
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

    public function testEncodeBinaryUsesFieldsHex(): void
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $codec->encodeBinary($this->uuid);
    }

    public function testEncodeBinaryReturnsBinaryString(): void
    {
        $expected = hex2bin($this->optimizedHex);
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertEquals($expected, $result);
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
        $bytes = pack('H*', $this->optimizedHex);
        $codec = new OrderedTimeCodec($this->builder);
        $this->builder->method('build')->with($this->anything(), $this->equalTo($this->fields))
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertEquals($this->uuid, $result);
    }
}
