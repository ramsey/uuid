<?php

namespace Rhumsaa\Uuid\Generator;

use Rhumsaa\Uuid\RandomGeneratorInterface;

class OpenSslGenerator implements RandomGeneratorInterface
{

    public function generate($length)
    {
        return openssl_random_pseudo_bytes($length);
    }
}
