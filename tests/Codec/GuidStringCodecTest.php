<?php

namespace Ramsey\Uuid\Test\Codec;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * Class GuidStringCodecTest
 * @package Ramsey\Uuid\Test\Codec
 * @covers Ramsey\Uuid\Codec\GuidStringCodec
 */
class GuidStringCodecTest extends TestCase
{

    /** @var UuidBuilderInterface */
    private $builder;
    /** @var UuidInterface */
    private $uuid;
    /** @var array */
    private $fields;

    protected function setUp()
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder('Ramsey\Uuid\Builder\UuidBuilderInterface')->getMock();
        $this->uuid = $this->getMockBuilder('Ramsey\Uuid\UuidInterface')->getMock();
        $this->fields = ['time_low' => '12345678',
            'time_mid' => '1234',
            'time_hi_and_version' => 'abcd',
            'clock_seq_hi_and_reserved' => 'ab',
            'clock_seq_low' => 'ef',
            'node' => '1234abcd4321'];
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->builder = null;
        $this->fields = null;
        $this->uuid = null;
    }

    public function testEncodeUsesFieldsArray()
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString()
    {
        $this->skipIfBigEndianHost();
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals('78563412-3412-cdab-abef-1234abcd4321', $result);
    }

    public function testEncodeReturnsFormattedStringOnBigEndian()
    {
        $this->skipIfLittleEndianHost();
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals('12345678-1234-abcd-abef-1234abcd4321', $result);
    }


    public function testEncodeBinaryUsesFieldsArray()
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $codec->encodeBinary($this->uuid);
    }

    public function testEncodeBinaryReturnsBinaryString()
    {
        $expected = hex2bin('123456781234abcdabef1234abcd4321');
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new GuidStringCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertEquals($expected, $result);
    }

    public function testDecodeUsesBuilderOnFields()
    {
        $this->skipIfBigEndianHost();
        $string = 'uuid:78563412-3412-cdab-abef-1234abcd4321';
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf('Ramsey\Uuid\Codec\GuidStringCodec'), $this->fields);
        $codec = new GuidStringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeUsesBuilderOnFieldsOnBigEndian()
    {
        $this->skipIfLittleEndianHost();
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf('Ramsey\Uuid\Codec\GuidStringCodec'), $this->fields);
        $codec = new GuidStringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeReturnsUuidFromBuilder()
    {
        $string = 'uuid:78563412-3412-cdab-abef-1234abcd4321';
        $this->builder->method('build')
            ->willReturn($this->uuid);

        $codec = new GuidStringCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertEquals($this->uuid, $result);
    }

    public function testDecodeBytesReturnsUuid()
    {
        $string = '123456781234abcdabef1234abcd4321';
        $bytes = pack('H*', $string);
        $codec = new GuidStringCodec($this->builder);
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertEquals($this->uuid, $result);
    }
}
