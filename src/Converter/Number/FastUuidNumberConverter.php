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

namespace Ramsey\Uuid\Converter\Number;

use FastUuid\Exception\InvalidArgumentException as FastUuidInvalidArgumentException;
use FastUuid\Uuid as FastUuid;
use Ramsey\Uuid\Converter\NumberConverterInterface;

use function ltrim;

/**
 * FastUuidNumberConverter converts unsigned numbers of up to 128 bits with ext-fast_uuid
 *
 * Other values, such as negative numbers or numbers with a sign or leading zeros, go to the fallback converter, so the
 * results and exceptions are the same as the fallback converter's.
 *
 * @link https://github.com/iliaal/fast_uuid ext-fast_uuid
 */
class FastUuidNumberConverter implements NumberConverterInterface
{
    public function __construct(private NumberConverterInterface $fallbackConverter)
    {
    }

    /**
     * @pure
     */
    public function fromHex(string $hex): string
    {
        try {
            /** @var numeric-string */
            return FastUuid::fromHexadecimal($hex, true)->getInteger();
        } catch (FastUuidInvalidArgumentException) {
            // ext-fast_uuid accepts only 1 to 32 hexadecimal digits.
            return $this->fallbackConverter->fromHex($hex);
        }
    }

    /**
     * @pure
     */
    public function toHex(string $number): string
    {
        try {
            $hex = ltrim(FastUuid::fromInteger($number)->getHex(), '0');
        } catch (FastUuidInvalidArgumentException) {
            // ext-fast_uuid accepts only digits without a leading zero, up to 2^128 - 1.
            return $this->fallbackConverter->toHex($number);
        }

        return $hex === '' ? '0' : $hex;
    }
}
