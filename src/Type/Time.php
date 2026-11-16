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
use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function is_string;
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
    private readonly IntegerObject $seconds;
    private readonly IntegerObject $microseconds;

    public function __construct(
        IntegerObject | float | int | string $seconds,
        IntegerObject | float | int | string $microseconds = new IntegerObject(0),
    ) {
        $this->seconds = $seconds instanceof IntegerObject ? $seconds : new IntegerObject($seconds);
        $this->microseconds = $microseconds instanceof IntegerObject ? $microseconds : new IntegerObject($microseconds);
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
     *
     * @return numeric-string
     */
    public function toString(): string
    {
        /** @var non-empty-string&numeric-string */
        return (string) BigDecimal::of($this->seconds->toString())
            ->plus(BigDecimal::ofUnscaledValue($this->microseconds->toString(), 6));
    }

    /**
     * @return numeric-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return array{seconds: string, microseconds: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'seconds' => $this->getSeconds()->toString(),
            'microseconds' => $this->getMicroseconds()->toString(),
        ];
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
     * @inheritDoc
     */
    public function __unserialize(array $data): void
    {
        if (!is_string($data['seconds'] ?? null) || !is_string($data['microseconds'] ?? null)) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }

        $this->seconds = new IntegerObject($data['seconds']);
        $this->microseconds = new IntegerObject($data['microseconds']);
    }
}
