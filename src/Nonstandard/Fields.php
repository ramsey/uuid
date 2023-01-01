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
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Type\Hexadecimal;

use function bin2hex;
use function dechex;
use function hexdec;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

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
     * @param non-empty-string $bytes A 16-byte binary string representation of a UUID
     *
     * @throws InvalidArgumentException if the byte string is not exactly 16 bytes
     */
    public function __construct(private readonly string $bytes)
    {
        if (strlen($this->bytes) !== 16) {
            throw new InvalidArgumentException(
                'The byte string must be 16 bytes long; '
                . 'received ' . strlen($this->bytes) . ' bytes'
            );
        }
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }

    public function getClockSeq(): Hexadecimal
    {
        $clockSeq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
        $clockSeqHex = str_pad(dechex($clockSeq), 4, '0', STR_PAD_LEFT);

        return new Hexadecimal($clockSeqHex);
    }

    public function getClockSeqHiAndReserved(): Hexadecimal
    {
        /** @var non-empty-string $clockSeqHiAndReserved */
        $clockSeqHiAndReserved = bin2hex(substr($this->bytes, 8, 1));

        return new Hexadecimal($clockSeqHiAndReserved);
    }

    public function getClockSeqLow(): Hexadecimal
    {
        /** @var non-empty-string $clockSeqLow */
        $clockSeqLow = bin2hex(substr($this->bytes, 9, 1));

        return new Hexadecimal($clockSeqLow);
    }

    public function getNode(): Hexadecimal
    {
        /** @var non-empty-string $node */
        $node = bin2hex(substr($this->bytes, 10));

        return new Hexadecimal($node);
    }

    public function getTimeHiAndVersion(): Hexadecimal
    {
        /** @var non-empty-string $timeHiAndVersion */
        $timeHiAndVersion = bin2hex(substr($this->bytes, 6, 2));

        return new Hexadecimal($timeHiAndVersion);
    }

    public function getTimeLow(): Hexadecimal
    {
        /** @var non-empty-string $timeLow */
        $timeLow = bin2hex(substr($this->bytes, 0, 4));

        return new Hexadecimal($timeLow);
    }

    public function getTimeMid(): Hexadecimal
    {
        /** @var non-empty-string $timeMid */
        $timeMid = bin2hex(substr($this->bytes, 4, 2));

        return new Hexadecimal($timeMid);
    }

    public function getTimestamp(): Hexadecimal
    {
        /** @var non-empty-string $timestamp */
        $timestamp = sprintf(
            '%03x%04s%08s',
            hexdec($this->getTimeHiAndVersion()->toString()) & 0x0fff,
            $this->getTimeMid()->toString(),
            $this->getTimeLow()->toString()
        );

        return new Hexadecimal($timestamp);
    }

    public function getVersion(): ?Version
    {
        return null;
    }

    public function isNil(): bool
    {
        return false;
    }

    public function isMax(): bool
    {
        return false;
    }
}
