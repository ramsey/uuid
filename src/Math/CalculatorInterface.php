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

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\IntegerValue;

/**
 * A calculator performs arithmetic operations on numbers
 *
 * @psalm-immutable
 */
interface CalculatorInterface
{
    /**
     * Returns the sum of all the provided parameters
     *
     * @param IntegerValue $augend The first addend (the integer being added to)
     * @param IntegerValue ...$addends The additional integers to a add to the augend
     *
     * @return IntegerValue The sum of all the parameters
     */
    public function add(IntegerValue $augend, IntegerValue ...$addends): IntegerValue;

    /**
     * Returns the difference of all the provided parameters
     *
     * @param IntegerValue $minuend The integer being subtracted from
     * @param IntegerValue ...$subtrahends The integers to subtract from the minuend
     *
     * @return IntegerValue The difference after subtracting all parameters
     */
    public function subtract(IntegerValue $minuend, IntegerValue ...$subtrahends): IntegerValue;

    /**
     * Returns the product of all the provided parameters
     *
     * @param IntegerValue $multiplicand The integer to be multiplied
     * @param IntegerValue ...$multipliers The factors by which to multiply the multiplicand
     *
     * @return IntegerValue The product of multiplying all the provided parameters
     */
    public function multiply(IntegerValue $multiplicand, IntegerValue ...$multipliers): IntegerValue;

    /**
     * Returns the quotient of the provided parameters divided left-to-right
     *
     * @param int $roundingMode The RoundingMode constant to use for this operation
     * @param IntegerValue $dividend The integer to be divided
     * @param IntegerValue ...$divisors The integers to divide the dividend, in
     *     the order in which the division operations should take place
     *     (left-to-right)
     *
     * @return IntegerValue The quotient of dividing the provided parameters left-to-right
     */
    public function divide(int $roundingMode, IntegerValue $dividend, IntegerValue ...$divisors): IntegerValue;

    /**
     * Converts a value from an arbitrary base to a base-10 integer value
     *
     * @param string $value The value to convert
     * @param int $base The base to convert from (i.e., 2, 16, 32, etc.)
     *
     * @return IntegerValue The base-10 integer value of the converted value
     */
    public function fromBase(string $value, int $base): IntegerValue;

    /**
     * Converts a base-10 integer value to an arbitrary base
     *
     * @param IntegerValue $value The integer value to convert
     * @param int $base The base to convert to (i.e., 2, 16, 32, etc.)
     *
     * @return string The value represented in the specified base
     */
    public function toBase(IntegerValue $value, int $base): string;

    /**
     * Converts an IntegerValue instance to a Hexadecimal instance
     */
    public function toHexadecimal(IntegerValue $value): Hexadecimal;
}
