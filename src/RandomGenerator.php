<?php

namespace Rhumsaa\Uuid;

interface RandomGenerator
{
    /**
     * @param integer $length
     *
     * @return string
     */
    function generate($length);
}
