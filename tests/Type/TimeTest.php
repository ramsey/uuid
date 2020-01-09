<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Type;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

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

        if ($microSeconds !== null) {
            $params[] = $microSeconds;
        }

        $time = new Time(...$params);

        $this->assertSame((string) $seconds, $time->getSeconds()->toString());

        $this->assertSame(
            (string) $microSeconds ?: '0',
            $time->getMicroSeconds()->toString()
        );
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
}
