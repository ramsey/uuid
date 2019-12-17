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

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * A UUID builder builds instances of UuidInterface
 */
interface UuidBuilderInterface
{
    /**
     * Builds and returns a UuidInterface
     *
     * @param CodecInterface $codec The codec to use for building this UuidInterface instance
     * @param string[] $fields An array of fields from which to construct a UuidInterface instance;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     *
     * @return UuidInterface Implementations may choose to return more specific
     *     instances of UUIDs that implement UuidInterface
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface;
}
