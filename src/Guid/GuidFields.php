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

namespace Ramsey\Uuid\Guid;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Ramsey\Uuid\Rfc4122\NilTrait;
use Ramsey\Uuid\Rfc4122\Rfc4122FieldsInterface;
use Ramsey\Uuid\Rfc4122\VariantTrait;
use Ramsey\Uuid\Rfc4122\VersionTrait;
use Ramsey\Uuid\Uuid;

/**
 * GUIDs are comprised of a set of named fields, according to RFC 4122
 *
 * @psalm-immutable
 */
final class GuidFields implements Rfc4122FieldsInterface
{
    use NilTrait;
    use SerializableFieldsTrait;
    use VariantTrait;
    use VersionTrait;

    /**
     * @var string
     */
    private $bytes;

    /**
     * @param string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     * @throws InvalidArgumentException if the byte string does not represent a GUID
     * @throws InvalidArgumentException if the byte string does not contain a valid version
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

        if (!$this->isCorrectVariant()) {
            throw new InvalidArgumentException(
                'The byte string received does not conform to the RFC '
                . '4122 or Microsoft Corporation variants'
            );
        }

        if (!$this->isCorrectVersion()) {
            throw new InvalidArgumentException(
                'The byte string received does not contain a valid version'
            );
        }
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getTimeLow(): string
    {
        // Swap the bytes from little endian to network byte order.
        $hex = unpack(
            'H*',
            pack(
                'v*',
                hexdec(bin2hex(substr($this->bytes, 2, 2))),
                hexdec(bin2hex(substr($this->bytes, 0, 2)))
            )
        );

        return (string) ($hex[1] ?? '');
    }

    public function getTimeMid(): string
    {
        // Swap the bytes from little endian to network byte order.
        $hex = unpack(
            'H*',
            pack(
                'v',
                hexdec(bin2hex(substr($this->bytes, 4, 2)))
            )
        );

        return (string) ($hex[1] ?? '');
    }

    public function getTimeHiAndVersion(): string
    {
        // Swap the bytes from little endian to network byte order.
        $hex = unpack(
            'H*',
            pack(
                'v',
                hexdec(bin2hex(substr($this->bytes, 6, 2)))
            )
        );

        return (string) ($hex[1] ?? '');
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

    public function getVersion(): ?int
    {
        $parts = unpack('n*', $this->bytes);

        return ((int) $parts[4] >> 4) & 0x00f;
    }

    private function isCorrectVariant(): bool
    {
        if ($this->isNil()) {
            return true;
        }

        $variant = $this->getVariant();

        return $variant === Uuid::RFC_4122 || $variant === Uuid::RESERVED_MICROSOFT;
    }
}
