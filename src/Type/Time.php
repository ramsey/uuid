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

use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function sprintf;

/**
 * A value object representing a timestamp
 *
 * This class exists for type-safety purposes, to ensure that timestamps used
 * by ramsey/uuid are truly timestamp integers and not some other kind of string
 * or integer.
 *
 * @psalm-immutable
 */
final class Time implements TypeInterface
{
    private readonly IntegerObject $seconds;
    private readonly IntegerObject $microseconds;

    public function __construct(
        int | float | string | IntegerObject $seconds,
        int | float | string | IntegerObject $microseconds = new IntegerObject(0),
    ) {
        $this->seconds = $seconds instanceof IntegerObject ? $seconds : new IntegerObject($seconds);
        $this->microseconds = $microseconds instanceof IntegerObject ? $microseconds : new IntegerObject($microseconds);
    }

    public function getSeconds(): IntegerObject
    {
        return $this->seconds;
    }

    public function getMicroseconds(): IntegerObject
    {
        return $this->microseconds;
    }

    /**
     * @return numeric-string
     */
    public function toString(): string
    {
        /** @var numeric-string */
        return $this->seconds->toString() . '.' . sprintf('%06s', $this->microseconds->toString());
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
        if (!isset($data['seconds']) || !isset($data['microseconds'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }

        assert(is_string($data['seconds']));
        assert(is_string($data['microseconds']));

        $this->seconds = new IntegerObject($data['seconds']);
        $this->microseconds = new IntegerObject($data['microseconds']);
    }
}
