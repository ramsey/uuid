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

/**
 * A value object representing a timestamp
 *
 * This class exists for type-safety purposes, to ensure that timestamps used
 * by ramsey/uuid are truly timestamp integers and not some other kind of string
 * or integer.
 *
 * @psalm-immutable
 */
final class Time
{
    /**
     * @var IntegerValue
     */
    private $seconds;

    /**
     * @var IntegerValue
     */
    private $microSeconds;

    /**
     * @param mixed $seconds
     * @param mixed $microSeconds
     */
    public function __construct($seconds, $microSeconds = 0)
    {
        $this->seconds = new IntegerValue($seconds);
        $this->microSeconds = new IntegerValue($microSeconds);
    }

    public function getSeconds(): IntegerValue
    {
        return $this->seconds;
    }

    public function getMicroSeconds(): IntegerValue
    {
        return $this->microSeconds;
    }
}
