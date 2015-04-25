<?php

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\RandomGeneratorInterface;

class MtRandGenerator implements RandomGeneratorInterface
{
    public function generate($length)
    {
        $bytes = '';

        for ($i = 1; $i <= $length; $i++) {
            $bytes = chr(mt_rand(0, 255)) . $bytes;
        }

        return $bytes;
    }
}
