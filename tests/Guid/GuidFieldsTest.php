<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Guid\GuidFields;
use Ramsey\Uuid\Test\TestCase;

class GuidFieldsTest extends TestCase
{
    public function testConstructorThrowsExceptionIfNotSixteenByteString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string must be 16 bytes long; received 6 bytes'
        );

        new GuidFields('foobar');
    }

    /**
     * @dataProvider nonRfc4122GuidVariantProvider
     */
    public function testConstructorThrowsExceptionIfNotRfc4122Variant(string $guid): void
    {
        $bytes = (string) hex2bin($guid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not conform to the RFC 4122 or '
            . 'Microsoft Corporation variants'
        );

        new GuidFields($bytes);
    }

    /**
     * These values are already in GUID byte order, for easy testing.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
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
     * @dataProvider invalidVersionProvider
     */
    public function testConstructorThrowsExceptionIfInvalidVersion(string $guid): void
    {
        $bytes = (string) hex2bin($guid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not contain a valid version'
        );

        new GuidFields($bytes);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function invalidVersionProvider(): array
    {
        // The following UUIDs are in GUID byte order. Dashes have
        // been removed in the tests to distinguish these from string
        // representations, which are never in GUID byte order.
        return [
            ['b08c6fff7dc5e1018b210800200c9a66'],
            ['b08c6fff7dc5e1618b210800200c9a66'],
            ['b08c6fff7dc5e1719b210800200c9a66'],
            ['b08c6fff7dc5e181ab210800200c9a66'],
            ['b08c6fff7dc5e191bb210800200c9a66'],
        ];
    }

    /**
     * @param string|int $expectedValue
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
     * @dataProvider fieldGetterMethodProvider
     */
    public function testFieldGetterMethods(string $bytes, string $methodName, $expectedValue): void
    {
        $bytes = (string) hex2bin($bytes);
        $fields = new GuidFields($bytes);

        $this->assertSame($expectedValue, $fields->$methodName());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function fieldGetterMethodProvider(): array
    {
        // The following UUIDs are in GUID byte order. Dashes have
        // been removed in the tests to distinguish these from string
        // representations, which are never in GUID byte order.
        return [
            // For ff6f8cb0-c57d-11e1-cb21-0800200c9a66
            ['b08c6fff7dc5e111cb210800200c9a66', 'getClockSeqHiAndReserved', 'cb'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeHiAndVersion', '11e1'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getVariant', 6],
            ['b08c6fff7dc5e111cb210800200c9a66', 'getVersion', 1],

            // For ff6f8cb0-c57d-41e1-db21-0800200c9a66
            ['b08c6fff7dc5e141db210800200c9a66', 'getClockSeqHiAndReserved', 'db'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeHiAndVersion', '41e1'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e141db210800200c9a66', 'getVariant', 6],
            ['b08c6fff7dc5e141db210800200c9a66', 'getVersion', 4],

            // For ff6f8cb0-c57d-31e1-8b21-0800200c9a66
            ['b08c6fff7dc5e1318b210800200c9a66', 'getClockSeqHiAndReserved', '8b'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeHiAndVersion', '31e1'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getVariant', 2],
            ['b08c6fff7dc5e1318b210800200c9a66', 'getVersion', 3],

            // For ff6f8cb0-c57d-51e1-9b21-0800200c9a66
            ['b08c6fff7dc5e1519b210800200c9a66', 'getClockSeqHiAndReserved', '9b'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getClockSeqLow', '21'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getNode', '0800200c9a66'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeHiAndVersion', '51e1'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getTimeMid', 'c57d'],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getVariant', 2],
            ['b08c6fff7dc5e1519b210800200c9a66', 'getVersion', 5],
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin('b08c6fff7dc5e111cb210800200c9a66');
        $fields = new GuidFields($bytes);

        $serializedFields = serialize($fields);
        $unserializedFields = unserialize($serializedFields);

        $this->assertEquals($fields, $unserializedFields);
    }
}
