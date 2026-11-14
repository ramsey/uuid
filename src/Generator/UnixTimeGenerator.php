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

namespace Ramsey\Uuid\Generator;

use Brick\Math\BigInteger;
use DateTimeInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Exception\TimeSourceException;
use Ramsey\Uuid\Type\Hexadecimal;

use function function_exists;
use function hash;
use function intdiv;
use function pack;
use function str_pad;
use function str_starts_with;
use function strcmp;
use function strlen;
use function substr;
use function substr_replace;
use function unpack;

use const PHP_INT_SIZE;
use const STR_PAD_LEFT;

/**
 * UnixTimeGenerator generates bytes, combining a 48-bit timestamp in milliseconds since the Unix Epoch with a 12-bit
 * fraction of the millisecond and a 62-bit counter that starts at a random value
 *
 * The fraction follows RFC 9562 section 6.2, method 3: the part of the time below one millisecond, multiplied by 4096
 * and rounded down. The counter follows method 2 of the same section. Bytes generated in one process without a
 * date-time always increase, and so do bytes generated for the same date-time in consecutive calls.
 *
 * The four bits before the fraction and the two bits before the counter are zero. They are where a version 7 UUID has
 * its version and variant.
 *
 * Code and concepts within this class are borrowed from the symfony/uid package and are used under the terms of the MIT
 * license distributed with symfony/uid.
 *
 * symfony/uid is copyright (c) Fabien Potencier.
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-6.2 RFC 9562, 6.2. Monotonicity and Counters
 * @link https://symfony.com/components/Uid Symfony Uid component
 * @link https://github.com/symfony/uid/blob/4f9f537e57261519808a7ce1d941490736522bbc/UuidV7.php Symfony UuidV7 class
 * @link https://github.com/symfony/uid/blob/6.2/LICENSE MIT License
 */
class UnixTimeGenerator implements TimeGeneratorInterface
{
    /**
     * The largest count of milliseconds since the Unix Epoch that fits in the 48-bit timestamp.
     */
    private const MAX_TIME = '281474976710655';

    /**
     * The largest value of the 12-bit fraction of a millisecond.
     */
    private const MAX_FRACTION = 0x0fff;

    /**
     * The largest value of each 31-bit half of the 62-bit counter.
     */
    private const MAX_HALF = 0x7fffffff;

    /**
     * The last timestamp and counter generated without a date-time.
     */
    private static ?UnixTimeSequence $clockSequence = null;

    /**
     * The last timestamp and counter generated for a date-time, with the date-time they were generated for.
     */
    private static ?UnixTimeSequence $datedSequence = null;

    /**
     * Whether PHP can return the identifier of the current process; some hosts disable the function that does.
     */
    private static ?bool $readsProcessId = null;

    private static ?string $seed = null;
    private static int | false $seedProcessId = false;
    private static int $seedIndex = 0;

    /** @var int[] */
    private static array $seedParts = [];

    public function __construct(
        private RandomGeneratorInterface $randomGenerator,
        private int $intSize = PHP_INT_SIZE,
    ) {
        self::$readsProcessId ??= function_exists('getmypid');
    }

    /**
     * @param Hexadecimal | int | string | null $node Unused in this generator
     * @param int | null $clockSeq Unused in this generator
     * @param DateTimeInterface | null $dateTime A date-time instance to use when generating bytes
     */
    public function generate($node = null, ?int $clockSeq = null, ?DateTimeInterface $dateTime = null): string
    {
        if ($dateTime === null) {
            $sequence = $this->nextForClock();
        } else {
            $sequence = $this->nextForDateTime($dateTime);
        }

        if ($this->intSize >= 8) {
            $time = substr(pack('J', (int) $sequence->time), -6);
        } else {
            $time = str_pad(BigInteger::of($sequence->time)->toBytes(false), 6, "\x00", STR_PAD_LEFT);
        }

        // The counter is 62 bits long: the high half, then the low half. The first number packed is its first 30 bits
        // and the second is its last 32. On a 32-bit build the shifted bit is the sign bit, which packs as bit 31.
        return $time . pack(
            'nNN',
            $sequence->fraction,
            $sequence->high >> 1,
            (($sequence->high & 1) << 31) | $sequence->low,
        );
    }

