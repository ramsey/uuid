<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Lazy;

use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Throwable;

use function bin2hex;
use function chr;
use function hex2bin;
use function ord;
use function random_bytes;

/**
 * @requires extension fast_uuid
 */
class FastUuidLazyUuidFromStringTest extends TestCase
{
    /**
     * @dataProvider provideBytes
     */
    public function testFromBytesAndGetBytesMatchTheStringForm(string $bytes): void
    {
        $uuid = LazyUuidFromString::fromBytes($bytes);
        $hex = bin2hex($bytes);

        $this->assertSame(
            substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-'
                . substr($hex, 16, 4) . '-' . substr($hex, 20),
            $uuid->toString(),
        );
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @dataProvider provideBytes
     */
    public function testGetIntegerMatchesGenericNumberConverter(string $bytes): void
    {
        $expected = (new GenericNumberConverter(new BrickMathCalculator()))->fromHex(bin2hex($bytes));

        $this->assertSame($expected, LazyUuidFromString::fromBytes($bytes)->getInteger()->toString());
    }

    /**
     * @dataProvider provideBytes
     */
    public function testGetDateTimeMatchesTheFullUuid(string $bytes): void
    {
        $lazy = LazyUuidFromString::fromBytes($bytes);
        $full = $this->fullUuid($lazy);

        try {
            $expected = $full->getDateTime();
        } catch (Throwable $exception) {
            $this->expectException($exception::class);
            $lazy->getDateTime();

            return;
        }

        $actual = $lazy->getDateTime();

        $this->assertSame($expected::class, $actual::class);
        $this->assertSame($expected->format('Y-m-d H:i:s.u e'), $actual->format('Y-m-d H:i:s.u e'));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideBytes(): array
    {
        $values = [
            'nil' => [str_repeat("\x00", 16)],
            'max' => [str_repeat("\xff", 16)],
            'v7 at the last millisecond' => [str_repeat("\xff", 6) . "\x70\x00\x80" . str_repeat("\x00", 7)],
            'v1 0.9 microseconds after the epoch' => [(string) hex2bin('138140091dd211b28000010203040506')],
            'v6 0.9 microseconds after the epoch' => [(string) hex2bin('1b21dd21381460098000010203040506')],
            'v1 0.1 microseconds before the epoch' => [(string) hex2bin('13813fff1dd211b28000010203040506')],
            'v1 0.5 microseconds after the epoch' => [(string) hex2bin('138140051dd211b28000010203040506')],
            'v1 0.4 microseconds after the epoch' => [(string) hex2bin('138140041dd211b28000010203040506')],
            'v1 at the last 100 nanoseconds' => [(string) hex2bin('ffffffffffff1fff8000010203040506')],
        ];

        // Every version, with the RFC 9562 variant and with the other variants.
        foreach ([0x00, 0x80, 0xc0, 0xe0] as $variant) {
            for ($version = 0; $version < 16; $version++) {
                for ($i = 0; $i < 3; $i++) {
                    $bytes = random_bytes(16);
                    $bytes[6] = chr(ord($bytes[6]) & 0x0f | $version << 4);
                    $bytes[8] = chr(ord($bytes[8]) & 0x1f | $variant);
                    $values["version $version, variant byte " . bin2hex(chr($variant)) . ", #$i"] = [$bytes];
                }
            }
        }

        return $values;
    }

    private function fullUuid(UuidInterface $lazy): UuidInterface
    {
        return (new UuidFactory())->uuid($lazy->getBytes());
    }
}
