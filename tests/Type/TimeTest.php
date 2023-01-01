<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function json_encode;
use function serialize;
use function unserialize;

class TimeTest extends TestCase
{
    /**
     * @dataProvider provideTimeValues
     */
    public function testTime(
        int | IntegerObject | string $seconds,
        int | IntegerObject | string | null $microseconds
    ): void {
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
     * @return array<array{seconds: int | IntegerObject | string, microseconds: int | IntegerObject | string | null}>
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
            [
                'seconds' => new IntegerObject(103072857659),
                'microseconds' => null,
            ],
            [
                'seconds' => new IntegerObject(-12219292800),
                'microseconds' => new IntegerObject(1234),
            ],
        ];
    }

    /**
     * @dataProvider provideTimeValues
     */
    public function testSerializeUnserializeTime(
        int | IntegerObject | string $seconds,
        int | IntegerObject | string | null $microseconds
    ): void {
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

    /**
     * @dataProvider provideTimeValues
     */
    public function testJsonSerialize(
        int | IntegerObject | string $seconds,
        int | IntegerObject | string | null $microseconds
    ): void {
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
