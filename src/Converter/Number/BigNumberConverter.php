<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Converter\Number;

use InvalidArgumentException;
use Moontoast\Math\BigNumber;
use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * BigNumberConverter uses moontoast/math to convert UUIDs from hexadecimal
 * characters into string representations of integers and vice versa
 */
class BigNumberConverter implements NumberConverterInterface
{
    use DependencyCheckTrait;
    use NumberStringTrait;

    /**
     * Converts a hexadecimal number into an string integer representation of
     * the number
     *
     * The integer representation returned is a string representation of the
     * integer, to accommodate unsigned integers greater than PHP_INT_MAX.
     *
     * @param string $hex The hexadecimal string representation to convert
     * @return string
     * @throws InvalidArgumentException if $hex is not a hexadecimal string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function fromHex(string $hex): string
    {
        $this->checkMoontoastMathLibrary();
        $this->checkHexadecimalString($hex, 'hex');

        return BigNumber::convertToBase10($hex, 16);
    }

    /**
     * Converts a string integer representation into a hexadecimal string
     * representation of the number
     *
     * @param string $number A string integer representation to convert; this
     *     must be a numeric string to accommodate unsigned integers greater
     *     than PHP_INT_MAX.
     * @return string Hexadecimal string
     * @throws InvalidArgumentException if $integer is not an integer string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function toHex(string $number): string
    {
        $this->checkMoontoastMathLibrary();
        $this->checkIntegerString($number, 'number');

        return BigNumber::convertFromBase10($number, 16);
    }
}
