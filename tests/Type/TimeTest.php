<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function json_encode;
use function serialize;
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
        $timeString = (string) $seconds;

        if ($microseconds !== null) {
            $params[] = $microseconds;
            $timeString .= sprintf('.%06s', (string) $microseconds);
        } else {
            $timeString .= '.000000';
        }

        $time = new Time(...$params);

        $this->assertSame((string) $seconds, $time->getSeconds()->toString());

        $this->assertSame(
            (string) $microseconds ?: '0',
            $time->getMicroseconds()->toString()
        );

        $this->assertSame($timeString, (string) $time);
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
}
