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

namespace Ramsey\Uuid\Fields;

/**
 * UUIDs are comprised of unsigned integers, the bytes of which are separated
 * into fields and arranged in a particular layout defined by the specification
 * for the variant
 *
 * @psalm-immutable
 */
interface FieldsInterface
{
    /**
     * @return mixed[]
     */
    public function __serialize(): array;

    /**
     * @param mixed[] $data
     */
    public function __unserialize(array $data): void;

    /**
     * Returns the bytes that comprise the fields
     *
     * @return non-empty-string
     */
    public function getBytes(): string;
}
