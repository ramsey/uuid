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

use function bin2hex;
use function hex2bin;
use function implode;
use function preg_match;
use function sprintf;
use function strlen;
use function substr;

/**
 * StringCodec encodes and decodes RFC 9562 (formerly RFC 4122) UUIDs
 *
 * @immutable
 */
class StringCodec implements CodecInterface
{
    /**
     * Pattern to match a UUID string: 32 hexadecimal digits with hyphens in all four standard positions or with no
     * hyphens, optionally in one pair of braces, optionally after one "urn:" prefix, one "uuid:" prefix, or both.
     */
    private const ENCODED_PATTERN = '/\A(?:urn:)?(?:uuid:)?(\{)?'
        . '([0-9a-f]{8})(-?)([0-9a-f]{4})\3([0-9a-f]{4})\3([0-9a-f]{4})\3([0-9a-f]{12})'
        . '(?(1)\})\z/Di';

    /**
     * Constructs a StringCodec
     *
     * @param UuidBuilderInterface $builder The builder to use when encoding UUIDs
     */
    public function __construct(private UuidBuilderInterface $builder)
    {
    }

    public function encode(UuidInterface $uuid): string
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $hex = bin2hex($uuid->getFields()->getBytes());

        /** @var non-empty-string */
        return sprintf(
            '%08s-%04s-%04s-%04s-%012s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20),
        );
    }

    /**
     * @return non-empty-string
     */
    public function encodeBinary(UuidInterface $uuid): string
    {
        /** @phpstan-ignore-next-line PHPStan complains that this is not a non-empty-string. */
        return $uuid->getFields()->getBytes();
    }

    /**
     * @throws InvalidUuidStringException
     *
     * @inheritDoc
     */
    public function decode(string $encodedUuid): UuidInterface
    {
        /** @phpstan-ignore possiblyImpure.methodCall */
        return $this->builder->build($this, $this->getBytes($encodedUuid));
    }

    public function decodeBytes(string $bytes): UuidInterface
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
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
     */
    protected function getBytes(string $encodedUuid): string
    {
        if (preg_match(self::ENCODED_PATTERN, $encodedUuid, $matches) !== 1) {
            throw new InvalidUuidStringException('Invalid UUID string: ' . $encodedUuid);
        }

        $components = [$matches[2], $matches[4], $matches[5], $matches[6], $matches[7]];

        if (!Uuid::isValid(implode('-', $components))) {
            throw new InvalidUuidStringException('Invalid UUID string: ' . $encodedUuid);
        }

        return (string) hex2bin(implode('', $components));
    }
}
