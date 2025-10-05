<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

use function hex2bin;
use function serialize;
use function str_replace;
use function unserialize;

class FieldsTest extends TestCase
{
    public function testConstructorThrowsExceptionIfNotSixteenByteString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string must be 16 bytes long; received 6 bytes'
        );

        new Fields('foobar');
    }

    /**
     * @param non-empty-string $uuid
     *
     * @dataProvider nonRfc4122VariantProvider
     */
    public function testConstructorThrowsExceptionIfNotRfc4122Variant(string $uuid): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) variant'
        );

        new Fields($bytes);
    }

    /**
     * @return array<array{0: non-empty-string}>
     */
    public function nonRfc4122VariantProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-11e1-0b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-1b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-2b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-3b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-4b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-5b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-6b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-7b21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-cb21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-db21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-eb21-0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-fb21-0800200c9a66'],
        ];
    }

    /**
     * @param non-empty-string $uuid
     *
     * @dataProvider invalidVersionProvider
     */
    public function testConstructorThrowsExceptionIfInvalidVersion(string $uuid): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not contain a valid RFC 9562 (formerly RFC 4122) version'
        );

        new Fields($bytes);
    }

    /**
     * @return array<array{0: non-empty-string}>
     */
    public function invalidVersionProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-01e1-8b21-0800200c9a66'],
            ['ff6f8cb0-c57d-91e1-bb21-0800200c9a66'],
            ['ff6f8cb0-c57d-a1e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-b1e1-ab21-0800200c9a66'],
            ['ff6f8cb0-c57d-c1e1-ab21-0800200c9a66'],
            ['ff6f8cb0-c57d-d1e1-ab21-0800200c9a66'],
            ['ff6f8cb0-c57d-e1e1-ab21-0800200c9a66'],
            ['ff6f8cb0-c57d-f1e1-ab21-0800200c9a66'],
        ];
    }

    /**
     * @param non-empty-string $uuid
     * @param non-empty-string $methodName
     * @param non-empty-string | int | bool | null $expectedValue
     *
     * @dataProvider fieldGetterMethodProvider
     */
    public function testFieldGetterMethods(
        string $uuid,
        string $methodName,
        bool | int | string | null $expectedValue,
    ): void {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));
        $fields = new Fields($bytes);

        $result = $fields->$methodName();

        if ($result instanceof Hexadecimal) {
            $this->assertSame($expectedValue, $result->toString());
        } else {
            $this->assertSame($expectedValue, $result);
        }
    }

    /**
     * @return array<array{0: non-empty-string, 1: non-empty-string, 2: non-empty-string | int | bool | null}>
     */
    public function fieldGetterMethodProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getClockSeq', '1b21'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getClockSeqHiAndReserved', '9b'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getTimeHiAndVersion', '11e1'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getVariant', 2],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'getVersion', 1],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 'isMax', false],

            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getClockSeq', '2b21'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getClockSeqHiAndReserved', 'ab'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getTimeHiAndVersion', '41e1'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getVariant', 2],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'getVersion', 4],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 'isMax', false],

            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getClockSeq', '3b21'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getClockSeqHiAndReserved', 'bb'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getTimeHiAndVersion', '31e1'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getVariant', 2],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'getVersion', 3],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 'isMax', false],

            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getClockSeq', '0b21'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getClockSeqHiAndReserved', '8b'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getTimeHiAndVersion', '51e1'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getVariant', 2],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'getVersion', 5],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 'isMax', false],

            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getClockSeq', '0b21'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getClockSeqHiAndReserved', '8b'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getTimeHiAndVersion', '61e1'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getTimestamp', 'ff6f8cb0c57d1e1'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getVariant', 2],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'getVersion', 6],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66', 'isMax', false],

            ['00000000-0000-0000-0000-000000000000', 'getClockSeq', '0000'],
            ['00000000-0000-0000-0000-000000000000', 'getClockSeqHiAndReserved', '00'],
            ['00000000-0000-0000-0000-000000000000', 'getClockSeqLow', '00'],
            ['00000000-0000-0000-0000-000000000000', 'getNode', '000000000000'],
            ['00000000-0000-0000-0000-000000000000', 'getTimeHiAndVersion', '0000'],
            ['00000000-0000-0000-0000-000000000000', 'getTimeLow', '00000000'],
            ['00000000-0000-0000-0000-000000000000', 'getTimeMid', '0000'],
            ['00000000-0000-0000-0000-000000000000', 'getTimestamp', '000000000000000'],
            ['00000000-0000-0000-0000-000000000000', 'getVariant', 0],
            ['00000000-0000-0000-0000-000000000000', 'getVersion', null],
            ['00000000-0000-0000-0000-000000000000', 'isNil', true],
            ['00000000-0000-0000-0000-000000000000', 'isMax', false],

            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getClockSeq', 'ffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getClockSeqHiAndReserved', 'ff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getClockSeqLow', 'ff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getNode', 'ffffffffffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getTimeHiAndVersion', 'ffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getTimeLow', 'ffffffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getTimeMid', 'ffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getTimestamp', 'fffffffffffffff'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getVariant', 7],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'getVersion', null],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'isNil', false],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', 'isMax', true],

            ['000001f5-5cde-21ea-8400-0242ac130003', 'getClockSeq', '0400'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getClockSeqHiAndReserved', '84'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getClockSeqLow', '00'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getNode', '0242ac130003'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getTimeHiAndVersion', '21ea'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getTimeLow', '000001f5'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getTimeMid', '5cde'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getTimestamp', '1ea5cde00000000'],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getVariant', 2],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'getVersion', 2],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'isNil', false],
            ['000001f5-5cde-21ea-8400-0242ac130003', 'isMax', false],

            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getClockSeq', '1b21'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getClockSeqHiAndReserved', '9b'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getTimeHiAndVersion', '71e1'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getTimeLow', '018339f0'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getTimeMid', '1b83'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getTimestamp', '000018339f01b83'],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getVariant', 2],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'getVersion', 7],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'isNil', false],
            ['018339f0-1b83-71e1-9b21-0800200c9a66', 'isMax', false],
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66'));
        $fields = new Fields($bytes);

        $serializedFields = serialize($fields);

        /** @var Fields $unserializedFields */
        $unserializedFields = unserialize($serializedFields);

        $this->assertSame($fields->getBytes(), $unserializedFields->getBytes());
    }

    public function testSerializingFieldsWithOldFormat(): void
    {
        $fields = new Fields("\xb3\xcd\x58\x6a\xe3\xca\x44\xf3\x98\x8c\xf4\xd6\x66\xc1\xbf\x4d");

        $serializedFields = 'C:26:"Ramsey\Uuid\Rfc4122\Fields":24:{s81YauPKRPOYjPTWZsG/TQ==}';

        /** @var Fields $unserializedFields */
        $unserializedFields = unserialize($serializedFields);

        $this->assertSame($fields->getBytes(), $unserializedFields->getBytes());
    }
}
