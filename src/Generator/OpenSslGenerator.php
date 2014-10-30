<?php

namespace Rhumsaa\Uuid\Generator;

use Rhumsaa\Uuid\RandomGenerator;

class OpenSslGenerator implements RandomGenerator
{

    public function generate($length)
    {
        return openssl_random_pseudo_bytes($length);
    }
}
