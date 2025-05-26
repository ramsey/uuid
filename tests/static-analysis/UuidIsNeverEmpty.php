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

namespace Ramsey\Uuid\StaticAnalysis;

use Ramsey\Uuid\UuidInterface;

/**
 * This is a static analysis fixture to verify that the API signature
 * of a UUID does not return empty strings for methods that never will do so.
 */
final class UuidIsNeverEmpty
{
    /** @return non-empty-string */
    public function bytesAreNeverEmpty(UuidInterface $uuid): string
    {
        return $uuid->getBytes();
    }

    /** @return non-empty-string */
    public function stringIsNeverEmpty(UuidInterface $uuid): string
    {
        return $uuid->toString();
    }
}
