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

namespace Ramsey\Uuid\Rfc4122;

/**
 * Provides common functionality for handling the version, as defined by RFC 9562 (formerly RFC 4122)
 *
 * @immutable
 */
trait VersionTrait
{
    /**
     * Returns the UUID version
     *
     * This returns `null` if the UUID is not an RFC 9562 (formerly RFC 4122) variant, since the version is only
     * meaningful for this variant.
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.2 RFC 9562, 4.2. Version Field
     *
     * @pure
     */
    abstract public function getVersion(): ?Version;

    /**
     * Returns true if these fields represent a max UUID
     */
    abstract public function isMax(): bool;

    /**
     * Returns true if these fields represent a nil UUID
     */
    abstract public function isNil(): bool;

    /**
     * Returns true if the version matches one of those defined by RFC 9562 (formerly RFC 4122)
     *
     * @return bool True if the UUID version is valid, false otherwise
     */
    private function isCorrectVersion(): bool
    {
        if ($this->isNil() || $this->isMax()) {
            return true;
        }

        return match ($this->getVersion()) {
            Version::Time, Version::DceSecurity, Version::HashMd5,
                Version::Random, Version::HashSha1, Version::ReorderedTime,
                Version::UnixTime, Version::Custom => true,
            default => false,
        };
    }
}
