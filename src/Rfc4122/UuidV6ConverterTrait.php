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

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Uuid;

/**
 * Provides functionality to convert between UuidV1 and UuidV6
 *
 * @psalm-immutable
 */
trait UuidV6ConverterTrait
{
    /**
     * Converts this UUID into an instance of a version 1 UUID
     */
    public function toUuidV1(): UuidV1
    {
        $hex = $this->getHex()->toString();
        $hex = substr($hex, 7, 5)
            . substr($hex, 13, 3)
            . substr($hex, 3, 4)
            . '1' . substr($hex, 0, 3)
            . substr($hex, 16);

        /** @var LazyUuidFromString $uuid */
        $uuid = Uuid::fromBytes((string) hex2bin($hex));

        return $uuid->toUuidV1();
    }

    /**
     * Converts a version 1 UUID into an instance of a version 6 UUID
     */
    public static function fromUuidV1(UuidV1 $uuidV1): UuidV6
    {
        $hex = $uuidV1->getHex()->toString();
        $hex = substr($hex, 13, 3)
            . substr($hex, 8, 4)
            . substr($hex, 0, 5)
            . '6' . substr($hex, 5, 3)
            . substr($hex, 16);

        /** @var LazyUuidFromString $uuid */
        $uuid = Uuid::fromBytes((string) hex2bin($hex));

        return $uuid->toUuidV6();
    }
}
