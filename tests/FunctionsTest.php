<?php

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\Uuid;
use function Ramsey\Uuid\v1;
use function Ramsey\Uuid\v3;
use function Ramsey\Uuid\v4;
use function Ramsey\Uuid\v5;

class FunctionsTest extends TestCase
{
    public function testV1ReturnsVersion1UuidString(): void
    {
        $v1 = v1();

        $this->assertIsString($v1);
        $this->assertSame(Uuid::UUID_TYPE_TIME, Uuid::fromString($v1)->getVersion());
    }

    public function testV3ReturnsVersion3UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v3 = v3($ns, 'https://example.com/foo');

        $this->assertIsString($v3);
        $this->assertSame(Uuid::UUID_TYPE_HASH_MD5, Uuid::fromString($v3)->getVersion());
    }

    public function testV4ReturnsVersion4UuidString(): void
    {
        $v4 = v4();

        $this->assertIsString($v4);
        $this->assertSame(Uuid::UUID_TYPE_RANDOM, Uuid::fromString($v4)->getVersion());
    }

    public function testV5ReturnsVersion5UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v5 = v5($ns, 'https://example.com/foo');

        $this->assertIsString($v5);
        $this->assertSame(Uuid::UUID_TYPE_HASH_SHA1, Uuid::fromString($v5)->getVersion());
    }
}
