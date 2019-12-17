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

use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * DegradedNumberConverter is chosen if all other options for large integer
 * support are unavailable. This exists to throw exceptions if these methods
 * are called on systems that do not have support for large integers.
 */
class DegradedNumberConverter implements NumberConverterInterface
{
    /**
     * Converts a hexadecimal number into an string integer representation of
     * the number
     *
     * The integer representation returned is a string representation of the
     * integer, to accommodate unsigned integers greater than PHP_INT_MAX.
     *
     * @param string $hex The hexadecimal string representation to convert
     * @return string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function fromHex(string $hex): string
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call fromHex using the DegradedNumberConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }

    /**
     * Converts a string integer representation into a hexadecimal string
     * representation of the number
     *
     * @param string $number A string integer representation to convert; this
     *     must be a numeric string to accommodate unsigned integers greater
     *     than PHP_INT_MAX.
     * @return string Hexadecimal string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     */
    public function toHex(string $number): string
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call toHex using the DegradedNumberConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }
}
