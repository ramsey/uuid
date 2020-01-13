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

use Ramsey\Uuid\Uuid;

/**
 * Version 3 UUIDs are named-based, using combination of a namespace and name
 * that are hashed into a 128-bit unsigned integer using MD5
 *
 * @psalm-immutable
 */
final class UuidV3 extends Uuid implements UuidInterface
{
}
