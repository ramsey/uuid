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

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\UuidInterface;

/**
 * GuidStringCodec encodes and decodes globally unique identifiers (GUID)
 *
 * @link https://en.wikipedia.org/wiki/Globally_unique_identifier Globally Unique Identifier
 */
class GuidStringCodec extends StringCodec
{
    /**
     * @psalm-pure
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        $components = $this->swapBytes($this->extractComponents($uuid->toString()));

        return (string) hex2bin(implode('', $components));
    }

    /**
     * @throws InvalidUuidStringException
     *
     * @inheritDoc
     *
     * @psalm-pure
     */
    public function decode(string $encodedUuid): UuidInterface
    {
        $components = $this->swapBytes($this->extractComponents($encodedUuid));

        return $this->getBuilder()->build($this, $this->getFields($components));
    }

    /**
     * @throws InvalidArgumentException if $bytes is an invalid length
     *
     * @inheritDoc
     *
     * @psalm-pure
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        // Specifically call parent::decode to preserve correct byte order
        return parent::decode(bin2hex($bytes));
    }

    /**
     * @param string[] $fields The fields that comprise this UUID
     *
     * @return string[]
     *
     * @psalm-pure
     */
    private function swapBytes(array $fields): array
    {
        $fields = array_values($fields);

        // Swap bytes to support GUID byte order.
        $bytes = (string) hex2bin(implode('', $fields));
        $fields[0] = bin2hex($bytes[3] . $bytes[2] . $bytes[1] . $bytes[0]);
        $fields[1] = bin2hex($bytes[5] . $bytes[4]);
        $fields[2] = bin2hex($bytes[7] . $bytes[6]);

        return $fields;
    }
}
