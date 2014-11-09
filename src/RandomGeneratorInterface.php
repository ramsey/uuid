<?php

namespace Rhumsaa\Uuid;

interface RandomGeneratorInterface
{
    /**
     * @param integer $length
     *
     * @return string
     */
    public function generate($length);
}
