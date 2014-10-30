<?php

namespace Rhumsaa\Uuid;

use Rhumsaa\Uuid\Generator\OpenSslGenerator;
use Rhumsaa\Uuid\Generator\MtRandGenerator;

class RandomGeneratorFactory
{
    /**
     * For testing, openssl_random_pseudo_bytes() override; if true, treat as
     * if openssl_random_pseudo_bytes() is not available
     *
     * @var bool
     */
    public static $forceNoOpensslRandomPseudoBytes = false;

    /**
     * Returns true if the system has openssl_random_pseudo_bytes()
     *
     * @return bool
     */
    protected static function hasOpensslRandomPseudoBytes()
    {
        return (function_exists('openssl_random_pseudo_bytes') && !self::$forceNoOpensslRandomPseudoBytes);
    }

    public static function getGenerator()
    {
        if (self::hasOpensslRandomPseudoBytes()) {
            return new OpenSslGenerator();
        }

        return new MtRandGenerator();
    }
}
