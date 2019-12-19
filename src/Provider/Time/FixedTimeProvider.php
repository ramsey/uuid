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

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\TimeProviderInterface;

/**
 * FixedTimeProvider uses an known timestamp to provide the time
 *
 * This provider allows the use of a previously-generated, or known, timestamp
 * when generating time-based UUIDs.
 */
class FixedTimeProvider implements TimeProviderInterface
{
    /**
     * @var int[] Array containing `sec` and `usec` components of a timestamp
     */
    private $fixedTime;

    /**
     * @param int[] $timestamp Array containing `sec` and `usec` components of
     *     a timestamp
     *
     * @throws InvalidArgumentException if the `$timestamp` does not contain
     *     `sec` or `usec` components
     */
    public function __construct(array $timestamp)
    {
        if (!array_key_exists('sec', $timestamp) || !array_key_exists('usec', $timestamp)) {
            throw new InvalidArgumentException('Array must contain sec and usec keys.');
        }

        $this->fixedTime = $timestamp;
    }

    /**
     * Sets the `usec` component of the timestamp
     *
     * @param int $value The `usec` value to set
     */
    public function setUsec(int $value): void
    {
        $this->fixedTime['usec'] = $value;
    }

    /**
     * Sets the `sec` component of the timestamp
     *
     * @param int $value The `sec` value to set
     */
    public function setSec(int $value): void
    {
        $this->fixedTime['sec'] = $value;
    }

    /**
     * @inheritDoc
     */
    public function currentTime(): array
    {
        return $this->fixedTime;
    }
}
