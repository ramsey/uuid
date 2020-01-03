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
    public function encode(UuidInterface $uuid): string
    {
        /** @var string[] $components */
        $components = array_values($uuid->getFieldsHex());

        // Swap byte-order on the first three fields.
        $components = $this->swapFields($components);

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $components
        );
    }

    /**
     * @psalm-pure
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        $components = array_values($uuid->getFieldsHex());

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
        $components = $this->extractComponents($encodedUuid);

        /** @var string[] $components */
        $components = $this->swapFields($components);

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
     * Swap fields to support GUID byte order
     *
     * @param string[] $components An array of UUID components (the UUID exploded on its dashes)
     *
     * @return string[]
     *
     * @psalm-pure
     */
    private function swapFields(array $components): array
    {
        $hex = unpack('H*', pack('L', hexdec($components[0])));
        assert(is_string($hex[1]));
        $components[0] = $hex[1];

        $hex = unpack('H*', pack('S', hexdec($components[1])));
        assert(is_string($hex[1]));
        $components[1] = $hex[1];

        $hex = unpack('H*', pack('S', hexdec($components[2])));
        assert(is_string($hex[1]));
        $components[2] = $hex[1];

        return $components;
    }
}
