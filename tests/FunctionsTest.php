<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Ramsey\Uuid\Rfc4122\UuidV7;
use Ramsey\Uuid\Rfc4122\UuidV8;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;

use function Ramsey\Uuid\v1;
use function Ramsey\Uuid\v2;
use function Ramsey\Uuid\v3;
use function Ramsey\Uuid\v4;
use function Ramsey\Uuid\v5;
use function Ramsey\Uuid\v6;
use function Ramsey\Uuid\v7;
use function Ramsey\Uuid\v8;

class FunctionsTest extends TestCase
{
    public function testV1ReturnsVersion1UuidString(): void
    {
        $v1 = v1();

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromString($v1);

        $this->assertIsString($v1);
        $this->assertSame(Version::Time, $uuid->getFields()->getVersion());
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

        $this->assertIsString($v2);
        $this->assertSame(Version::DceSecurity, $fields->getVersion());
    }

    public function testV3ReturnsVersion3UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v3 = v3($ns, 'https://example.com/foo');

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromString($v3);

        $this->assertIsString($v3);
        $this->assertSame(Version::HashMd5, $uuid->getFields()->getVersion());
    }

    public function testV4ReturnsVersion4UuidString(): void
    {
        $v4 = v4();

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromString($v4);

        $this->assertIsString($v4);
        $this->assertSame(Version::Random, $uuid->getFields()->getVersion());
    }

    public function testV5ReturnsVersion5UuidString(): void
    {
        $ns = Uuid::fromString(Uuid::NAMESPACE_URL);
        $v5 = v5($ns, 'https://example.com/foo');

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromString($v5);

        $this->assertIsString($v5);
        $this->assertSame(Version::HashSha1, $uuid->getFields()->getVersion());
    }

    public function testV6ReturnsVersion6UuidString(): void
    {
        $v6 = v6(
            new Hexadecimal('aabbccdd0011'),
            1234
        );

        /** @var FieldsInterface $fields */
        $fields = Uuid::fromString($v6)->getFields();

        $this->assertIsString($v6);
        $this->assertSame(Version::ReorderedTime, $fields->getVersion());
    }

    public function testV7ReturnsVersion7UuidString(): void
    {
        $v7 = v7();

        /** @var UuidV7 $uuid */
        $uuid = Uuid::fromString($v7);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertIsString($v7);
        $this->assertSame(Version::UnixTime, $fields->getVersion());
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
    }

    public function testV7WithCustomDateTimeReturnsVersion7UuidString(): void
    {
        $dateTime = new DateTimeImmutable('2022-09-14T22:44:33+00:00');

        $v7 = v7($dateTime);

        /** @var UuidV7 $uuid */
        $uuid = Uuid::fromString($v7);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertIsString($v7);
        $this->assertSame(Version::UnixTime, $fields->getVersion());
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertSame(1663195473, $uuid->getDateTime()->getTimestamp());
    }

    public function testV8ReturnsVersion8UuidString(): void
    {
        $v8 = v8("\x00\x11\x22\x33\x44\x55\x66\x77\x88\x99\xaa\xbb\xcc\xdd\xee\xff");

        /** @var UuidV8 $uuid */
        $uuid = Uuid::fromString($v8);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertIsString($v8);
        $this->assertSame(Version::Custom, $fields->getVersion());
    }
}
