<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Math;

use Brick\Math\RoundingMode as BrickMathRounding;
use Ramsey\Uuid\Math\BrickMathRoundingMode;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Test\TestCase;

class BrickMathRoundingModeTest extends TestCase
{
    /**
     * @dataProvider roundingModeProvider
     */
    public function testResolveReturnsValidBrickMathRoundingMode(int $ramseyMode, string $expectedName): void
    {
        $result = BrickMathRoundingMode::resolve($ramseyMode);

        $this->assertTrue(
            defined(BrickMathRounding::class . '::' . $expectedName),
            "Expected constant Brick\\Math\\RoundingMode::$expectedName to exist",
        );
        $this->assertSame(constant(BrickMathRounding::class . '::' . $expectedName), $result);
    }

    /**
     * @return array<string, array{int, string}>
     */
    public static function roundingModeProvider(): array
    {
        $usesPascalCase = defined(BrickMathRounding::class . '::Unnecessary');

        return [
            'UNNECESSARY' => [RoundingMode::UNNECESSARY, $usesPascalCase ? 'Unnecessary' : 'UNNECESSARY'],
            'UP' => [RoundingMode::UP, $usesPascalCase ? 'Up' : 'UP'],
            'DOWN' => [RoundingMode::DOWN, $usesPascalCase ? 'Down' : 'DOWN'],
            'CEILING' => [RoundingMode::CEILING, $usesPascalCase ? 'Ceiling' : 'CEILING'],
            'FLOOR' => [RoundingMode::FLOOR, $usesPascalCase ? 'Floor' : 'FLOOR'],
            'HALF_UP' => [RoundingMode::HALF_UP, $usesPascalCase ? 'HalfUp' : 'HALF_UP'],
            'HALF_DOWN' => [RoundingMode::HALF_DOWN, $usesPascalCase ? 'HalfDown' : 'HALF_DOWN'],
            'HALF_CEILING' => [RoundingMode::HALF_CEILING, $usesPascalCase ? 'HalfCeiling' : 'HALF_CEILING'],
            'HALF_FLOOR' => [RoundingMode::HALF_FLOOR, $usesPascalCase ? 'HalfFloor' : 'HALF_FLOOR'],
            'HALF_EVEN' => [RoundingMode::HALF_EVEN, $usesPascalCase ? 'HalfEven' : 'HALF_EVEN'],
        ];
    }

    public function testResolveDefaultsToUnnecessaryForUnknownMode(): void
    {
        $result = BrickMathRoundingMode::resolve(999);

        $expectedName = defined(BrickMathRounding::class . '::Unnecessary') ? 'Unnecessary' : 'UNNECESSARY';

        $this->assertSame(constant(BrickMathRounding::class . '::' . $expectedName), $result);
    }
}
