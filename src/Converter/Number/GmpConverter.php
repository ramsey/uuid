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

use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * BigNumberConverter converts UUIDs from hexadecimal to decimal string representations and from
 * decimal string representations or integers to hexadecimal string representations
 */
class GmpConverter implements NumberConverterInterface
{
    /**
     * Converts a hexadecimal string representation into a decimal string representation
     *
     * @param string $hex The hexadecimal string representation to convert
     * @return string Decimal string
     */
    public function fromHex($hex)
    {
        $number = gmp_init('0x'.$hex);

        return gmp_strval($number);
    }

    /**
     * Converts an integer or a decimal string representation into a hexadecimal string representation
     *
     * @param int|string $integer An integer or decimal string representation
     * @return string Hexadecimal string
     */
    public function toHex($integer)
    {
        $number = gmp_init($integer);

        return gmp_strval($number, 16);
    }
}
