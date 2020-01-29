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
use Ramsey\Uuid\Rfc4122\FieldsInterface;
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
        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        return $fields->getTimeLow()->toString()
            . '-'
            . $fields->getTimeMid()->toString()
            . '-'
            . $fields->getTimeHiAndVersion()->toString()
            . '-'
            . $fields->getClockSeqHiAndReserved()->toString()
            . $fields->getClockSeqLow()->toString()
            . '-'
            . $fields->getNode()->toString();
    }

    /**
     * @psalm-pure
     * @psalm-return non-empty-string
     * @psalm-suppress MoreSpecificReturnType we know that the retrieved `string` is never empty
     * @psalm-suppress LessSpecificReturnStatement we know that the retrieved `string` is never empty
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        return $uuid->getFields()->getBytes();
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
        return $this->builder->build($this, $this->getBytes($encodedUuid));
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException(
                '$bytes string should contain 16 characters.'
            );
        }

        return $this->builder->build($this, $bytes);
    }

    /**
     * Returns the UUID builder
     */
    protected function getBuilder(): UuidBuilderInterface
    {
        return $this->builder;
    }

    /**
     * Returns a byte string of the UUID
     *
     * @psalm-pure
     */
    protected function getBytes(string $encodedUuid): string
    {
        $parsedUuid = str_replace(
            ['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}', '-'],
            '',
            $encodedUuid
        );

        $components = [
            substr($parsedUuid, 0, 8),
            substr($parsedUuid, 8, 4),
            substr($parsedUuid, 12, 4),
            substr($parsedUuid, 16, 4),
            substr($parsedUuid, 20),
        ];

        if (!Uuid::isValid(implode('-', $components))) {
            throw new InvalidUuidStringException(
                'Invalid UUID string: ' . $encodedUuid
            );
        }

        return (string) hex2bin($parsedUuid);
    }
}
