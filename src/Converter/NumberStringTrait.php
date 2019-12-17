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

namespace Ramsey\Uuid\Converter;

use InvalidArgumentException;

/**
 * Provides shared functionality to check the values of string numbers for
 * conversion
 */
trait NumberStringTrait
{
    /**
     * Returns boolean true if the string $integer contains only digits, throws
     * InvalidArgumentException otherwise
     *
     * @param string $integer The string integer value to check
     * @param string $param The name of the parameter being checked
     * @return bool
     */
    private function checkIntegerString(string $integer, string $param): bool
    {
        // If it is a negative integer, remove the sign so that the string
        // can be checked properly for only digits with ctype_digit().
        if (strpos($integer, '-') === 0) {
            $integer = substr($integer, 1);
        }

        if (!ctype_digit($integer)) {
            throw new InvalidArgumentException(
                "\${$param} must contain only digits"
            );
        }

        return true;
    }

    /**
     * Returns boolean true if the string $hex contains only hexadecimal
     * characters, throws InvalidArgumentException otherwise
     *
     * @param string $hex The hexadecimal string value to check
     * @param string $param The name of the parameter being checked
     * @return bool
     */
    private function checkHexadecimalString(string $hex, string $param): bool
    {
        if (!ctype_xdigit($hex)) {
            throw new InvalidArgumentException(
                "\${$param} must contain only hexadecimal characters"
            );
        }

        return true;
    }
}
