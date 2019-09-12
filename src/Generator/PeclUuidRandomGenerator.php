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
 * PeclUuidRandomGenerator provides functionality to generate strings of random
 * binary data using the PECL UUID PHP extension
 *
 * @link https://pecl.php.net/package/uuid
 */
class PeclUuidRandomGenerator implements RandomGeneratorInterface
{
    /**
     * Generates a string of random binary data of the specified length
     *
     * @param int $length The number of bytes of random binary data to generate
     * @return string A binary string
     */
    public function generate(int $length): string
    {
        $uuid = uuid_create(UUID_TYPE_RANDOM);

        return uuid_parse($uuid);
    }
}
