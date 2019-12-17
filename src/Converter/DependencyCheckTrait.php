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

namespace Ramsey\Uuid\Converter;

use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * Provides methods to check dependencies for various converters
 */
trait DependencyCheckTrait
{
    /**
     * Returns boolean true if the current build of PHP is a 64-bit build,
     * throws UnsatisfiedDependencyException otherwise
     *
     * @throws UnsatisfiedDependencyException if PHP is not 64-bit
     */
    private function check64BitPhp(): bool
    {
        if ($this->getPhpIntSize() < 8) {
            throw new UnsatisfiedDependencyException(
                'The PHP build must be 64-bit to use this converter'
            );
        }

        return true;
    }

    /**
     * Returns boolean true if the GMP extension is loaded, throws
     * UnsatisfiedDependencyException otherwise
     *
     * @throws UnsatisfiedDependencyException if GMP is not loaded
     */
    private function checkGmpExtension(): bool
    {
        if (!extension_loaded('gmp')) {
            throw new UnsatisfiedDependencyException(
                'ext-gmp must be present to use this converter'
            );
        }

        return true;
    }

    /**
     * Returns boolean true if the moontoast/math library is present, throws
     * UnsatisfiedDependencyException otherwise
     *
     * @throws UnsatisfiedDependencyException if moontoast/math is not loaded
     */
    private function checkMoontoastMathLibrary(): bool
    {
        if (!class_exists('Moontoast\Math\BigNumber')) {
            throw new UnsatisfiedDependencyException(
                'moontoast/math must be present to use this converter'
            );
        }

        return true;
    }

    protected function getPhpIntSize(): int
    {
        return PHP_INT_SIZE;
    }
}
