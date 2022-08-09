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

use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use ValueError;

use function json_decode;
use function json_encode;
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
    /**
     * @var IntegerObject
     */
    private $seconds;

    /**
     * @var IntegerObject
     */
    private $microseconds;

    /**
     * @param int|float|string|IntegerObject $seconds
     * @param int|float|string|IntegerObject $microseconds
     */
    public function __construct($seconds, $microseconds = 0)
    {
        $this->seconds = new IntegerObject($seconds);
        $this->microseconds = new IntegerObject($microseconds);
    }

    public function getSeconds(): IntegerObject
    {
        return $this->seconds;
    }

    public function getMicroseconds(): IntegerObject
    {
        return $this->microseconds;
    }

    public function toString(): string
    {
        return $this->seconds->toString() . '.' . $this->microseconds->toString();
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
     * @param string $serialized The serialized string representation of the object
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @psalm-suppress UnusedMethodCall
     */
    public function unserialize($serialized): void
    {
        /** @var array{seconds?: int|float|string, microseconds?: int|float|string} $time */
        $time = json_decode($serialized, true);

        if (!isset($time['seconds']) || !isset($time['microseconds'])) {
            throw new UnsupportedOperationException(
                'Attempted to unserialize an invalid value'
            );
        }

        $this->__construct($time['seconds'], $time['microseconds']);
    }

    /**
     * @param array{seconds?: string, microseconds?: string} $data
     */
    public function __unserialize(array $data): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($data['seconds']) || !isset($data['microseconds'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        // @codeCoverageIgnoreEnd

        $this->__construct($data['seconds'], $data['microseconds']);
    }
}
