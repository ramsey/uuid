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

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\VariantTrait;

/**
 * Nonstandard UUID fields do not conform to the RFC 4122 standard
 *
 * Since some systems may create nonstandard UUIDs, this implements the
 * Rfc4122\FieldsInterface, so that functionality of a nonstandard UUID is not
 * degraded, in the event these UUIDs are expected to contain RFC 4122 fields.
 *
 * Internally, this class represents the fields together as a 16-byte binary
 * string.
 *
 * @psalm-immutable
 */
final class Fields implements FieldsInterface
{
    use SerializableFieldsTrait;
    use VariantTrait;

    /**
     * @var string
     */
    private $bytes;

    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     */
    public function __construct(string $bytes)
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException(
                'The byte string must be 16 bytes long; '
                . 'received ' . strlen($bytes) . ' bytes'
            );
        }

        $this->bytes = $bytes;
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getClockSeqHiAndReserved(): string
    {
        return bin2hex(substr($this->bytes, 8, 1));
    }

    public function getClockSeqLow(): string
    {
        return bin2hex(substr($this->bytes, 9, 1));
    }

    public function getNode(): string
    {
        return bin2hex(substr($this->bytes, 10));
    }

    public function getTimeHiAndVersion(): string
    {
        return bin2hex(substr($this->bytes, 6, 2));
    }

    public function getTimeLow(): string
    {
        return bin2hex(substr($this->bytes, 0, 4));
    }

    public function getTimeMid(): string
    {
        return bin2hex(substr($this->bytes, 4, 2));
    }

    public function getVersion(): ?int
    {
        return null;
    }

    public function isNil(): bool
    {
        return false;
    }
}
