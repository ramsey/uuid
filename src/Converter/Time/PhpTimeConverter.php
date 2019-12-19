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

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * PhpTimeConverter uses built-in PHP functions and standard math operations
 * available to the PHP programming language to provide facilities for
 * converting parts of time into representations that may be used in UUIDs
 */
class PhpTimeConverter implements TimeConverterInterface
{
    use DependencyCheckTrait;
    use NumberStringTrait;

    /**
     * @throws InvalidArgumentException if $seconds or $microseconds are not integer strings
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     */
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        $this->check64BitPhp();
        $this->checkIntegerString($seconds, 'seconds');
        $this->checkIntegerString($microSeconds, 'microSeconds');

        // 0x01b21dd213814000 is the number of 100-ns intervals between the
        // UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
        $uuidTime = ((int) $seconds * 10000000) + ((int) $microSeconds * 10) + 0x01b21dd213814000;

        return [
            'low' => sprintf('%08x', $uuidTime & 0xffffffff),
            'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
        ];
    }

    /**
     * @throws InvalidArgumentException if $timestamp is not an integer string
     * @throws UnsatisfiedDependencyException if the chosen converter is not present
     *
     * @inheritDoc
     */
    public function convertTime(string $timestamp): string
    {
        $this->check64BitPhp();
        $this->checkIntegerString($timestamp, 'timestamp');

        $unixTime = ((int) $timestamp - 0x01b21dd213814000) / 1e7;

        return number_format($unixTime, 0, '', '');
    }
}
