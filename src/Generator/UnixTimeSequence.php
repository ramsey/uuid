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

namespace Ramsey\Uuid\Generator;

/**
 * The timestamp and counter that UnixTimeGenerator last generated, kept so the next ones can be greater
 *
 * @internal
 */
final class UnixTimeSequence
{
    /**
     * @param string $time The count of milliseconds since the Unix Epoch, in decimal digits
     * @param int $fraction The fraction of the millisecond, from 0 through 4095
     * @param int $high The first 31 bits of the 62-bit counter
     * @param int $low The last 31 bits of the 62-bit counter
     * @param int | false $processId The identifier of the process that last changed these values
     * @param string | null $givenTime For a date-time passed by a caller, its count of milliseconds
     * @param int | null $givenFraction For a date-time passed by a caller, its fraction of the millisecond
     */
    public function __construct(
        public string $time,
        public int $fraction,
        public int $high,
        public int $low,
        public int | false $processId,
        public ?string $givenTime = null,
        public ?int $givenFraction = null,
    ) {
    }
}
