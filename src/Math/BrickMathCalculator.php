<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Math;

use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Brick\Math\RoundingMode as BrickMathRounding;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\IntegerValue;

/**
 * A calculator using the brick/math library for arbitrary-precision arithmetic
 *
 * @psalm-immutable
 */
final class BrickMathCalculator implements CalculatorInterface
{
    private const ROUNDING_MODE_MAP = [
        RoundingMode::UNNECESSARY => BrickMathRounding::UNNECESSARY,
        RoundingMode::UP => BrickMathRounding::UP,
        RoundingMode::DOWN => BrickMathRounding::DOWN,
        RoundingMode::CEILING => BrickMathRounding::CEILING,
        RoundingMode::FLOOR => BrickMathRounding::FLOOR,
        RoundingMode::HALF_UP => BrickMathRounding::HALF_UP,
        RoundingMode::HALF_DOWN => BrickMathRounding::HALF_DOWN,
        RoundingMode::HALF_CEILING => BrickMathRounding::HALF_CEILING,
        RoundingMode::HALF_FLOOR => BrickMathRounding::HALF_FLOOR,
        RoundingMode::HALF_EVEN => BrickMathRounding::HALF_EVEN,
    ];

    public function add(IntegerValue $augend, IntegerValue ...$addends): IntegerValue
    {
        /** @psalm-suppress ImpureMethodCall */
        $sum = BigInteger::of($augend->toString());

        foreach ($addends as $addend) {
            /** @psalm-suppress ImpureMethodCall */
            $sum = $sum->plus($addend->toString());
        }

        return new IntegerValue((string) $sum);
    }

    public function subtract(IntegerValue $minuend, IntegerValue ...$subtrahends): IntegerValue
    {
        /** @psalm-suppress ImpureMethodCall */
        $difference = BigInteger::of($minuend->toString());

        foreach ($subtrahends as $subtrahend) {
            /** @psalm-suppress ImpureMethodCall */
            $difference = $difference->minus($subtrahend->toString());
        }

        return new IntegerValue((string) $difference);
    }

    public function multiply(IntegerValue $multiplicand, IntegerValue ...$multipliers): IntegerValue
    {
        /** @psalm-suppress ImpureMethodCall */
        $product = BigInteger::of($multiplicand->toString());

        foreach ($multipliers as $multiplier) {
            /** @psalm-suppress ImpureMethodCall */
            $product = $product->multipliedBy($multiplier->toString());
        }

        return new IntegerValue((string) $product);
    }

    public function divide(int $roundingMode, IntegerValue $dividend, IntegerValue ...$divisors): IntegerValue
    {
        $brickRounding = $this->getBrickRoundingMode($roundingMode);

        /** @psalm-suppress ImpureMethodCall */
        $quotient = BigInteger::of($dividend->toString());

        foreach ($divisors as $divisor) {
            /** @psalm-suppress ImpureMethodCall */
            $quotient = $quotient->dividedBy($divisor->toString(), $brickRounding);
        }

        return new IntegerValue((string) $quotient);
    }

    public function fromBase(string $value, int $base): IntegerValue
    {
        try {
            /** @psalm-suppress ImpureMethodCall */
            return new IntegerValue((string) BigInteger::fromBase($value, $base));
        } catch (MathException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }

    public function toBase(IntegerValue $value, int $base): string
    {
        try {
            /** @psalm-suppress ImpureMethodCall */
            return BigInteger::of($value->toString())->toBase($base);
        } catch (MathException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }

    public function toHexadecimal(IntegerValue $value): Hexadecimal
    {
        return new Hexadecimal($this->toBase($value, 16));
    }

    /**
     * Maps ramsey/uuid rounding modes to those used by brick/math
     */
    private function getBrickRoundingMode(int $roundingMode): int
    {
        return self::ROUNDING_MODE_MAP[$roundingMode] ?? 0;
    }
}
