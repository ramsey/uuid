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

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * DegradedNumberConverter is chosen if all other options for large integer
 * support are unavailable. This exists to throw exceptions if these methods
 * are called on systems that do not have support for large integers.
 */
class DegradedNumberConverter implements NumberConverterInterface
{
    /**
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
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
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
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
