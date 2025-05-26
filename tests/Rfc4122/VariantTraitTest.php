<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Mockery;
use Ramsey\Uuid\Exception\InvalidBytesException;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\VariantTrait;
use Ramsey\Uuid\Test\TestCase;

use function hex2bin;
use function str_replace;

class VariantTraitTest extends TestCase
{
    /**
     * @dataProvider invalidBytesProvider
     */
    public function testGetVariantThrowsExceptionForWrongNumberOfBytes(string $bytes): void
    {
        /** @var Fields $trait */
        $trait = Mockery::mock(VariantTrait::class, [
            'getBytes' => $bytes,
            'isMax' => false,
            'isNil' => false,
        ]);

        $this->expectException(InvalidBytesException::class);
        $this->expectExceptionMessage('Invalid number of bytes');

        $trait->getVariant();
    }

    /**
     * @return array<array{0: non-empty-string}>
     */
    public function invalidBytesProvider(): array
    {
        return [
            ['not16Bytes_abcd'],
            ['not16Bytes_abcdef'],
        ];
    }

    /**
     * @dataProvider uuidVariantProvider
     */
    public function testGetVariant(string $uuid, int $expectedVariant): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        /** @var Fields $trait */
        $trait = Mockery::mock(VariantTrait::class, [
            'getBytes' => $bytes,
            'isMax' => false,
            'isNil' => false,
        ]);

        $this->assertSame($expectedVariant, $trait->getVariant());
    }

    /**
     * @return array<array{0: non-empty-string, 1: int}>
     */
    public function uuidVariantProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-11e1-0b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-1b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-2b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-3b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-4b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-5b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-6b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-7b21-0800200c9a66', 0],
            ['ff6f8cb0-c57d-11e1-8b21-0800200c9a66', 2],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 2],
            ['ff6f8cb0-c57d-11e1-ab21-0800200c9a66', 2],
            ['ff6f8cb0-c57d-11e1-bb21-0800200c9a66', 2],
            ['ff6f8cb0-c57d-11e1-cb21-0800200c9a66', 6],
            ['ff6f8cb0-c57d-11e1-db21-0800200c9a66', 6],
            ['ff6f8cb0-c57d-11e1-eb21-0800200c9a66', 7],
            ['ff6f8cb0-c57d-11e1-fb21-0800200c9a66', 7],

            // The following are the same UUIDs in GUID byte order. Dashes have
            // been removed in the tests to distinguish these from string
            // representations, which are never in GUID byte order.
            ['b08c6fff7dc5e1110b210800200c9a66', 0],
            ['b08c6fff7dc5e1111b210800200c9a66', 0],
            ['b08c6fff7dc5e1112b210800200c9a66', 0],
            ['b08c6fff7dc5e1113b210800200c9a66', 0],
            ['b08c6fff7dc5e1114b210800200c9a66', 0],
            ['b08c6fff7dc5e1115b210800200c9a66', 0],
            ['b08c6fff7dc5e1116b210800200c9a66', 0],
            ['b08c6fff7dc5e1117b210800200c9a66', 0],
            ['b08c6fff7dc5e1118b210800200c9a66', 2],
            ['b08c6fff7dc5e1119b210800200c9a66', 2],
            ['b08c6fff7dc5e111ab210800200c9a66', 2],
            ['b08c6fff7dc5e111bb210800200c9a66', 2],
            ['b08c6fff7dc5e111cb210800200c9a66', 6],
            ['b08c6fff7dc5e111db210800200c9a66', 6],
            ['b08c6fff7dc5e111eb210800200c9a66', 7],
            ['b08c6fff7dc5e111fb210800200c9a66', 7],
        ];
    }
}
