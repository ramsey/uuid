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

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * StringCodec encodes and decodes RFC 4122 UUIDs
 *
 * @link http://tools.ietf.org/html/rfc4122
 */
class StringCodec implements CodecInterface
{
    /**
     * @var UuidBuilderInterface
     */
    private $builder;

    /**
     * Constructs a StringCodec
     *
     * @param UuidBuilderInterface $builder The builder to use when encoding UUIDs
     */
    public function __construct(UuidBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @psalm-pure
     */
    public function encode(UuidInterface $uuid): string
    {
        $fields = array_values($uuid->getFieldsHex());

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $fields
        );
    }

    /**
     * @psalm-pure
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        return $uuid->getBytes();
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
        $fields = $this->getFields($components);

        return $this->builder->build($this, $fields);
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
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException(
                '$bytes string should contain 16 characters.'
            );
        }

        $hexUuid = unpack('H*', $bytes);

        return $this->decode((string) $hexUuid[1]);
    }

    /**
     * Returns the UUID builder
     */
    protected function getBuilder(): UuidBuilderInterface
    {
        return $this->builder;
    }

    /**
     * Returns an array of UUID components (the UUID exploded on its dashes)
     *
     * @param string $encodedUuid A hexadecimal string representation of a UUID
     *
     * @return string[]
     *
     * @throws InvalidUuidStringException
     *
     * @psalm-pure
     */
    protected function extractComponents(string $encodedUuid): array
    {
        $nameParsed = str_replace([
            'urn:',
            'uuid:',
            '{',
            '}',
            '-',
        ], '', $encodedUuid);

        // We have stripped out the dashes and are breaking up the string using
        // substr(). In this way, we can accept a full hex value that doesn't
        // contain dashes.
        $components = [
            substr($nameParsed, 0, 8),
            substr($nameParsed, 8, 4),
            substr($nameParsed, 12, 4),
            substr($nameParsed, 16, 4),
            substr($nameParsed, 20),
        ];

        $nameParsed = implode('-', $components);

        if (!Uuid::isValid($nameParsed)) {
            throw new InvalidUuidStringException(
                'Invalid UUID string: ' . $encodedUuid
            );
        }

        return $components;
    }

    /**
     * Returns the fields that make up this UUID
     *
     * @see \Ramsey\Uuid\UuidInterface::getFieldsHex()
     *
     * @param string[] $components An array of hexadecimal strings representing
     *     the fields of an RFC 4122 UUID
     *
     * @return string[]
     *
     * @psalm-pure
     */
    protected function getFields(array $components): array
    {
        return [
            'time_low' => str_pad($components[0], 8, '0', STR_PAD_LEFT),
            'time_mid' => str_pad($components[1], 4, '0', STR_PAD_LEFT),
            'time_hi_and_version' => str_pad($components[2], 4, '0', STR_PAD_LEFT),
            'clock_seq_hi_and_reserved' => str_pad(substr($components[3], 0, 2), 2, '0', STR_PAD_LEFT),
            'clock_seq_low' => str_pad(substr($components[3], 2), 2, '0', STR_PAD_LEFT),
            'node' => str_pad($components[4], 12, '0', STR_PAD_LEFT),
        ];
    }
}
