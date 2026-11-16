<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use PHPUnit\Framework\Attributes\DataProvider;
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
    #[DataProvider('provideTimeValues')]
    public function testTime(
        int | IntegerObject | string $seconds,
        int | IntegerObject | string | null $microseconds
    ): void {
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

    #[DataProvider('provideTimeStrings')]
    public function testToString(int | string $seconds, int | string $microseconds, string $expected): void
    {
        $time = new Time($seconds, $microseconds);

        $this->assertSame($expected, $time->toString());
        $this->assertSame($expected, (string) $time);
    }

    /**
     * @return array<array{seconds: int | string, microseconds: int | string, expected: string}>
     */
    public static function provideTimeStrings(): array
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
     * @return array<array{seconds: int | IntegerObject | string, microseconds: int | IntegerObject | string | null}>
     */
    public static function provideTimeValues(): array
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

    #[DataProvider('provideTimeValues')]
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

    #[DataProvider('provideTimeValues')]
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

    #[DataProvider('provideSerializedPropertiesOfTheWrongType')]
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
    public static function provideSerializedPropertiesOfTheWrongType(): array
    {
        return [
            'seconds as an array' => ['s:7:"seconds";a:0:{}s:12:"microseconds";s:1:"1";'],
            'microseconds as an array' => ['s:7:"seconds";s:1:"1";s:12:"microseconds";a:0:{}'],
            'seconds as a boolean' => ['s:7:"seconds";b:1;s:12:"microseconds";s:1:"1";'],
        ];
    }
}
