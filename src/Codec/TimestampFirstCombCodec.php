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

namespace Ramsey\Uuid\Codec;

use InvalidArgumentException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\UuidInterface;

/**
 * TimestampFirstCombCodec encodes and decodes COMBs, with the timestamp as the
 * first 48 bits
 *
 * In contrast with the TimestampLastCombCodec, the TimestampFirstCombCodec
 * adds the timestamp to the first 48 bits of the COMB. To generate a
 * timestamp-first COMB, set the TimestampFirstCombCodec as the codec, along
 * with the CombGenerator as the random generator.
 *
 * ``` php
 * $factory = new UuidFactory();
 *
 * $factory->setCodec(new TimestampFirstCombCodec($factory->getUuidBuilder()));
 *
 * $factory->setRandomGenerator(new CombGenerator(
 *     $factory->getRandomGenerator(),
 *     $factory->getNumberConverter()
 * ));
 *
 * $timestampFirstComb = $factory->uuid4();
 * ```
 *
 * @link https://www.informit.com/articles/printerfriendly/25862 The Cost of GUIDs as Primary Keys
 */
class TimestampFirstCombCodec extends StringCodec
{
    public function encode(UuidInterface $uuid): string
    {
        $sixPieceComponents = array_values($uuid->getFieldsHex());

        $this->swapTimestampAndRandomBits($sixPieceComponents);

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $sixPieceComponents
        );
    }

    public function encodeBinary(UuidInterface $uuid): string
    {
        $stringEncoding = $this->encode($uuid);

        return (string) hex2bin(str_replace('-', '', $stringEncoding));
    }

    /**
     * @throws InvalidUuidStringException
     *
     * @inheritDoc
     */
    public function decode(string $encodedUuid): UuidInterface
    {
        $fivePieceComponents = $this->extractComponents($encodedUuid);

        $this->swapTimestampAndRandomBits($fivePieceComponents);

        return $this->getBuilder()->build($this, $this->getFields($fivePieceComponents));
    }

    /**
     * @throws InvalidArgumentException if $bytes is an invalid length
     *
     * @inheritDoc
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        return $this->decode(bin2hex($bytes));
    }

    /**
     * Swaps the first 48 bits with the last 48 bits
     *
     * @param string[] $components An array of UUID components (the UUID exploded on its dashes)
     */
    private function swapTimestampAndRandomBits(array &$components): void
    {
        $last48Bits = $components[4];

        if (count($components) === 6) {
            $last48Bits = $components[5];
            $components[5] = $components[0] . $components[1];
        } else {
            $components[4] = $components[0] . $components[1];
        }

        $components[0] = substr($last48Bits, 0, 8);
        $components[1] = substr($last48Bits, 8, 4);
    }
}
