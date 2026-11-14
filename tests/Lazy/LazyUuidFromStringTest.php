<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Lazy;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Test\TestCase;

use function bin2hex;
use function serialize;
use function sprintf;
use function str_repeat;
use function strlen;
use function unserialize;

class LazyUuidFromStringTest extends TestCase
{
    private const UUID = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    public function testUnserializeRestoresUuid(): void
    {
        $unserialized = unserialize(serialize(new LazyUuidFromString(self::UUID)));

        $this->assertInstanceOf(LazyUuidFromString::class, $unserialized);
        $this->assertSame(self::UUID, $unserialized->toString());
    }

    public function testUnserializeLowercasesUppercaseUuid(): void
    {
        $uuid = new LazyUuidFromString(self::UUID);
        $uuid->unserialize('6BA7B810-9DAD-11D1-80B4-00C04FD430C8');

        $this->assertSame(self::UUID, $uuid->toString());
        $this->assertSame('6ba7b8109dad11d180b400c04fd430c8', bin2hex($uuid->getBytes()));
    }

    /**
     * @param non-empty-string $string
     *
     * @dataProvider provideInvalidStrings
     */
    public function testUnserializeThrowsExceptionForInvalidString(string $string): void
    {
        $uuid = new LazyUuidFromString(self::UUID);

        $this->expectException(InvalidUuidStringException::class);
        $this->expectExceptionMessage('Invalid UUID string: ' . $string);

        $uuid->unserialize($string);
    }

    /**
     * @dataProvider provideInvalidStrings
     */
    public function testUnserializingSerializedPayloadThrowsExceptionForInvalidString(string $string): void
    {
        $payload = sprintf(
            'O:%d:"%s":1:{s:6:"string";s:%d:"%s";}',
            strlen(LazyUuidFromString::class),
            LazyUuidFromString::class,
            strlen($string),
            $string,
        );

        $this->expectException(InvalidUuidStringException::class);
        $this->expectExceptionMessage('Invalid UUID string: ' . $string);

        unserialize($payload);
    }

    /**
     * @return array<string, array{non-empty-string}>
     */
    public function provideInvalidStrings(): array
    {
        return [
            'text that is not a UUID' => ["x' OR 1=1; DROP TABLE"],
            'text as long as a UUID' => [str_repeat('x', 36)],
            'only hyphens' => [str_repeat('-', 36)],
            'no hyphens' => ['6ba7b8109dad11d180b400c04fd430c8'],
            'too short' => ['6ba7b810-9dad-11d1-80b4-00c04fd430c'],
            'too long' => ['6ba7b810-9dad-11d1-80b4-00c04fd430c8a'],
            'trailing newline' => [self::UUID . "\n"],
            'braces' => ['{' . self::UUID . '}'],
            'prefix' => ['urn:uuid:' . self::UUID],
        ];
    }

    public function testToUuidV1ThrowsExceptionForOtherVersions(): void
    {
        $uuid = new LazyUuidFromString('c4b4ad6e-0c3e-4bb0-9a4b-8a7b2a4f0d0e');

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a version 1 or version 6 UUID');

        $uuid->toUuidV1();
    }

    public function testToUuidV6ThrowsExceptionForOtherVersions(): void
    {
        $uuid = new LazyUuidFromString('c4b4ad6e-0c3e-4bb0-9a4b-8a7b2a4f0d0e');

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a version 6 UUID');

        $uuid->toUuidV6();
    }

    public function testCompareToReturnsTheOrderOfTheUuidStrings(): void
    {
        $uuid = new LazyUuidFromString(self::UUID);

        $this->assertSame(1, $uuid->compareTo(new LazyUuidFromString('00000000-0000-4000-8000-000000000000')));
        $this->assertSame(-1, $uuid->compareTo(new LazyUuidFromString('ffffffff-ffff-4fff-bfff-ffffffffffff')));
        $this->assertSame(0, $uuid->compareTo(new LazyUuidFromString(self::UUID)));
    }

    public function testCompareToDoesNotBuildTheWrappedUuid(): void
    {
        $uuid = new LazyUuidFromString(self::UUID);
        $other = new LazyUuidFromString('ffffffff-ffff-4fff-bfff-ffffffffffff');

        $uuid->compareTo($other);

        $property = "\0" . LazyUuidFromString::class . "\0unwrapped";

        $this->assertNull(((array) $uuid)[$property]);
        $this->assertNull(((array) $other)[$property]);
    }

    public function testLazyUuidsShareOneNumberConverter(): void
    {
        $uuid = new LazyUuidFromString(self::UUID);
        $other = new LazyUuidFromString('ffffffff-ffff-4fff-bfff-ffffffffffff');

        $this->assertSame($uuid->getNumberConverter(), $other->getNumberConverter());
    }
}
