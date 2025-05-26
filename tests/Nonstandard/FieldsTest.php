<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Nonstandard;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Nonstandard\Fields;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

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
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getClockSeq', '0b21'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getClockSeqHiAndReserved', '0b'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getClockSeqLow', '21'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getNode', '0800200c9a66'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeHiAndVersion', '91e1'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeLow', 'ff6f8cb0'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimeMid', 'c57d'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getTimestamp', '1e1c57dff6f8cb0'],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getVariant', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'getVersion', null],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'isNil', false],
            ['ff6f8cb0-c57d-91e1-0b21-0800200c9a66', 'isMax', false],
        ];
    }

    public function testSerializingFields(): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', 'ff6f8cb0-c57d-91e1-0b21-0800200c9a66'));
        $fields = new Fields($bytes);

        $serializedFields = serialize($fields);

        /** @var Fields $unserializedFields */
        $unserializedFields = unserialize($serializedFields);

        $this->assertSame($fields->getBytes(), $unserializedFields->getBytes());
    }
}
