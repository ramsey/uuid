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

namespace Ramsey\Uuid;

use Ramsey\Uuid\Generator\OpenSslGenerator;
use Ramsey\Uuid\Generator\MtRandGenerator;

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
