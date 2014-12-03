<?php

namespace Rhumsaa\Uuid\Provider\Time;

use Rhumsaa\Uuid\Provider\TimeProviderInterface;

class SystemTimeProvider implements TimeProviderInterface
{
    public function currentTime()
    {
        return gettimeofday();
    }
}
