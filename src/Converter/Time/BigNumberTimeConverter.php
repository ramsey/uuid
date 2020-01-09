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
use Ramsey\Uuid\Math\BrickMathCalculator;

/**
 * Previously used to integrate moontoast/math as a bignum arithmetic library,
 * BigNumberTimeConverter is deprecated in favor of ArbitraryPrecisionTimeConverter
 *
 * @deprecated Transition to {@see GenericTimeConverter}.
 */
class BigNumberTimeConverter implements TimeConverterInterface
{
    /**
     * @var TimeConverterInterface
     */
    private $converter;

    public function __construct()
    {
        $this->converter = new GenericTimeConverter(new BrickMathCalculator());
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function calculateTime(string $seconds, string $microSeconds): array
    {
        return $this->converter->calculateTime($seconds, $microSeconds);
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function convertTime(string $timestamp): string
    {
        return $this->converter->convertTime($timestamp);
    }
}
