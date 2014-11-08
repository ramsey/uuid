<?php

namespace Rhumsaa\Uuid;

interface TimeProvider
{
    /**
     * @return string[] Array guaranteed to contain "sec" and "usec" components of current timestamp.
     */
    public function currentTime();
}
