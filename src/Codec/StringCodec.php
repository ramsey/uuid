<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Codec;

use InvalidArgumentException;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFields;
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
     * Constructs a StringCodec for use encoding and decoding UUIDs
     *
     * @param UuidBuilderInterface $builder The UUID builder to use when encoding UUIDs
     */
    public function __construct(UuidBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Encodes a UuidInterface as a string representation of a UUID
     *
     * @param UuidInterface $uuid
     * @return string Hexadecimal string representation of a UUID
     */
    public function encode(UuidInterface $uuid)
    {
        $fields = array_values($uuid->getFieldsHex()->getFields());

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $fields
        );
    }

    /**
     * Encodes a UuidInterface as a binary representation of a UUID
     *
     * @param UuidInterface $uuid
     * @return string Binary string representation of a UUID
     */
    public function encodeBinary(UuidInterface $uuid)
    {
        return hex2bin($uuid->getHex());
    }

    /**
     * Decodes a string representation of a UUID into a UuidInterface object instance
     *
     * @param string $encodedUuid
     * @return UuidInterface
     */
    public function decode($encodedUuid)
    {
        $components = $this->extractComponents($encodedUuid);
        $fields = $this->getFields($components);

        return $this->builder->build($this, $fields);
    }

    /**
     * Decodes a binary representation of a UUID into a UuidInterface object instance
     *
     * @param string $bytes
     * @return UuidInterface
     */
    public function decodeBytes($bytes)
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }

        $hexUuid = unpack('H*', $bytes);

        return $this->decode($hexUuid[1]);
    }

    /**
     * Returns the UUID builder
     *
     * @return UuidBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Returns an array of UUID components (the UUID exploded on its dashes)
     *
     * @param string $encodedUuid
     * @return array
     */
    protected function extractComponents($encodedUuid)
    {
        $nameParsed = str_replace(array(
            'urn:',
            'uuid:',
            '{',
            '}',
            '-'
        ), '', $encodedUuid);

        // We have stripped out the dashes and are breaking up the string using
        // substr(). In this way, we can accept a full hex value that doesn't
        // contain dashes.
        $components = array(
            substr($nameParsed, 0, 8),
            substr($nameParsed, 8, 4),
            substr($nameParsed, 12, 4),
            substr($nameParsed, 16, 4),
            substr($nameParsed, 20)
        );

        $nameParsed = implode('-', $components);

        if (!Uuid::isValid($nameParsed)) {
            throw new InvalidArgumentException('Invalid UUID string: ' . $encodedUuid);
        }

        return $components;
    }

    /**
     * Returns the fields that make up this UUID
     *
     * @param array $components
     *
     * @return UuidFields
     */
    protected function getFields(array $components)
    {
        return new UuidFields(
            sprintf('%08s', $components[0]),
            sprintf('%04s', $components[1]),
            sprintf('%04s', $components[2]),
            sprintf('%02s', substr($components[3], 0, 2)),
            sprintf('%02s', substr($components[3], 2)),
            sprintf('%012s', $components[4])
        );
    }
}
