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

use Ramsey\Uuid\Uuid;

/**
 * Default validation behavior
 */
class Validator implements ValidatorInterface
{
    /**
     * Regular expression pattern for matching a valid UUID of any variant.
     */
    const VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Validate that a string represents a UUID
     *
     * @param string $uuid
     * @return bool Returns TRUE if the string was validated as a valid UUID or FALSE on failure
     */
    public function validate($uuid): bool
    {
        $uuid = str_replace(['urn:', 'uuid:', '{', '}'], '', $uuid);

        return $uuid === Uuid::NIL || preg_match('/' . self::VALID_PATTERN . '/D', $uuid);
    }
}
