<?php

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;

class SystemTimeProvider implements TimeProviderInterface
{
    public function currentTime()
    {
        return gettimeofday();
    }
}
