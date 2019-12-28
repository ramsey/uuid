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

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;

/**
 * DegradedUuid represents an RFC 4122 UUID on 32-bit systems
 *
 * Some of the functionality of a DegradedUuid is not present or degraded, since
 * 32-bit systems are unable to perform the necessary mathematical operations or
 * represent the integers appropriately.
 *
 * @psalm-immutable
 */
class DegradedUuid extends Uuid
{
    /**
     * @return DateTimeImmutable An immutable instance of DateTimeInterface
     *
     * @throws DateTimeException if DateTime throws an exception/error
     * @throws UnsatisfiedDependencyException if large integer support is not available
     * @throws UnsupportedOperationException if UUID is not time-based
     */
    public function getDateTime(): DateTimeInterface
    {
        if ($this->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        $time = $this->numberConverter->fromHex($this->getTimestampHex());
        $unixTime = $this->timeConverter->convertTime($time);

        try {
            return new DateTimeImmutable("@{$unixTime}");
        } catch (\Throwable $exception) {
            throw new DateTimeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws UnsatisfiedDependencyException if large integer support is not available
     *
     * @inheritDoc
     */
    public function getFields(): array
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since some '
            . 'values overflow the system max integer value'
            . '; consider calling getFieldsHex instead'
        );
    }

    /**
     * @throws UnsatisfiedDependencyException if large integer support is not available
     */
    public function getNode(): int
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since node '
            . 'is an unsigned 48-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getNodeHex instead'
        );
    }

    /**
     * @throws UnsatisfiedDependencyException if large integer support is not available
     */
    public function getTimeLow(): int
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since time_low '
            . 'is an unsigned 32-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getTimeLowHex instead'
        );
    }

    /**
     * @throws UnsatisfiedDependencyException if large integer support is not available
     * @throws UnsupportedOperationException if UUID is not time-based
     */
    public function getTimestamp(): int
    {
        if ($this->getVersion() !== 1) {
            throw new UnsupportedOperationException('Not a time-based UUID');
        }

        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' on a 32-bit system, since timestamp '
            . 'is an unsigned 60-bit integer and can overflow the system '
            . 'max integer value'
            . '; consider calling getTimestampHex instead'
        );
    }
}
