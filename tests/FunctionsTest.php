<?php

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\Uuid;

class FunctionsTest extends TestCase
{
    public function testV1ReturnsVersion1UuidString()
    {
        $v1 = \Ramsey\Uuid\v1();

        $this->assertInternalType('string', $v1);
        $this->assertSame(Uuid::UUID_TYPE_TIME, Uuid::fromString($v1)->getVersion());
    }

    public function testV3ReturnsVersion3UuidString()
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v3 = \Ramsey\Uuid\v3($ns, 'https://example.com/foo');

        $this->assertInternalType('string', $v3);
        $this->assertSame(Uuid::UUID_TYPE_HASH_MD5, Uuid::fromString($v3)->getVersion());
    }

    public function testV4ReturnsVersion4UuidString()
    {
        $v4 = \Ramsey\Uuid\v4();

        $this->assertInternalType('string', $v4);
        $this->assertSame(Uuid::UUID_TYPE_RANDOM, Uuid::fromString($v4)->getVersion());
    }

    public function testV5ReturnsVersion5UuidString()
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v5 = \Ramsey\Uuid\v5($ns, 'https://example.com/foo');

        $this->assertInternalType('string', $v5);
        $this->assertSame(Uuid::UUID_TYPE_HASH_SHA1, Uuid::fromString($v5)->getVersion());
    }
}
