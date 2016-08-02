<?php

namespace Ramsey\Uuid\Test\Codec;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidFields;
use Ramsey\Uuid\UuidInterface;

/**
 * Class OrderedTimeCodecTest
 * @package Ramsey\Uuid\Test\Codec
 * @covers Ramsey\Uuid\Codec\OrderedTimeCodec
 */
class OrderedTimeCodecTest extends TestCase
{

    /** @var UuidBuilderInterface */
    private $builder;
    /** @var UuidInterface */
    private $uuid;
    /** @var UuidFields */
    private $fields;
    /** @var string */
    private $uuidString = '58e0a7d7-eebc-11d8-9669-0800200c9a66';
    /** @var string */
    private $optimizedHex = '11d8eebc58e0a7d796690800200c9a66';

    public function setUp()
    {
        parent::setUp();
        $this->builder = $this->getMock('Ramsey\Uuid\Builder\UuidBuilderInterface');
        $this->uuid = $this->getMock('Ramsey\Uuid\UuidInterface');
        $this->fields = new UuidFields(
            '58e0a7d7',
            'eebc',
            '11d8',
            '96',
            '69',
            '0800200c9a66'
        );
    }

    public function tearDown()
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
        $codec = new OrderedTimeCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString()
    {
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertEquals($this->uuidString, $result);
    }

    public function testEncodeBinaryUsesFieldsHex()
    {
        $this->uuid->expects($this->once())
            ->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $codec->encodeBinary($this->uuid);
    }

    public function testEncodeBinaryReturnsBinaryString()
    {
        $expected = hex2bin($this->optimizedHex);
        $this->uuid->method('getFieldsHex')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertEquals($expected, $result);
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters()
    {
        $string = '61';
        $bytes = pack('H*', $string);
        $codec = new OrderedTimeCodec($this->builder);
        $this->setExpectedException('InvalidArgumentException', '$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeReturnsUuidFromBuilder()
    {
        $string = 'uuid:58e0a7d7-eebc-11d8-9669-0800200c9a66';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertEquals($this->uuid, $result);
    }

    public function testDecodeBytesRearrangesFields()
    {
        $bytes = pack('H*', $this->optimizedHex);
        $codec = new OrderedTimeCodec($this->builder);
        $this->builder->method('build')->with($this->anything(), $this->equalTo($this->fields))
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertEquals($this->uuid, $result);
    }
}
