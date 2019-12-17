<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

/**
 * RandomBytesGenerator generates strings of random binary data using the
 * built-in `random_bytes()` PHP function
 *
 * @link http://php.net/random_bytes random_bytes()
 */
class RandomBytesGenerator implements RandomGeneratorInterface
{
    public function generate(int $length): string
    {
        return random_bytes($length);
    }
}
