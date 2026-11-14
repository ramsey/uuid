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

use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\Exception\DateTimeException;

use function implode;
use function sprintf;
use function str_pad;

use const STR_PAD_LEFT;

/**
 * Provides common functionality for getting the time from a time-based UUID
 *
 * @immutable
 */
trait TimeTrait
{
    /**
     * Returns a DateTimeInterface object representing the timestamp associated with the UUID
     *
     * @return DateTimeImmutable A PHP DateTimeImmutable instance representing the timestamp of a time-based UUID
     */
    public function getDateTime(): DateTimeInterface
    {
        $time = $this->timeConverter->convertTime($this->fields->getTimestamp());

        // The seconds are rounded down and the microseconds are never negative, so read them as two fields, not as
        // one decimal.
        $timeString = $time->getSeconds()->toString()
            . ' '
            . str_pad($time->getMicroseconds()->toString(), 6, '0', STR_PAD_LEFT);

        $dateTime = DateTimeImmutable::createFromFormat('U u', $timeString);

        if ($dateTime === false) {
            $errors = DateTimeImmutable::getLastErrors();

            throw new DateTimeException(sprintf(
                'Unable to create a date from the time "%s": %s',
                $timeString,
                $errors === false ? 'unknown error' : implode('; ', $errors['errors']),
            ));
        }

        return $dateTime;
    }
}
