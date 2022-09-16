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

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;

use function hex2bin;

/**
 * UnixTimeGenerator generates bytes that combine a 48-bit timestamp in
 * milliseconds since the Unix Epoch with 80 random bits
 */
class UnixTimeGenerator implements TimeGeneratorInterface
{
    public function __construct(
        private TimeConverterInterface $timeConverter,
        private TimeProviderInterface $timeProvider,
        private RandomGeneratorInterface $randomGenerator
    ) {
    }

    /**
     * @inheritDoc
     */
    public function generate($node = null, ?int $clockSeq = null): string
    {
        // Generate 10 random bytes to append to the string returned, since our
        // time bytes will consist of 6 bytes.
        $random = $this->randomGenerator->generate(10);

        $time = $this->timeProvider->getTime();
        $unixTimeHex = $this->timeConverter->calculateTime(
            $time->getSeconds()->toString(),
            $time->getMicroseconds()->toString(),
        );

        return hex2bin($unixTimeHex->toString()) . $random;
    }
}
