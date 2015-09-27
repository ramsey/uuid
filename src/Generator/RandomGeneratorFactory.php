<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Generator;

/**
 * A factory for retrieving a random generator, based on the environment
 */
class RandomGeneratorFactory
{
    /**
     * For testing, `openssl_random_pseudo_bytes()` override; if `true`, treat as
     * if `openssl_random_pseudo_bytes()` is not available
     *
     * @var bool
     */
    public static $forceNoOpensslRandomPseudoBytes = false;

    /**
     * For testing, `random_bytes()` override; if `true`, treat as if `random_bytes()`
     * is not available.
     *
     * @var bool
     */
    public static $forceNoRandomBytes = false;

    /**
     * Returns `true` if the system has `openssl_random_pseudo_bytes()`
     *
     * @return bool
     */
    protected static function hasOpensslRandomPseudoBytes()
    {
        return (function_exists('openssl_random_pseudo_bytes') && !self::$forceNoOpensslRandomPseudoBytes);
    }

    /**
     * Returns `true` if the system has `random_bytes()`
     *
     * @return bool
     */
    protected static function hasRandomBytes()
    {
        return (function_exists('random_bytes') && !self::$forceNoRandomBytes);
    }

    /**
     * Returns a default random generator, based on the current environment
     *
     * @return RandomGeneratorInterface
     */
    public static function getGenerator()
    {
        if (self::hasRandomBytes()) {
            return new RandomBytesGenerator();
        }

        if (self::hasOpensslRandomPseudoBytes()) {
            return new OpenSslGenerator();
        }

        return new MtRandGenerator();
    }
}
