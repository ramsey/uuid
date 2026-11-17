<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\FastUuidRandomGenerator;
use Ramsey\Uuid\Test\TestCase;

use function strlen;

/**
 * @requires extension fast_uuid
 */
class FastUuidRandomGeneratorTest extends TestCase
{
    /**
     * @param int<1, max> $length
     *
     * @dataProvider provideLengths
     */
    public function testGenerateReturnsRequestedLength(int $length): void
    {
        $bytes = (new FastUuidRandomGenerator())->generate($length);

        $this->assertSame($length, strlen($bytes));
    }

    /**
     * @return array<array{int<1, max>}>
     */
    public function provideLengths(): array
    {
        return [[1], [8], [16], [4097], [9000], [70000]];
    }

    public function testGenerateReturnsDifferentBytes(): void
    {
        $generator = new FastUuidRandomGenerator();

        $this->assertNotSame($generator->generate(16), $generator->generate(16));
    }
}
