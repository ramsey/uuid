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

namespace Ramsey\Uuid\Type;

use Brick\Math\BigDecimal;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function is_float;
use function is_int;
use function is_string;
use function json_decode;
use function json_encode;
use function sprintf;

/**
 * A value object representing a timestamp
 *
 * This class exists for type-safety purposes, to ensure that timestamps used by ramsey/uuid are truly timestamp
 * integers and not some other kind of string or integer.
 *
 * @immutable
 */
final class Time implements TypeInterface
{
    private IntegerObject $seconds;
    private IntegerObject $microseconds;

    public function __construct(
        IntegerObject | float | int | string $seconds,
        IntegerObject | float | int | string $microseconds = 0,
    ) {
        $this->seconds = new IntegerObject($seconds);
        $this->microseconds = new IntegerObject($microseconds);
    }

    /**
     * @pure
     */
    public function getSeconds(): IntegerObject
    {
        return $this->seconds;
    }

    /**
     * @pure
     */
    public function getMicroseconds(): IntegerObject
    {
        return $this->microseconds;
    }

    /**
     * Returns the time as a decimal number of seconds, with six digits after the decimal point
     *
     * The microseconds are added to the seconds, so a time of -2 seconds and 500000 microseconds is "-1.500000".
     */
    public function toString(): string
    {
        return (string) BigDecimal::of($this->seconds->toString())
            ->plus(BigDecimal::ofUnscaledValue($this->microseconds->toString(), 6));
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return [
            'seconds' => $this->getSeconds()->toString(),
            'microseconds' => $this->getMicroseconds()->toString(),
        ];
    }

    public function serialize(): string
    {
        return (string) json_encode($this);
    }

    /**
     * @return array{seconds: string, microseconds: string}
     */
    public function __serialize(): array
    {
        return [
            'seconds' => $this->getSeconds()->toString(),
            'microseconds' => $this->getMicroseconds()->toString(),
        ];
    }

    /**
     * Constructs the object from a serialized string representation
     *
     * @param string $data The serialized string representation of the object
     */
    public function unserialize(string $data): void
    {
        /** @var array{seconds?: float | int | string, microseconds?: float | int | string} $time */
        $time = json_decode($data, true);

        if (
            !self::isNumberOrString($time['seconds'] ?? null)
            || !self::isNumberOrString($time['microseconds'] ?? null)
        ) {
            throw new UnsupportedOperationException('Attempted to unserialize an invalid value');
        }

        $this->__construct($time['seconds'], $time['microseconds']);
    }

    /**
     * @param array{seconds?: string, microseconds?: string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!is_string($data['seconds'] ?? null) || !is_string($data['microseconds'] ?? null)) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd

        $this->__construct($data['seconds'], $data['microseconds']);
    }

    /**
     * @phpstan-assert-if-true float | int | string $value
     */
    private static function isNumberOrString(mixed $value): bool
    {
        return is_float($value) || is_int($value) || is_string($value);
    }
}
