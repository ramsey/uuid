<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Codec;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

class StringCodecTest extends TestCase
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
    private $uuidString = '12345678-1234-abcd-abef-1234abcd4321';

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->uuid = $this->getMockBuilder(UuidInterface::class)->getMock();
        $this->fields = ['time_low' => '12345678',
            'time_mid' => '1234',
            'time_hi_and_version' => 'abcd',
            'clock_seq_hi_and_reserved' => 'ab',
            'clock_seq_low' => 'ef',
            'node' => '1234abcd4321',
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
        $codec = new StringCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString(): void
    {
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new StringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals($this->uuidString, $result);
    }

    public function testEncodeBinaryReturnsBinaryString(): void
    {
        $expected = hex2bin('123456781234abcdabef1234abcd4321');
        $this->uuid->method('getBytes')
            ->willReturn(hex2bin('123456781234abcdabef1234abcd4321'));
        $codec = new StringCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertSame($expected, $result);
    }

    public function testDecodeUsesBuilderOnFields(): void
    {
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf(StringCodec::class), $this->fields);
        $codec = new StringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeThrowsExceptionOnInvalidUuid(): void
    {
        $string = 'invalid-uuid';
        $codec = new StringCodec($this->builder);

        $this->expectException(\InvalidArgumentException::class);
        $codec->decode($string);
    }

    public function testDecodeReturnsUuidFromBuilder(): void
    {
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new StringCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertEquals($this->uuid, $result);
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters(): void
    {
        $string = '61';
        $bytes = pack('H*', $string);
        $codec = new StringCodec($this->builder);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeBytesReturnsUuid(): void
    {
        $string = '123456781234abcdabef1234abcd4321';
        $bytes = pack('H*', $string);
        $codec = new StringCodec($this->builder);
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertEquals($this->uuid, $result);
    }
}
