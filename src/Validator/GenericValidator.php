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
use function preg_replace;
use function strlen;

/**
 * GenericValidator validates strings as UUIDs of any variant
 *
 * @immutable
 */
final class GenericValidator implements ValidatorInterface
{
    /**
     * Regular expression pattern for matching a UUID of any variant.
     */
    private const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';

    /**
     * Pattern to match the optional "urn:" and "uuid:" prefixes and the optional pair of braces around a UUID.
     */
    private const WRAPPER_PATTERN = '/\A(?:urn:)?(?:uuid:)?(?:\{(.*)\}|(.*))\z/Dis';

    /**
     * The length of the longest valid form: "urn:uuid:", braces, and 36 characters.
     */
    private const MAX_LENGTH = 47;

    /**
     * @return non-empty-string
     */
    public function getPattern(): string
    {
        return self::VALID_PATTERN;
    }

    public function validate(string $uuid): bool
    {
        // No valid form is longer than a UUID in braces after the "urn:uuid:" prefix.
        if (strlen($uuid) > self::MAX_LENGTH) {
            return false;
        }

        // Remove one "urn:" prefix, one "uuid:" prefix, and one pair of braces around the UUID, if present.
        /** @phpstan-ignore possiblyImpure.functionCall */
        $uuid = preg_replace(self::WRAPPER_PATTERN, '$1$2', $uuid) ?? $uuid;

        /** @phpstan-ignore possiblyImpure.functionCall */
        return $uuid === Uuid::NIL || preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}
