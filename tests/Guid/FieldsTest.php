<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Guid\Fields;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

use function hex2bin;
use function serialize;
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
     * @param non-empty-string $guid
     *
     * @dataProvider nonRfc4122GuidVariantProvider
     */
    public function testConstructorThrowsExceptionIfNotRfc4122Variant(string $guid): void
    {
        $bytes = (string) hex2bin($guid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) or '
            . 'Microsoft Corporation variants'
        );

        new Fields($bytes);
    }

    /**
     * These values are already in GUID byte order, for easy testing.
     *
     * @return array<array{0: non-empty-string}>
     */
    public function nonRfc4122GuidVariantProvider(): array
    {
        // In string representation, the following IDs would begin as:
        // ff6f8cb0-c57d-11e1-...
        return [
            ['b08c6fff7dc5e1110b210800200c9a66'],
            ['b08c6fff7dc5e1111b210800200c9a66'],
            ['b08c6fff7dc5e1112b210800200c9a66'],
            ['b08c6fff7dc5e1113b210800200c9a66'],
            ['b08c6fff7dc5e1114b210800200c9a66'],
            ['b08c6fff7dc5e1115b210800200c9a66'],
            ['b08c6fff7dc5e1116b210800200c9a66'],
            ['b08c6fff7dc5e1117b210800200c9a66'],
            ['b08c6fff7dc5e111eb210800200c9a66'],
            ['b08c6fff7dc5e111fb210800200c9a66'],
        ];
    }

    /**
     * @param non-empty-string $guid
     *
     * @dataProvider invalidVersionProvider
     */
    public function testConstructorThrowsExceptionIfInvalidVersion(string $guid): void
    {
        $bytes = (string) hex2bin($guid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not contain a valid version'
        );

        new Fields($bytes);
    }

    /**
     * @return array<array{0: non-empty-string}>
     */
    public function invalidVersionProvider(): array
    {
        // The following UUIDs are in GUID byte order. Dashes have
        // been removed in the tests to distinguish these from string
        // representations, which are never in GUID byte order.
        return [
            ['b08c6fff7dc5e1018b210800200c9a66'],
            ['b08c6fff7dc5e191bb210800200c9a66'],
            ['b08c6fff7dc5e1a19b210800200c9a66'],
            ['b08c6fff7dc5e1b1ab210800200c9a66'],
            ['b08c6fff7dc5e1c1ab210800200c9a66'],
            ['b08c6fff7dc5e1d1ab210800200c9a66'],
            ['b08c6fff7dc5e1e1ab210800200c9a66'],
            ['b08c6fff7dc5e1f1ab210800200c9a66'],
        ];
    }

    /**
     * @param non-empty-string $bytes
     * @param non-empty-string $methodName
     * @param non-empty-string | bool | int | null $expectedValue
     *
     * @dataProvider fieldGetterMethodProvider
     */
    public function testFieldGetterMethods(
        string $bytes,
        string $methodName,
        bool | int | string | null $expectedValue,
    ): void {
        $bytes = (string) hex2bin($bytes);
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
        // The following UUIDs are in GUID byte order. Dashes have
        // been removed in the tests to distinguish these from string
        // representations, which are never in GUID byte order.
        return [
            // For ff6f8cb0-c57d-11e1-cb21-0800200c9a66
            ['b08c6fff7dc5e111cb210800200c9a66', 'getClockSeq', '0b21'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getClockSeqHiAndReserved', 'cb'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeHiAndVersion', '11e1'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getVariant', 6],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getVersion', 1],
            ['b08c6fff7dc5e111cb210800200c9a66', 'isNil', false],
            ['b08c6fff7dc5e111cb210800200c9a66', 'isMax', false],

            // For ff6f8cb0-c57d-41e1-db21-0800200c9a66
            ['b08c6fff7dc5e141db210800200c9a66', 'getClockSeq', '1b21'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getClockSeqHiAndReserved', 'db'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeHiAndVersion', '41e1'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getVariant', 6],
            ['b08c6fff7dc5e141db210800200c9a66', 'getVersion', 4],
            ['b08c6fff7dc5e141db210800200c9a66', 'isNil', false],
            ['b08c6fff7dc5e141db210800200c9a66', 'isMax', false],

            // For ff6f8cb0-c57d-31e1-8b21-0800200c9a66
            ['b08c6fff7dc5e1318b210800200c9a66', 'getClockSeq', '0b21'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getClockSeqHiAndReserved', '8b'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeHiAndVersion', '31e1'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getVariant', 2],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getVersion', 3],
            ['b08c6fff7dc5e1318b210800200c9a66', 'isNil', false],
            ['b08c6fff7dc5e1318b210800200c9a66', 'isMax', false],

            // For ff6f8cb0-c57d-51e1-9b21-0800200c9a66
            ['b08c6fff7dc5e1519b210800200c9a66', 'getClockSeq', '1b21'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getClockSeqHiAndReserved', '9b'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeHiAndVersion', '51e1'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getVariant', 2],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getVersion', 5],
            ['b08c6fff7dc5e1519b210800200c9a66', 'isNil', false],
            ['b08c6fff7dc5e1519b210800200c9a66', 'isMax', false],

            // For 00000000-0000-0000-0000-000000000000
            ['00000000000000000000000000000000', 'getClockSeq', '0000'],
            ['00000000000000000000000000000000', 'getClockSeqHiAndReserved', '00'],
            ['00000000000000000000000000000000', 'getClockSeqLow', '00'],
            ['00000000000000000000000000000000', 'getNode', '000000000000'],
            ['00000000000000000000000000000000', 'getTimeHiAndVersion', '0000'],
            ['00000000000000000000000000000000', 'getTimeLow', '00000000'],
            ['00000000000000000000000000000000', 'getTimeMid', '0000'],
            ['00000000000000000000000000000000', 'getTimestamp', '000000000000000'],
            ['00000000000000000000000000000000', 'getVariant', 0],
            ['00000000000000000000000000000000', 'getVersion', null],
            ['00000000000000000000000000000000', 'isNil', true],
            ['00000000000000000000000000000000', 'isMax', false],

            // For ffffffff-ffff-ffff-ffff-ffffffffffff
            ['ffffffffffffffffffffffffffffffff', 'getClockSeq', 'ffff'],
            ['ffffffffffffffffffffffffffffffff', 'getClockSeqHiAndReserved', 'ff'],
            ['ffffffffffffffffffffffffffffffff', 'getClockSeqLow', 'ff'],
            ['ffffffffffffffffffffffffffffffff', 'getNode', 'ffffffffffff'],
            ['ffffffffffffffffffffffffffffffff', 'getTimeHiAndVersion', 'ffff'],
            ['ffffffffffffffffffffffffffffffff', 'getTimeLow', 'ffffffff'],
            ['ffffffffffffffffffffffffffffffff', 'getTimeMid', 'ffff'],
            ['ffffffffffffffffffffffffffffffff', 'getTimestamp', 'fffffffffffffff'],
            ['ffffffffffffffffffffffffffffffff', 'getVariant', 7],
            ['ffffffffffffffffffffffffffffffff', 'getVersion', null],
            ['ffffffffffffffffffffffffffffffff', 'isNil', false],
            ['ffffffffffffffffffffffffffffffff', 'isMax', true],
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin('b08c6fff7dc5e111cb210800200c9a66');
        $fields = new Fields($bytes);

        $serializedFields = serialize($fields);

        /** @var Fields $unserializedFields */
        $unserializedFields = unserialize($serializedFields);

        $this->assertSame($fields->getBytes(), $unserializedFields->getBytes());
    }
}
