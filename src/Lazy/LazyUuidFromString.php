<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license   http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Lazy;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Nonstandard\UuidV6;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function hex2bin;
use function str_replace;

/**
 * Lazy version of a UUID: its format has not been determined yet, so it is mostly only usable for string/bytes
 * conversion. This object optimizes instantiation, serialization and string conversion time, at the cost of
 * increased overhead for more advanced UUID operations.
 *
 * @psalm-immutable
 *
 * @internal this type is used internally for performance reasons, and is not supposed to be directly referenced
 *           in consumer libraries.
 */
final class LazyUuidFromString implements UuidInterface
{
    public const VALID_PATTERN = '/\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z/ms';

    /** @var non-empty-string */
    private $uuid;

    /** @var non-empty-string */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function serialize(): string
    {
        return $this->uuid;
    }

    public function unserialize($serialized): void
    {
        $this->uuid = $serialized;
    }

    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->unwrap()
            ->getNumberConverter();
    }

    public function getFieldsHex(): array
    {
        return $this->unwrap()
            ->getFieldsHex();
    }

    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->unwrap()
            ->getClockSeqHiAndReservedHex();
    }

    public function getClockSeqLowHex(): string
    {
        return $this->unwrap()
            ->getClockSeqLowHex();
    }

    public function getClockSequenceHex(): string
    {
        return $this->unwrap()
            ->getClockSequenceHex();
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->unwrap()
            ->getDateTime();
    }

    public function getLeastSignificantBitsHex(): string
    {
        return $this->unwrap()
            ->getLeastSignificantBitsHex();
    }

    public function getMostSignificantBitsHex(): string
    {
        return $this->unwrap()
            ->getMostSignificantBitsHex();
    }

    public function getNodeHex(): string
    {
        return $this->unwrap()
            ->getNodeHex();
    }

    public function getTimeHiAndVersionHex(): string
    {
        return $this->unwrap()
            ->getTimeHiAndVersionHex();
    }

    public function getTimeLowHex(): string
    {
        return $this->unwrap()
            ->getTimeLowHex();
    }

    public function getTimeMidHex(): string
    {
        return $this->unwrap()
            ->getTimeMidHex();
    }

    public function getTimestampHex(): string
    {
        return $this->unwrap()
            ->getTimestampHex();
    }

    public function getUrn(): string
    {
        return $this->unwrap()
            ->getUrn();
    }

    public function getVariant(): ?int
    {
        return $this->unwrap()
            ->getVariant();
    }

    public function getVersion(): ?int
    {
        return $this->unwrap()
            ->getVersion();
    }

    public function compareTo(UuidInterface $other): int
    {
        return $this->unwrap()
            ->compareTo($other);
    }

    public function equals(?object $other): bool
    {
        if (! $other instanceof UuidInterface) {
            return false;
        }

        return $this->uuid === $other->toString();
    }

    public function getBytes(): string
    {
        return hex2bin(str_replace('-', '', $this->uuid));
    }

    public function getFields(): FieldsInterface
    {
        return $this->unwrap()
            ->getFields();
    }

    public function getHex(): Hexadecimal
    {
        return $this->unwrap()
            ->getHex();
    }

    public function getInteger(): IntegerObject
    {
        return $this->unwrap()
            ->getInteger();
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqHiAndReserved()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSeqHiAndReserved(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getClockSeqHiAndReserved()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeqLow()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSeqLow(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getClockSeqLow()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getClockSeq()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getClockSequence(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getClockSeq()
                    ->toString()
            );
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct
     *     alternative, but the same information may be obtained by splitting
     *     in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getLeastSignificantBits(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(substr($instance->getHex()->toString(), 16));
    }

    /**
     * @deprecated This method will be removed in 5.0.0. There is no direct
     *     alternative, but the same information may be obtained by splitting
     *     in half the value returned by {@see UuidInterface::getHex()}.
     */
    public function getMostSignificantBits(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(substr($instance->getHex()->toString(), 0, 16));
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getNode()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getNode(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getNode()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeHiAndVersion()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeHiAndVersion(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getTimeHiAndVersion()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeLow()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeLow(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getTimeLow()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimeMid()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimeMid(): string
    {
        $instance = $this->unwrap();

        return $instance->getNumberConverter()
            ->fromHex(
                $instance->getFields()
                    ->getTimeMid()
                    ->toString()
            );
    }

    /**
     * @deprecated Use {@see UuidInterface::getFields()} to get a
     *     {@see FieldsInterface} instance. If it is a {@see Rfc4122FieldsInterface}
     *     instance, you may call {@see Rfc4122FieldsInterface::getTimestamp()}
     *     and use the arbitrary-precision math library of your choice to
     *     convert it to a string integer.
     */
    public function getTimestamp(): string
    {
        $instance = $this->unwrap();
        $fields   = $instance->getFields();

        if ($fields->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        return $instance->getNumberConverter()
            ->fromHex($fields->getTimestamp()->toString());
    }

    public function toUuidV1(): UuidV1
    {
        $instance = $this->unwrap();

        if ($instance instanceof UuidV1) {
            return $instance;
        }

        assert($instance instanceof UuidV6);

        return $instance->toUuidV1();
    }

    private function unwrap(): UuidInterface
    {
        return Uuid::getFactory()
            ->fromString($this->uuid);
    }
}
