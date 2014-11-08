<?php

namespace Rhumsaa\Uuid;

interface RandomGenerator
{
    /**
     * @param integer $length
     *
     * @return string
     */
    public function generate($length);
}
