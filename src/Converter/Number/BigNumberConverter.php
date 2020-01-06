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

use Brick\Math\BigInteger;
use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * BigNumberConverter converts UUIDs from hexadecimal characters into
 * brick/math `BigInteger` representations of integers and vice versa
 */
class BigNumberConverter implements NumberConverterInterface
{
    /**
     * Converts a hexadecimal number into a `Brick\Math\BigInteger` representation
     *
     * @param string $hex The hexadecimal string representation to convert
     * @return BigInteger
     */
    public function fromHex($hex)
    {
        $number = BigInteger::fromBase($hex, 16)->toBase(10);

        return BigInteger::of($number);
    }

    /**
     * Converts an integer or `Brick\Math\BigInteger` integer representation
     * into a hexadecimal string representation
     *
     * @param int|string|BigInteger $integer An integer or `Brick\Math\BigInteger`
     * @return string Hexadecimal string
     */
    public function toHex($integer)
    {
        if (!$integer instanceof BigInteger) {
            $integer = BigInteger::of($integer);
        }

        return BigInteger::fromBase($integer, 10)->toBase(16);
    }
}
