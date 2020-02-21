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

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function explode;
use function str_pad;

use const STR_PAD_LEFT;

/**
 * GenericTimeConverter uses the provided calculator to calculate and convert
 * time values
 */
class GenericTimeConverter implements TimeConverterInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function calculateTime(string $seconds, string $microSeconds): Hexadecimal
    {
        $timestamp = new Time($seconds, $microSeconds);

        $sec = $this->calculator->multiply(
            $timestamp->getSeconds(),
            new IntegerObject('10000000')
        );

        $usec = $this->calculator->multiply(
            $timestamp->getMicroSeconds(),
            new IntegerObject('10')
        );

        /** @var IntegerObject $uuidTime */
        $uuidTime = $this->calculator->add(
            $sec,
            $usec,
            new IntegerObject('122192928000000000')
        );

        $uuidTimeHex = str_pad(
            $this->calculator->toHexadecimal($uuidTime)->toString(),
            16,
            '0',
            STR_PAD_LEFT
        );

        return new Hexadecimal($uuidTimeHex);
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function convertTime(Hexadecimal $uuidTimestamp): Time
    {
        // From the total, subtract the number of 100-nanosecond intervals from
        // the UUID epoch (Gregorian calendar date) to the Unix epoch. This
        // gives us the number of 100-nanosecond intervals from the Unix epoch,
        // which also includes the microtime.
        $epochNanoseconds = $this->calculator->subtract(
            $this->calculator->toIntegerValue($uuidTimestamp),
            new IntegerObject('122192928000000000')
        );

        $unixTimestamp = $this->calculator->divide(
            RoundingMode::HALF_UP,
            6,
            $epochNanoseconds,
            new IntegerObject('10000000')
        );

        $split = explode('.', (string) $unixTimestamp, 2);

        return new Time($split[0], $split[1] ?? 0);
    }
}
