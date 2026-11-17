<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\Uuid;

use function bin2hex;
use function chr;
use function ord;
use function random_bytes;
use function str_repeat;
use function substr;

class FastUuidFromBytesTest extends TestCase
{
    /**
     * @dataProvider provideBytes
     */
    public function testFromBytesMatchesFromStringOfTheHexadecimalForm(string $bytes): void
    {
        $hex = bin2hex($bytes);
        $expected = Uuid::fromString(
            substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-'
                . substr($hex, 16, 4) . '-' . substr($hex, 20),
        );

        $actual = Uuid::fromBytes($bytes);

        $this->assertSame($expected::class, $actual::class);
        $this->assertSame($expected->toString(), $actual->toString());
        $this->assertSame($bytes, $actual->getBytes());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideBytes(): array
    {
        $values = [
            'nil' => [str_repeat("\x00", 16)],
            'max' => [str_repeat("\xff", 16)],
        ];

        foreach ([0x00, 0x80, 0xc0, 0xe0] as $variant) {
            for ($version = 0; $version < 16; $version++) {
                $bytes = random_bytes(16);
                $bytes[6] = chr(ord($bytes[6]) & 0x0f | $version << 4);
                $bytes[8] = chr(ord($bytes[8]) & 0x1f | $variant);
                $values["version $version, variant byte " . bin2hex(chr($variant))] = [$bytes];
            }
        }

        return $values;
    }
}
