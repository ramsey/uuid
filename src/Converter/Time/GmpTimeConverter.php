<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;

/**
 * BigNumberTimeConverter provides facilities for converting parts of time into representations that may
 * be used in UUIDs
 */
class GmpTimeConverter implements TimeConverterInterface
{
    /**
     * Uses the provided seconds and micro-seconds to calculate the time_low,
     * time_mid, and time_high fields used by RFC 4122 version 1 UUIDs
     *
     * @param string $seconds
     * @param string $microSeconds
     * @return string[] An array containing `low`, `mid`, and `high` keys
     * @link http://tools.ietf.org/html/rfc4122#section-4.2.2
     */
    public function calculateTime($seconds, $microSeconds)
    {
        $sec = gmp_init($seconds);
        $sec = gmp_mul($sec, gmp_init(10000000));

        $usec = gmp_init($microSeconds);
        $usec = gmp_mul($usec, gmp_init(10));

        $uuidTime = gmp_add($sec, $usec);
        $uuidTime = gmp_add($uuidTime, gmp_init('122192928000000000'));

        $uuidTimeHex = sprintf('%016s', gmp_strval($uuidTime, 16));

        return array(
            'low' => substr($uuidTimeHex, 8),
            'mid' => substr($uuidTimeHex, 4, 4),
            'hi' => substr($uuidTimeHex, 0, 4),
        );
    }

    /**
     * Converts a timestamp extracted from a UUID to a unix timestamp
     * @param mixed $timestamp - an integer, string or a GMP object
     * @return string
     */
    public function convertTime($timestamp)
    {
        if (!$timestamp instanceof \GMP) {
            $timestamp = gmp_init($timestamp);
        }

        $timestamp = gmp_sub($timestamp, gmp_init('122192928000000000'));
        $d = gmp_init('10000000');
        list($q, $r) = gmp_div_qr($timestamp, $d);

        //If $r >= $d/2, we have to round up
        $sign = gmp_sign(gmp_sub($d, gmp_add($r, $r)));
        if ($sign <= 0) {
            $q = gmp_add($q, gmp_init(1));
        }

        return gmp_strval($q);
    }
}
