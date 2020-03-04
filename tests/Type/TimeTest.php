<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

use function json_encode;
use function serialize;
use function unserialize;

class TimeTest extends TestCase
{
    /**
     * @param int|float|string $seconds
     * @param int|float|string|null $microSeconds
     *
     * @dataProvider provideTimeValues
     */
    public function testTime($seconds, $microSeconds): void
    {
        $params = [$seconds];
        $timeString = (string) $seconds;

        if ($microSeconds !== null) {
            $params[] = $microSeconds;
            $timeString .= ".{$microSeconds}";
        } else {
            $timeString .= '.0';
        }

        $time = new Time(...$params);

        $this->assertSame((string) $seconds, $time->getSeconds()->toString());

        $this->assertSame(
            (string) $microSeconds ?: '0',
            $time->getMicroSeconds()->toString()
        );

        $this->assertSame($timeString, (string) $time);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideTimeValues(): array
    {
        return [
            [
                'seconds' => 103072857659,
                'microSeconds' => null,
            ],
            [
                'seconds' => -12219292800,
                'microSeconds' => 1234,
            ],
        ];
    }

    /**
     * @param int|float|string $seconds
     * @param int|float|string|null $microSeconds
     *
     * @dataProvider provideTimeValues
     */
    public function testSerializeUnserializeTime($seconds, $microSeconds): void
    {
        $params = [$seconds];
        if ($microSeconds !== null) {
            $params[] = $microSeconds;
        }

        $time = new Time(...$params);
        $serializedTime = serialize($time);
        $unserializedTime = unserialize($serializedTime);

        $this->assertSame((string) $seconds, $unserializedTime->getSeconds()->toString());

        $this->assertSame(
            (string) $microSeconds ?: '0',
            $unserializedTime->getMicroSeconds()->toString()
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
     * @param int|float|string $seconds
     * @param int|float|string|null $microSeconds
     *
     * @dataProvider provideTimeValues
     */
    public function testJsonSerialize($seconds, $microSeconds): void
    {
        $time = [
            'seconds' => (string) $seconds,
            'microseconds' => (string) $microSeconds ?: '0',
        ];

        $expectedJson = json_encode($time);

        $params = [$seconds];
        if ($microSeconds !== null) {
            $params[] = $microSeconds;
        }

        $time = new Time(...$params);

        $this->assertSame($expectedJson, json_encode($time));
    }
}
