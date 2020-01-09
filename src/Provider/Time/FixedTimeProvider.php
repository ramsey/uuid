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
use Ramsey\Uuid\Type\Time;

/**
 * FixedTimeProvider uses an known time to provide the time
 *
 * This provider allows the use of a previously-generated, or known, time
 * when generating time-based UUIDs.
 */
class FixedTimeProvider implements TimeProviderInterface
{
    /**
     * @var Time
     */
    private $fixedTime;

    /**
     * @param int[]|string[]|Time $time An array containing 'sec' and
     *     'usec' keys or a Time object
     *
     * @throws InvalidArgumentException if the `$time` does not contain
     *     `sec` or `usec` components
     */
    public function __construct($time)
    {
        if (!$time instanceof Time) {
            $time = $this->convertToTime($time);
        }

        $this->fixedTime = $time;
    }

    /**
     * Sets the `usec` component of the time
     *
     * @param int|string|Time $value The `usec` value to set
     */
    public function setUsec($value): void
    {
        $this->fixedTime = new Time($this->fixedTime->getSeconds(), $value);
    }

    /**
     * Sets the `sec` component of the time
     *
     * @param int|string|Time $value The `sec` value to set
     */
    public function setSec($value): void
    {
        $this->fixedTime = new Time($value, $this->fixedTime->getMicroSeconds());
    }

    /**
     * @deprecated Transition to {@see FixedTimeProvider::getTime()}
     *
     * @inheritDoc
     */
    public function currentTime(): array
    {
        return [
            'sec' => $this->fixedTime->getSeconds()->toString(),
            'usec' => $this->fixedTime->getMicroSeconds()->toString(),
        ];
    }

    public function getTime(): Time
    {
        return $this->fixedTime;
    }

    /**
     * @param int[]|string[] $time
     *
     * @return Time A time created from the provided array
     *
     * @throws InvalidArgumentException if the `$time` does not contain
     *     `sec` or `usec` components
     */
    private function convertToTime(array $time): Time
    {
        if (!array_key_exists('sec', $time) || !array_key_exists('usec', $time)) {
            throw new InvalidArgumentException('Array must contain sec and usec keys.');
        }

        return new Time($time['sec'], $time['usec']);
    }
}
