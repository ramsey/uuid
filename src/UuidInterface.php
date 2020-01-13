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

namespace Ramsey\Uuid;

use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Fields\FieldsInterface;
use Serializable;

/**
 * A UUID is a universally unique identifier adhering to an agreed-upon
 * representation format and standard for generation
 *
 * @psalm-immutable
 */
interface UuidInterface extends JsonSerializable, Serializable
{
    /**
     * Returns -1, 0, or 1 if the UUID is less than, equal to, or greater than
     * the other UUID
     *
     * The first of two UUIDs is greater than the second if the most
     * significant field in which the UUIDs differ is greater for the first
     * UUID.
     *
     * * Q. What's the value of being able to sort UUIDs?
     * * A. Use them as keys in a B-Tree or similar mapping.
     *
     * @param UuidInterface $other The UUID to compare
     *
     * @return int -1, 0, or 1 if the UUID is less than, equal to, or greater than $other
     */
    public function compareTo(UuidInterface $other): int;

    /**
     * Returns true if the UUID is equal to the provided object
     *
     * The result is true if and only if the argument is not null, is a UUID
     * object, has the same variant, and contains the same value, bit for bit,
     * as the UUID.
     *
     * @param object|null $other An object to test for equality with this UUID
     *
     * @return bool True if the other object is equal to this UUID
     */
    public function equals(?object $other): bool;

    /**
     * Returns the binary string representation of the UUID
     */
    public function getBytes(): string;

    /**
     * Returns the fields that comprise this UUID
     */
    public function getFields(): FieldsInterface;

    /**
     * @deprecated This method will be removed in 5.0.0. There is no alternative
     *     recommendation, so plan accordingly.
     */
    public function getNumberConverter(): NumberConverterInterface;

    /**
     * Returns the hexadecimal string representation of the UUID
     */
    public function getHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance.
     *
     * @return string[]
     */
    public function getFieldsHex(): array;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqHiAndReserved()}.
     */
    public function getClockSeqHiAndReservedHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeqLow()}.
     */
    public function getClockSeqLowHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getClockSeq()}.
     */
    public function getClockSequenceHex(): string;

    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed from the
     *     interface. It is available at {@see UuidV1::getDateTime()}.
     */
    public function getDateTime(): DateTimeInterface;

    /**
     * Returns the integer value of the UUID as a string
     */
    public function getInteger(): string;

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct
     *     alternative, but the same information may be obtained by splitting
     *     in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getLeastSignificantBitsHex(): string;

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct
     *     alternative, but the same information may be obtained by splitting
     *     in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getMostSignificantBitsHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getNode()}.
     */
    public function getNodeHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeHiAndVersion()}.
     */
    public function getTimeHiAndVersionHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeLow()}.
     */
    public function getTimeLowHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimeMid()}.
     */
    public function getTimeMidHex(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getTimestamp()}.
     */
    public function getTimestampHex(): string;

    /**
     * @deprecated In ramsey/uuid version 5.0.0, this will be removed from this
     *     interface. It has moved to {@see \Ramsey\Uuid\Rfc4122\UuidInterface::getUrn()}.
     */
    public function getUrn(): string;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVariant()}.
     */
    public function getVariant(): ?int;

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface} instance, you may call
     *     {@see \Ramsey\Uuid\Rfc4122\FieldsInterface::getVersion()}.
     */
    public function getVersion(): ?int;

    /**
     * Returns a string representation of the UUID
     */
    public function toString(): string;

    /**
     * Casts the UUID to a string representation
     */
    public function __toString(): string;
}
