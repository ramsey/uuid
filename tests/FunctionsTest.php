<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;

use function Ramsey\Uuid\v1;
use function Ramsey\Uuid\v2;
use function Ramsey\Uuid\v3;
use function Ramsey\Uuid\v4;
use function Ramsey\Uuid\v5;
use function Ramsey\Uuid\v6;

class FunctionsTest extends TestCase
{
    public function testV1ReturnsVersion1UuidString(): void
    {
        $v1 = v1();

        self::assertIsString($v1);
        self::assertSame(Uuid::UUID_TYPE_TIME, Uuid::fromString($v1)->getVersion());
    }

    public function testV2ReturnsVersion2UuidString(): void
    {
        $v2 = v2(
            Uuid::DCE_DOMAIN_PERSON,
            new IntegerObject('1004'),
            new Hexadecimal('aabbccdd0011'),
            63
        );

        /** @var FieldsInterface $fields */
        $fields = Uuid::fromString($v2)->getFields();

        self::assertIsString($v2);
        self::assertSame(Uuid::UUID_TYPE_DCE_SECURITY, $fields->getVersion());
    }

    public function testV3ReturnsVersion3UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v3 = v3($ns, 'https://example.com/foo');

        self::assertIsString($v3);
        self::assertSame(Uuid::UUID_TYPE_HASH_MD5, Uuid::fromString($v3)->getVersion());
    }

    public function testV4ReturnsVersion4UuidString(): void
    {
        $v4 = v4();

        self::assertIsString($v4);
        self::assertSame(Uuid::UUID_TYPE_RANDOM, Uuid::fromString($v4)->getVersion());
    }

    public function testV5ReturnsVersion5UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v5 = v5($ns, 'https://example.com/foo');

        self::assertIsString($v5);
        self::assertSame(Uuid::UUID_TYPE_HASH_SHA1, Uuid::fromString($v5)->getVersion());
    }

    public function testV6ReturnsVersion6UuidString(): void
    {
        $v6 = v6(
            new Hexadecimal('aabbccdd0011'),
            1234
        );

        /** @var FieldsInterface $fields */
        $fields = Uuid::fromString($v6)->getFields();

        self::assertIsString($v6);
        self::assertSame(Uuid::UUID_TYPE_PEABODY, $fields->getVersion());
    }
}
