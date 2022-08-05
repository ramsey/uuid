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

namespace Ramsey\Uuid;

use DateTimeInterface;

/**
 * Time-based UUIDs are derived from a date/time value
 *
 * @psalm-immutable
 */
interface TimeBasedUuidInterface extends UuidInterface
{
    /**
     * Returns a date object representing the timestamp associated with the UUID
     */
    public function getDateTime(): DateTimeInterface;
}
