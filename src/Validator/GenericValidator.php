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

namespace Ramsey\Uuid\Validator;

use Ramsey\Uuid\Uuid;

use function preg_match;
use function str_replace;

/**
 * GenericValidator validates strings as UUIDs of any variant
 */
class GenericValidator implements ValidatorInterface
{
    /**
     * Regular expression pattern for matching a UUID of any variant.
     */
    public const VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * @psalm-pure
     */
    public function validate(string $uuid): bool
    {
        $uuid = str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $uuid);

        return $uuid === Uuid::NIL || preg_match('/' . self::VALID_PATTERN . '/D', $uuid);
    }
}
