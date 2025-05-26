<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\BinaryUtils;

use function dechex;

class BinaryUtilsTest extends TestCase
{
    /**
     * @dataProvider provideVersionTestValues
     */
    public function testApplyVersion(int $timeHi, int $version, int $expectedInt, string $expectedHex): void
    {
        $this->assertSame($expectedInt, BinaryUtils::applyVersion($timeHi, $version));
        $this->assertSame($expectedHex, dechex(BinaryUtils::applyVersion($timeHi, $version)));
    }

    /**
     * @dataProvider provideVariantTestValues
     */
    public function testApplyVariant(int $clockSeq, int $expectedInt, string $expectedHex): void
    {
        $this->assertSame($expectedInt, BinaryUtils::applyVariant($clockSeq));
        $this->assertSame($expectedHex, dechex(BinaryUtils::applyVariant($clockSeq)));
    }

    /**
     * @return array<array{timeHi: int, version: int, expectedInt: int, expectedHex: non-empty-string}>
     */
    public function provideVersionTestValues(): array
    {
        return [
            [
                'timeHi' => 1001,
                'version' => 1,
                'expectedInt' => 5097,
                'expectedHex' => '13e9',
            ],
            [
                'timeHi' => 1001,
                'version' => 2,
                'expectedInt' => 9193,
                'expectedHex' => '23e9',
            ],
            [
                'timeHi' => 1001,
                'version' => 3,
                'expectedInt' => 13289,
                'expectedHex' => '33e9',
            ],
            [
                'timeHi' => 1001,
                'version' => 4,
                'expectedInt' => 17385,
                'expectedHex' => '43e9',
            ],
            [
                'timeHi' => 1001,
                'version' => 5,
                'expectedInt' => 21481,
                'expectedHex' => '53e9',
            ],
            [
                'timeHi' => 65535,
                'version' => 1,
                'expectedInt' => 8191,
                'expectedHex' => '1fff',
            ],
            [
                'timeHi' => 65535,
                'version' => 2,
                'expectedInt' => 12287,
                'expectedHex' => '2fff',
            ],
            [
                'timeHi' => 65535,
                'version' => 3,
                'expectedInt' => 16383,
                'expectedHex' => '3fff',
            ],
            [
                'timeHi' => 65535,
                'version' => 4,
                'expectedInt' => 20479,
                'expectedHex' => '4fff',
            ],
            [
                'timeHi' => 65535,
                'version' => 5,
                'expectedInt' => 24575,
                'expectedHex' => '5fff',
            ],
            [
                'timeHi' => 0,
                'version' => 1,
                'expectedInt' => 4096,
                'expectedHex' => '1000',
            ],
            [
                'timeHi' => 0,
                'version' => 2,
                'expectedInt' => 8192,
                'expectedHex' => '2000',
            ],
            [
                'timeHi' => 0,
                'version' => 3,
                'expectedInt' => 12288,
                'expectedHex' => '3000',
            ],
            [
                'timeHi' => 0,
                'version' => 4,
                'expectedInt' => 16384,
                'expectedHex' => '4000',
            ],
            [
                'timeHi' => 0,
                'version' => 5,
                'expectedInt' => 20480,
                'expectedHex' => '5000',
            ],
        ];
    }

    /**
     * @return array<array{clockSeq: int, expectedInt: int, expectedHex: non-empty-string}>
     */
    public function provideVariantTestValues(): array
    {
        return [
            [
                'clockSeq' => 0,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 4096,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 8192,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 12288,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 4095,
                'expectedInt' => 36863,
                'expectedHex' => '8fff',
            ],
            [
                'clockSeq' => 8191,
                'expectedInt' => 40959,
                'expectedHex' => '9fff',
            ],
            [
                'clockSeq' => 12287,
                'expectedInt' => 45055,
                'expectedHex' => 'afff',
            ],
            [
                'clockSeq' => 16383,
                'expectedInt' => 49151,
                'expectedHex' => 'bfff',
            ],
            [
                'clockSeq' => 16384,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 20480,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 24576,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 28672,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 32768,
                'expectedInt' => 32768,
                'expectedHex' => '8000',
            ],
            [
                'clockSeq' => 36864,
                'expectedInt' => 36864,
                'expectedHex' => '9000',
            ],
            [
                'clockSeq' => 40960,
                'expectedInt' => 40960,
                'expectedHex' => 'a000',
            ],
            [
                'clockSeq' => 45056,
                'expectedInt' => 45056,
                'expectedHex' => 'b000',
            ],
            [
                'clockSeq' => 36863,
                'expectedInt' => 36863,
                'expectedHex' => '8fff',
            ],
            [
                'clockSeq' => 40959,
                'expectedInt' => 40959,
                'expectedHex' => '9fff',
            ],
            [
                'clockSeq' => 45055,
                'expectedInt' => 45055,
                'expectedHex' => 'afff',
            ],
            [
                'clockSeq' => 49151,
                'expectedInt' => 49151,
                'expectedHex' => 'bfff',
            ],
        ];
    }
}
