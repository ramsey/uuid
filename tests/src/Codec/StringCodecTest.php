<?php

namespace Ramsey\Uuid\Test\Codec;

use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class StringCodecTest
 * @package Ramsey\Uuid\Test\Codec
 * @covers Ramsey\Uuid\Codec\StringCodec
 */
class StringCodecTest extends TestCase
{

    private $builder;
    private $uuid;
    private $fields;

    public function setUp()
    {
        parent::setUp();
        $this->builder = $this->getMock('Ramsey\Uuid\Builder\UuidBuilderInterface');
        $this->uuid = $this->getMock('Ramsey\Uuid\UuidInterface');
        $this->fields = ['time_low' => '12345678',
            'time_mid' => '1234',
            'time_hi_and_version' => 'abcd',
            'clock_seq_hi_and_reserved' => 'ab',
            'clock_seq_low' => 'ef',
            'node' => '1234abcd4321'];
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->builder = null;
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
        $expected = '12345678-1234-abcd-abef-1234abcd4321';

        $codec = new StringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals($expected, $result);
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

    public function testDecodeUsesBulderOnFields()
    {

    }

    public function testDecodeReturnsUuidFromBuilder()
    {

    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters()
    {

    }

    public function testDecodeBytesReturnsUuid()
    {

    }
}
