<?php

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;

class FixedTimeProvider implements TimeProviderInterface
{
    private $fixedTime;

    public function __construct(array $timestamp)
    {
        if (! array_key_exists('sec', $timestamp) || ! array_key_exists('usec', $timestamp)) {
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
