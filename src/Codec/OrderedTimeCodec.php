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
use Ramsey\Uuid\UuidInterface;

/**
 * OrderedTimeCodec encodes and decodes a UUID, optimizing the byte order for
 * more efficient storage
 *
 * For binary representations of version 1 UUID, this codec may be used to
 * reorganize the time fields, making the UUID closer to sequential when storing
 * the bytes. According to Percona, this optimization can improve database
 * INSERTs and SELECTs using the UUID column as a key.
 *
 * The string representation of the UUID will remain unchanged. Only the binary
 * representation is reordered.
 *
 * **PLEASE NOTE:** Binary representations of UUIDs encoded with this codec must
 * be decoded with this codec. Decoding using another codec can result in
 * malformed UUIDs.
 *
 * @link https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/ Storing UUID Values in MySQL
 */
class OrderedTimeCodec extends StringCodec
{
    /**
     * Returns a binary string representation of a UUID, with the timestamp
     * fields rearranged for optimized storage
     *
     * @inheritDoc
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        $fields = $uuid->getFieldsHex();

        $optimized = [
            $fields['time_hi_and_version'],
            $fields['time_mid'],
            $fields['time_low'],
            $fields['clock_seq_hi_and_reserved'],
            $fields['clock_seq_low'],
            $fields['node'],
        ];

        return (string) hex2bin(implode('', $optimized));
    }

    /**
     * Returns a UuidInterface derived from an ordered-time binary string
     * representation
     *
     * @throws InvalidArgumentException if $bytes is an invalid length
     *
     * @inheritDoc
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException(
                '$bytes string should contain 16 characters.'
            );
        }

        $hex = unpack('H*', $bytes)[1];

        // Rearrange the fields to their original order
        $hex = substr($hex, 8, 4)
            . substr($hex, 12, 4)
            . substr($hex, 4, 4)
            . substr($hex, 0, 4)
            . substr($hex, 16);

        return $this->decode($hex);
    }
}
