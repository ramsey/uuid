<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;

class FixedTimeProvider implements TimeProviderInterface
{
    private $fixedTime;

    public function __construct(array $timestamp)
    {
        if (!array_key_exists('sec', $timestamp) || !array_key_exists('usec', $timestamp)) {
            throw new \InvalidArgumentException('Array must contain sec and usec keys.');
        }

        $this->fixedTime = $timestamp;
    }

    public function setUsec($value)
    {
        $this->fixedTime['usec'] = $value;
    }

    public function setSec($value)
    {
        $this->fixedTime['sec'] = $value;
    }

    public function currentTime()
    {
        return $this->fixedTime;
    }
}
