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

use InvalidArgumentException;
use Ramsey\Uuid\Converter\DependencyCheckTrait;
use Ramsey\Uuid\Converter\NumberStringTrait;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

/**
 * GmpTimeConverter provides facilities for converting parts of time into
 * representations that may be used in UUIDs
 */
class GmpTimeConverter implements TimeConverterInterface
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
        $this->checkGmpExtension();
        $this->checkIntegerString($seconds, 'seconds');
        $this->checkIntegerString($microSeconds, 'microSeconds');

        $sec = gmp_init($seconds);
        $sec = gmp_mul($sec, gmp_init(10000000));

        $usec = gmp_init($microSeconds);
        $usec = gmp_mul($usec, gmp_init(10));

        $uuidTime = gmp_add($sec, $usec);
        $uuidTime = gmp_add($uuidTime, gmp_init('122192928000000000'));

        $uuidTimeHex = sprintf('%016s', gmp_strval($uuidTime, 16));

        return [
            'low' => substr($uuidTimeHex, 8),
            'mid' => substr($uuidTimeHex, 4, 4),
            'hi' => substr($uuidTimeHex, 0, 4),
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
        $this->checkGmpExtension();
        $this->checkIntegerString($timestamp, 'timestamp');

        $timestamp = gmp_init($timestamp);
        $timestamp = gmp_sub($timestamp, gmp_init('122192928000000000'));
        $d = gmp_init('10000000');
        [$q, $r] = gmp_div_qr($timestamp, $d);

        // If $r >= $d/2, we have to round up
        $sign = gmp_sign(gmp_sub($d, gmp_add($r, $r)));
        if ($sign <= 0) {
            $q = gmp_add($q, gmp_init(1));
        }

        return gmp_strval($q);
    }
}