    /**
     * Returns the timestamp and counter for the current time
     *
     * When the clock has not passed the last millisecond used, whether it stands still or has moved backward, the last
     * millisecond is used again and the counter increases.
     */
    private function nextForClock(): UnixTimeSequence
    {
        // The value looks like "0.12345600 1700000000": the fraction of the second, then the seconds.
        $now = microtime(false);
        $time = substr($now, 11) . substr($now, 2, 3);
        $fraction = intdiv((int) substr($now, 5, 3) * (self::MAX_FRACTION + 1), 1000);

        $sequence = self::$clockSequence;

        if ($sequence === null || $time !== $sequence->time) {
            if ($time[0] === '-') {
                throw new TimeSourceException(
                    'The current time is before 1970-01-01 00:00:00 UTC, the first time a version 7 UUID can have',
                );
            }

            // In the first second of the Unix Epoch, the seconds are "0", which would be a leading zero.
            if ($time[0] === '0') {
                $time = (string) (int) $time;
            }

            if ($sequence === null || $this->isLater($time, $sequence->time)) {
                if ($this->isLater($time, self::MAX_TIME)) {
                    throw new TimeSourceException(
                        'The current time is after 10889-08-02 05:31:50.655 UTC, '
                        . 'the last time a version 7 UUID can have',
                    );
                }

                return self::$clockSequence = $this->start($time, $fraction);
            }
        }

        if ($fraction > $sequence->fraction && $time === $sequence->time) {
            $sequence->fraction = $fraction;
        }

        $this->increase($sequence);

        return $sequence;
    }

    /**
     * Returns the timestamp and counter for a date-time
     *
     * A date-time equal, to the microsecond, to the one in the call before it continues that call's counter. Any other
     * date-time starts a new counter. The timestamp and counter for the current time are left alone.
     */
    private function nextForDateTime(DateTimeInterface $dateTime): UnixTimeSequence
    {
        // The value is the seconds followed by six digits of microseconds, such as "1700000000123456".
        $microtime = $dateTime->format('Uu');
        $time = substr($microtime, 0, -3);

        if (str_starts_with($time, '-') || $this->isLater($time, self::MAX_TIME)) {
            throw new InvalidArgumentException(
                'The date-time for a version 7 UUID must be from 1970-01-01 00:00:00 UTC '
                . 'through 10889-08-02 05:31:50.655 UTC',
            );
        }

        $fraction = intdiv((int) substr($microtime, -3) * (self::MAX_FRACTION + 1), 1000);

        $sequence = self::$datedSequence;

        if ($sequence !== null && $sequence->givenTime === $time && $sequence->givenFraction === $fraction) {
            $this->increase($sequence);

            return $sequence;
        }

        // In the first second of the Unix Epoch, the seconds are "0", which would be a leading zero.
        $sequence = $this->start($time[0] === '0' ? (string) (int) $time : $time, $fraction);
        $sequence->givenTime = $time;
        $sequence->givenFraction = $fraction;

        return self::$datedSequence = $sequence;
    }

    /**
     * Returns whether one count of milliseconds, as a string of digits with no leading zero, is greater than another
     */
    private function isLater(string $time, string $other): bool
    {
        if (strlen($time) !== strlen($other)) {
            return strlen($time) > strlen($other);
        }

        return strcmp($time, $other) > 0;
    }

    /**
     * Returns a sequence at the given timestamp with a new random counter
     */
    private function start(string $time, int $fraction): UnixTimeSequence
    {
        $sequence = new UnixTimeSequence($time, $fraction, 0, 0, false);
        $this->restart($sequence);

        return $sequence;
    }

    /**
     * Gives the sequence a new random counter and the identifier of the current process
     */
    private function restart(UnixTimeSequence $sequence): void
    {
        /** @var array{1: int, 2: int, 3: int, 4: int} $random */
        $random = unpack('n4', $this->randomBytes(8));

        $sequence->high = ($random[1] & 0x3fff) << 17 | $random[2] << 1 | $random[3] >> 15;
        $sequence->low = ($random[3] & 0x7fff) << 16 | $random[4];
        $sequence->processId = self::$readsProcessId === true ? getmypid() : false;
    }

    /**
     * Returns bytes from the random generator, after checking that it returned as many as it was asked for
     *
     * @param int<1, max> $length
     *
     * @throws RandomSourceException
     */
    private function randomBytes(int $length): string
    {
        $bytes = $this->randomGenerator->generate($length);

        if (strlen($bytes) !== $length) {
            throw new RandomSourceException(
                'The random generator must return ' . $length . ' bytes; received ' . strlen($bytes) . ' bytes',
            );
        }

        return $bytes;
    }

