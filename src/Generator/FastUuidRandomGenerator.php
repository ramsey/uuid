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

use Ramsey\Uuid\Exception\RandomSourceException;
use Throwable;

use function fast_uuid_random_bytes;
use function random_bytes;

/**
 * FastUuidRandomGenerator generates strings of random binary data using ext-fast_uuid
 *
 * @link https://github.com/iliaal/fast_uuid ext-fast_uuid
 */
class FastUuidRandomGenerator implements RandomGeneratorInterface
{
    /**
     * The largest length that fast_uuid_random_bytes() accepts
     */
    private const MAX_LENGTH = 16777216;

    /**
     * @throws RandomSourceException if the random source fails
     *
     * @inheritDoc
     */
    public function generate(int $length): string
    {
        try {
            if ($length > self::MAX_LENGTH) {
                return random_bytes($length);
            }

            return fast_uuid_random_bytes($length);
        } catch (Throwable $exception) {
            throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}
