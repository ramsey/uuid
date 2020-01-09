<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Test\TestCase;

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
     * @dataProvider nonRfc4122VariantProvider
     */
    public function testConstructorThrowsExceptionIfNotRfc4122Variant(string $uuid): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not conform to the RFC 4122 variant'
        );

        new Fields($bytes);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
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
     * @dataProvider invalidVersionProvider
     */
    public function testConstructorThrowsExceptionIfInvalidVersion(string $uuid): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string received does not contain a valid RFC 4122 version'
        );

        new Fields($bytes);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function invalidVersionProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-01e1-8b21-0800200c9a66'],
            ['ff6f8cb0-c57d-61e1-8b21-0800200c9a66'],
            ['ff6f8cb0-c57d-71e1-9b21-0800200c9a66'],
            ['ff6f8cb0-c57d-81e1-ab21-0800200c9a66'],
            ['ff6f8cb0-c57d-91e1-bb21-0800200c9a66'],
        ];
    }

    /**
     * @param string|int $expectedValue
     *
     * @dataProvider fieldGetterMethodProvider
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
     */
    public function testFieldGetterMethods(string $uuid, string $methodName, $expectedValue): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));
        $fields = new Fields($bytes);

        $this->assertSame($expectedValue, $fields->$methodName());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
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
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66'));
        $fields = new Fields($bytes);

        $serializedFields = serialize($fields);
        $unserializedFields = unserialize($serializedFields);

        $this->assertEquals($fields, $unserializedFields);
    }
}
