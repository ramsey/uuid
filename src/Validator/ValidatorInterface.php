<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Bradyn Poulsen <bradyn@bradynpoulsen.com>
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Validator;

/**
 * Outlines common behavior of UUID validators
 */
interface ValidatorInterface
{
    /**
     * Validate that a string represents a UUID
     *
     * @param string $uuid
     * @return bool Returns TRUE if the string was validated as a valid UUID or FALSE on failure
     */
    public function validate(string $uuid): bool;
}
