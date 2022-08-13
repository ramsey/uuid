<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\BinaryUtils;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Variant;

use function bin2hex;
use function dechex;
use function hex2bin;

class BinaryUtilsTest extends TestCase
{
    /**
     * @dataProvider provideVersionTestValues
     */
    public function testApplyVersion(int $timeHi, Version $version, int $expectedInt, string $expectedHex): void
    {
        $this->assertSame($expectedInt, BinaryUtils::applyVersion($timeHi, $version));
        $this->assertSame($expectedHex, dechex(BinaryUtils::applyVersion($timeHi, $version)));
    }

    /**
     * @dataProvider provideVariantTestValues
     */
    public function testApplyVariant(int $clockSeq, Variant $variant, int $expectedInt, string $expectedHex): void
    {
        $this->assertSame($expectedInt, BinaryUtils::applyVariant($clockSeq, $variant));
        $this->assertSame($expectedHex, dechex(BinaryUtils::applyVariant($clockSeq, $variant)));
    }

    /**
     * @param non-empty-string $bytes
     *
     * @dataProvider provideVersionAndVariantTestValues
     */
    public function testApplyVersionAndVariant(
        string $bytes,
        Version $version,
        string $expectedHex
    ): void {
        $this->assertSame($expectedHex, bin2hex(BinaryUtils::applyVersionAndVariant($bytes, $version)));
    }

    /**
     * @return array<array{timeHi: int, version: Version, expectedInt: int, expectedHex: string}>
     */
    public function provideVersionTestValues(): array
    {
        return [
            [
                'timeHi' => 1001,
                'version' => Version::Time,
                'expectedInt' => 5097,
                'expectedHex' => '13e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::DceSecurity,
                'expectedInt' => 9193,
                'expectedHex' => '23e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::HashMd5,
                'expectedInt' => 13289,
                'expectedHex' => '33e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::Random,
                'expectedInt' => 17385,
                'expectedHex' => '43e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::HashSha1,
                'expectedInt' => 21481,
                'expectedHex' => '53e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::ReorderedTime,
                'expectedInt' => 25577,
                'expectedHex' => '63e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::UnixTime,
                'expectedInt' => 29673,
                'expectedHex' => '73e9',
            ],
            [
                'timeHi' => 1001,
                'version' => Version::Custom,
                'expectedInt' => 33769,
                'expectedHex' => '83e9',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::Time,
                'expectedInt' => 8191,
                'expectedHex' => '1fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::DceSecurity,
                'expectedInt' => 12287,
                'expectedHex' => '2fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::HashMd5,
                'expectedInt' => 16383,
                'expectedHex' => '3fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::Random,
                'expectedInt' => 20479,
                'expectedHex' => '4fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::HashSha1,
                'expectedInt' => 24575,
                'expectedHex' => '5fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::ReorderedTime,
                'expectedInt' => 28671,
                'expectedHex' => '6fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::UnixTime,
                'expectedInt' => 32767,
                'expectedHex' => '7fff',
            ],
            [
                'timeHi' => 65535,
                'version' => Version::Custom,
                'expectedInt' => 36863,
                'expectedHex' => '8fff',
            ],
            [
                'timeHi' => 0,
                'version' => Version::Time,
                'expectedInt' => 4096,
                'expectedHex' => '1000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::DceSecurity,
                'expectedInt' => 8192,
                'expectedHex' => '2000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::HashMd5,
                'expectedInt' => 12288,
                'expectedHex' => '3000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::Random,
                'expectedInt' => 16384,
                'expectedHex' => '4000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::HashSha1,
                'expectedInt' => 20480,
                'expectedHex' => '5000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::ReorderedTime,
                'expectedInt' => 24576,
                'expectedHex' => '6000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::UnixTime,
                'expectedInt' => 28672,
                'expectedHex' => '7000',
            ],
            [
                'timeHi' => 0,
                'version' => Version::Custom,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
        ];
    }

    /**
     * @return array<array{clockSeq: int, variant: Variant, expectedInt: int, expectedHex: string}>
     */
    public function provideVariantTestValues(): array
    {
        return [
            [
                'clockSeq' => 0,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 4096,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 8192,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 12288,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 4095,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 36863,
                'expectedHex' => '8fff',
            ],
            [
                'clockSeq' => 8191,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 40959,
                'expectedHex' => '9fff',
            ],
            [
                'clockSeq' => 12287,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 45055,
                'expectedHex' => 'afff',
            ],
            [
                'clockSeq' => 16383,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 49151,
                'expectedHex' => 'bfff',
            ],
            [
                'clockSeq' => 16384,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 20480,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 24576,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 28672,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 32768,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 36864,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 40960,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 45056,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 36863,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 36863,
                'expectedHex' => '8fff',
            ],
            [
                'clockSeq' => 40959,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 40959,
                'expectedHex' => '9fff',
            ],
            [
                'clockSeq' => 45055,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 45055,
                'expectedHex' => 'afff',
            ],
            [
                'clockSeq' => 49151,
                'variant' => Variant::Rfc4122,
                'expectedInt' => 49151,
                'expectedHex' => 'bfff',
            ],
        ];
    }

    /**
     * @return array<array{bytes: non-empty-string, version: Version, expectedHex: non-empty-string}>
     */
    public function provideVersionAndVariantTestValues(): array
    {
        /** @var non-empty-string $bytes */
        $bytes = hex2bin('426a25b74e975e743af1700fa4e42455');

        return [
            [
                'bytes' => $bytes,
                'version' => Version::Time,
                'expectedHex' => '426a25b74e971e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::DceSecurity,
                'expectedHex' => '426a25b74e972e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::HashMd5,
                'expectedHex' => '426a25b74e973e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::Random,
                'expectedHex' => '426a25b74e974e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::HashSha1,
                'expectedHex' => '426a25b74e975e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::ReorderedTime,
                'expectedHex' => '426a25b74e976e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::UnixTime,
                'expectedHex' => '426a25b74e977e74baf1700fa4e42455',
            ],
            [
                'bytes' => $bytes,
                'version' => Version::Custom,
                'expectedHex' => '426a25b74e978e74baf1700fa4e42455',
            ],
        ];
    }
}
