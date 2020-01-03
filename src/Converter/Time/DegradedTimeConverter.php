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

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * DegradedTimeConverter is chosen if all other options for large integer
 * support are unavailable. This exists to throw exceptions if these methods
 * are called on systems that do not have support for large integers.
 */
class DegradedTimeConverter implements TimeConverterInterface
{
    /**
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     *
     * @psalm-pure
     */
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call calculateTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }

    /**
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     *
     * @psalm-pure
     */
    public function convertTime(string $timestamp): string
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call convertTime using the DegradedTimeConverter; '
            . 'please choose a converter with support for large integers; '
            . 'refer to the ramsey/uuid wiki for more information: '
            . 'https://github.com/ramsey/uuid/wiki'
        );
    }
}
