<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Nonstandard;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Nonstandard\NonstandardFields;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

class NonstandardFieldsTest extends TestCase
{
    public function testConstructorThrowsExceptionIfNotSixteenByteString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The byte string must be 16 bytes long; received 6 bytes'
        );

        new NonstandardFields('foobar');
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
        $fields = new NonstandardFields($bytes);

        $this->assertSame($expectedValue, $fields->$methodName());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function fieldGetterMethodProvider(): array
    {
        return [
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getClockSeqHiAndReserved', '0b'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeHiAndVersion', '91e1'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getVariant', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getVersion', null],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'isNil', false],
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', 'ff6f8cb0-c57d-91e1-0b21-0800200c9a66'));
        $fields = new NonstandardFields($bytes);

        $serializedFields = serialize($fields);
        $unserializedFields = unserialize($serializedFields);

        $this->assertEquals($fields, $unserializedFields);
    }
}
