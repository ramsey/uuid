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

namespace Ramsey\Uuid\Converter\Number;

use Moontoast\Math\BigNumber;
use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Exception\InvalidArgumentException;
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
     * @throws InvalidArgumentException if $hex is not a hexadecimal string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     */
    public function fromHex(string $hex): string
    {
        $this->checkMoontoastMathLibrary();
        $this->checkHexadecimalString($hex, 'hex');

        return BigNumber::convertToBase10($hex, 16);
    }

    /**
     * @throws InvalidArgumentException if $integer is not an integer string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     */
    public function toHex(string $number): string
    {
        $this->checkMoontoastMathLibrary();
        $this->checkIntegerString($number, 'number');

        return BigNumber::convertFromBase10($number, 16);
    }
}