    /**
     * Increases the counter of the sequence by 1 plus a random 24-bit number
     *
     * When the counter has no room for the number, or when the sequence was last used by another process, which
     * happens after a fork, the timestamp moves forward and the counter starts again.
     */
    private function increase(UnixTimeSequence $sequence): void
    {
        $processId = self::$readsProcessId === true ? getmypid() : false;

        if ($sequence->processId !== $processId) {
            $this->step($sequence);

            return;
        }

        $increment = $this->nextIncrement($processId);
        $room = self::MAX_HALF - $sequence->low;

        if ($increment <= $room) {
            $sequence->low += $increment;

            return;
        }

        if ($sequence->high === self::MAX_HALF) {
            $this->step($sequence);

            return;
        }

        $sequence->high++;
        $sequence->low = $increment - $room - 1;
    }

    /**
     * Moves the sequence one fraction forward, or to the start of the next millisecond when the fraction is at its
     * largest value, and gives it a new random counter
     */
    private function step(UnixTimeSequence $sequence): void
    {
        if ($sequence->fraction < self::MAX_FRACTION) {
            $sequence->fraction++;
            $this->restart($sequence);

            return;
        }

        $time = $sequence->time;

        if ($time === self::MAX_TIME) {
            throw new TimeSourceException(
                'The timestamp of a version 7 UUID cannot go past 10889-08-02 05:31:50.655 UTC',
            );
        }

        $lastDigits = (int) substr($time, -9);

        if ($this->intSize >= 8 || strlen($time) < 10) {
            $time = (string) ((int) $time + 1);
        } elseif ($lastDigits === 999999999) {
            $time = (1 + (int) substr($time, 0, -9)) . '000000000';
        } else {
            $time = substr_replace($time, str_pad((string) ($lastDigits + 1), 9, '0', STR_PAD_LEFT), -9);
        }

        $sequence->time = $time;
        $sequence->fraction = 0;
        $this->restart($sequence);
    }

    /**
     * Returns 1 plus the next random 24-bit number
     *
     * Special thanks to Nicolas Grekas (<https://github.com/nicolas-grekas>) for sharing the following information:
     *
     * Instead of getting this number from random_bytes(), which is slow, we get it by sha512-hashing self::$seed. This
     * produces 64 bytes of entropy, which we need to split in a list of 24-bit numbers. `unpack()` first splits them
     * into 16 x 32-bit numbers; we take the first byte of each number to get 5 extra 24-bit numbers. Then, we consume
     * each number one-by-one and run this logic every 21 iterations.
     *
     * The seed is used for nothing else, so no generated bytes reveal it. A process that did not draw the seed, which
     * happens after a fork, draws its own.
     *
     * @param int | false $processId The identifier of the current process
     */
    private function nextIncrement(int | false $processId): int
    {
        if (self::$seed === null || self::$seedProcessId !== $processId) {
            self::$seed = $this->randomBytes(16);
            self::$seedProcessId = $processId;
            self::$seedIndex = 0;
        }

        if (self::$seedIndex === 0) {
            self::$seed = hash('sha512', self::$seed, true);

            /** @var int[] $s */
            $s = unpack('V*', self::$seed);
            $s[] = ($s[1] >> 8 & 0xff0000) | ($s[2] >> 16 & 0xff00) | ($s[3] >> 24 & 0xff);
            $s[] = ($s[4] >> 8 & 0xff0000) | ($s[5] >> 16 & 0xff00) | ($s[6] >> 24 & 0xff);
            $s[] = ($s[7] >> 8 & 0xff0000) | ($s[8] >> 16 & 0xff00) | ($s[9] >> 24 & 0xff);
            $s[] = ($s[10] >> 8 & 0xff0000) | ($s[11] >> 16 & 0xff00) | ($s[12] >> 24 & 0xff);
            $s[] = ($s[13] >> 8 & 0xff0000) | ($s[14] >> 16 & 0xff00) | ($s[15] >> 24 & 0xff);

            self::$seedParts = $s;
            self::$seedIndex = 21;
        }

        return 1 + (self::$seedParts[self::$seedIndex--] & 0xffffff);
    }
}
