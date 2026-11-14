<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use ValueError;

use function json_encode;
use function serialize;
use function sprintf;
use function strlen;
use function unserialize;

class TimeTest extends TestCase
{
    /**
     * @param int|float|string|IntegerObject $seconds
     * @param int|float|string|IntegerObject|null $microseconds
     *
     * @dataProvider provideTimeValues
     */
    public function testTime($seconds, $microseconds): void
    {
        $params = [$seconds];

        if ($microseconds !== null) {
            $params[] = $microseconds;
        }

        $time = new Time(...$params);

        $this->assertSame((string) $seconds, $time->getSeconds()->toString());

        $this->assertSame(
            (string) $microseconds ?: '0',
            $time->getMicroseconds()->toString()
        );
    }

    /**
     * @dataProvider provideTimeStrings
     */
    public function testToString(int | string $seconds, int | string $microseconds, string $expected): void
    {
        $time = new Time($seconds, $microseconds);

        $this->assertSame($expected, $time->toString());
        $this->assertSame($expected, (string) $time);
    }

    /**
     * @return array<array{seconds: int | string, microseconds: int | string, expected: string}>
     */
    public function provideTimeStrings(): array
    {
        return [
            ['seconds' => 103072857659, 'microseconds' => 0, 'expected' => '103072857659.000000'],
            ['seconds' => 1700000000, 'microseconds' => 12346, 'expected' => '1700000000.012346'],
            ['seconds' => 0, 'microseconds' => 0, 'expected' => '0.000000'],
            ['seconds' => 0, 'microseconds' => 1, 'expected' => '0.000001'],

            // The microseconds are added to the seconds, which are rounded down for times before the epoch.
            ['seconds' => -1, 'microseconds' => 999999, 'expected' => '-0.000001'],
            ['seconds' => -2, 'microseconds' => 500000, 'expected' => '-1.500000'],
            ['seconds' => -1, 'microseconds' => 0, 'expected' => '-1.000000'],
            ['seconds' => -12219292800, 'microseconds' => 1234, 'expected' => '-12219292799.998766'],
            ['seconds' => '-99999999999999999999', 'microseconds' => 1, 'expected' => '-99999999999999999998.999999'],
        ];
    }

    /**
     * @return array<array{seconds: int|float|string|IntegerObject, microseconds: int|float|string|IntegerObject|null}>
     */
    public function provideTimeValues(): array
    {
        return [
            [
                'seconds' => 103072857659,
                'microseconds' => null,
            ],
            [
                'seconds' => -12219292800,
                'microseconds' => 1234,
            ],
        ];
    }

    /**
     * @param int|float|string|IntegerObject $seconds
     * @param int|float|string|IntegerObject|null $microseconds
     *
     * @dataProvider provideTimeValues
     */
    public function testSerializeUnserializeTime($seconds, $microseconds): void
    {
        $params = [$seconds];
        if ($microseconds !== null) {
            $params[] = $microseconds;
        }

        $time = new Time(...$params);
        $serializedTime = serialize($time);

        /** @var Time $unserializedTime */
        $unserializedTime = unserialize($serializedTime);

        $this->assertSame((string) $seconds, $unserializedTime->getSeconds()->toString());

        $this->assertSame(
            (string) $microseconds ?: '0',
            $unserializedTime->getMicroseconds()->toString()
        );
    }

    public function testUnserializeOfInvalidValueException(): void
    {
        $invalidSerialization = 'C:21:"Ramsey\\Uuid\\Type\\Time":13:{{"foo":"bar"}}';

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Attempted to unserialize an invalid value');

        unserialize($invalidSerialization);
    }

    /**
     * @param int|float|string|IntegerObject $seconds
     * @param int|float|string|IntegerObject|null $microseconds
     *
     * @dataProvider provideTimeValues
     */
    public function testJsonSerialize($seconds, $microseconds): void
    {
        $time = [
            'seconds' => (string) $seconds,
            'microseconds' => (string) $microseconds ?: '0',
        ];

        $expectedJson = json_encode($time);

        $params = [$seconds];
        if ($microseconds !== null) {
            $params[] = $microseconds;
        }

        $time = new Time(...$params);

        $this->assertSame($expectedJson, json_encode($time));
    }

    /**
     * @dataProvider provideJsonWithValuesOfTheWrongType
     */
    public function testUnserializeThrowsExceptionForValuesOfTheWrongType(string $json): void
    {
        $time = new Time(1);

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Attempted to unserialize an invalid value');

        $time->unserialize($json);
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideJsonWithValuesOfTheWrongType(): array
    {
        return [
            'seconds as an array' => ['{"seconds":[1],"microseconds":1}'],
            'microseconds as an object' => ['{"seconds":1,"microseconds":{"a":1}}'],
            'seconds as a boolean' => ['{"seconds":true,"microseconds":1}'],
            'microseconds as a boolean' => ['{"seconds":1,"microseconds":true}'],
        ];
    }

    /**
     * @dataProvider provideSerializedPropertiesOfTheWrongType
     */
    public function testUnserializingSerializedPayloadThrowsExceptionForValuesOfTheWrongType(string $properties): void
    {
        $serialized = sprintf('O:%d:"%s":2:{%s}', strlen(Time::class), Time::class, $properties);

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(Time::class . '::__unserialize(): Argument #1 ($data) is invalid');

        unserialize($serialized);
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideSerializedPropertiesOfTheWrongType(): array
    {
        return [
            'seconds as an array' => ['s:7:"seconds";a:0:{}s:12:"microseconds";s:1:"1";'],
            'microseconds as an array' => ['s:7:"seconds";s:1:"1";s:12:"microseconds";a:0:{}'],
            'seconds as a boolean' => ['s:7:"seconds";b:1;s:12:"microseconds";s:1:"1";'],
        ];
    }
}
