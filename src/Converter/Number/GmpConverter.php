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
use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * GmpConverter uses GMP to convert UUIDs from hexadecimal to decimal string
 * representations and from decimal string representations or integers to
 * hexadecimal string representations.
 */
class GmpConverter implements NumberConverterInterface
{
    use DependencyCheckTrait;
    use NumberStringTrait;

    /**
     * Converts a hexadecimal string representation into a decimal string representation
     *
     * @param string $hex The hexadecimal string representation to convert
     * @return string
     * @throws InvalidArgumentException if $hex is not a hexadecimal string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function fromHex(string $hex): string
    {
        $this->checkGmpExtension();
        $this->checkHexadecimalString($hex, 'hex');

        $gmpNumber = gmp_init('0x' . $hex);

        return gmp_strval($gmpNumber);
    }

    /**
     * Converts an integer or a decimal string representation into a hexadecimal string representation
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
        $this->checkGmpExtension();
        $this->checkIntegerString($number, 'number');

        $gmpNumber = gmp_init($number);

        return gmp_strval($gmpNumber, 16);
    }
}
