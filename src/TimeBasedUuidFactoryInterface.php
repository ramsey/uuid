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
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * TimeBasedUuidFactoryInterface defines common functionality all factories for
 * time-based UUIDs must implement
 *
 * @psalm-immutable
 */
interface TimeBasedUuidFactoryInterface extends UuidFactoryInterface
{
    /**
     * Creates a UUID from a DateTimeInterface instance
     *
     * @param DateTimeInterface $dateTime The date and time
     * @param Hexadecimal|null $node A 48-bit number representing the hardware
     *     address
     * @param int<0, 16383>|null $clockSeq A 14-bit number used to help avoid
     *     duplicates that could arise when the clock is set backwards in time
     *     or if the node ID changes
     *
     * @return UuidInterface A UuidInterface instance that represents a
     *     UUID created from a DateTimeInterface instance
     *
     * @psalm-pure
     */
    public function fromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): UuidInterface;
}
