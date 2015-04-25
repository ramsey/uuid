<?php

namespace Ramsey\Uuid;

interface RandomGeneratorInterface
{
    /**
     * @param integer $length
     *
     * @return string
     */
    public function generate($length);
}
