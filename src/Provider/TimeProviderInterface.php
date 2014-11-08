<?php

namespace Rhumsaa\Uuid\Provider;

interface TimeProviderInterface
{
    /**
     * @return string[] Array guaranteed to contain "sec" and "usec" components of current timestamp.
     */
    public function currentTime();
}
