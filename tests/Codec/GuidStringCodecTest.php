<?php

namespace Rhumsaa\Uuid\Codec;

use Rhumsaa\Uuid\TestCase;

class GuidStringCodecTest extends TestCase
{
    public function testDecodeParsesUuidStringCorrectly()
    {
        $codec = new GuidStringCodec();

        $uuid = $codec->decode('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }
}
