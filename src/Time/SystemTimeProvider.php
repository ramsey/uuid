<?php

namespace Rhumsaa\Uuid\Time;

use Rhumsaa\Uuid\TimeProvider;

class SystemTimeProvider implements TimeProvider
{
    public function currentTime()
    {
        return gettimeofday();
    }
}
