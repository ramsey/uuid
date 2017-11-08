<?php

namespace Ramsey\Uuid\Test\Codec;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * Class StringCodecTest
 * @package Ramsey\Uuid\Test\Codec
 * @covers Ramsey\Uuid\Codec\StringCodec
 */
class StringCodecTest extends TestCase
{

    /** @var UuidBuilderInterface */
    private $builder;
    /** @var UuidInterface */
    private $uuid;
    /** @var array */
    private $fields;
    /** @var string */
    private $uuidString = '12345678-1234-abcd-abef-1234abcd4321';

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
        $this->uuid = null;
        $this->fields = null;
    }

    public function testEncodeUsesFieldsArray()
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new StringCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString()
    {
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new StringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals($this->uuidString, $result);
    }

    public function testEncodeBinaryUsesHexadecimalValue()
    {
        $this->uuid->expects($this->once())
            ->method('getHex')
            ->willReturn('123456781234abcdabef1234abcd4321');
        $codec = new StringCodec($this->builder);
        $codec->encodeBinary($this->uuid);
    }

    public function testEncodeBinaryReturnsBinaryString()
    {
        $expected = hex2bin('123456781234abcdabef1234abcd4321');
        $this->uuid->method('getHex')
            ->willReturn('123456781234abcdabef1234abcd4321');
        $codec = new StringCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertEquals($expected, $result);
    }

    public function testDecodeUsesBuilderOnFields()
    {
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf('Ramsey\Uuid\Codec\StringCodec'), $this->fields);
        $codec = new StringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeThrowsExceptionOnInvalidUuid()
    {
        $string = 'invalid-uuid';
        $this->setExpectedException('\InvalidArgumentException');
        $codec = new StringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeReturnsUuidFromBuilder()
    {
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new StringCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertEquals($this->uuid, $result);
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters()
    {
        $string = '61';
        $bytes = pack('H*', $string);
        $codec = new StringCodec($this->builder);
        $this->setExpectedException('InvalidArgumentException', '$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeBytesReturnsUuid()
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
