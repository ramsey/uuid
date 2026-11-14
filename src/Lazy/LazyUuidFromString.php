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

namespace Ramsey\Uuid\Lazy;

use DateTimeInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\TimeBasedUuidInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use ValueError;

use function assert;
use function bin2hex;
use function hex2bin;
use function preg_match;
use function sprintf;
use function str_replace;
use function strcmp;
use function strtolower;
use function substr;

/**
 * Lazy version of a UUID: its format has not been determined yet, so it is mostly only usable for string/bytes
 * conversion. This object optimizes instantiation, serialization and string conversion time, at the cost of increased
 * overhead for more advanced UUID operations.
 *
 * > [!NOTE]
 * > The {@see FieldsInterface} does not declare methods that deprecated API relies upon: the API has been ported from
 * > the {@see \Ramsey\Uuid\Uuid} definition, and is deprecated anyway.
 *
 * > [!NOTE]
 * > The deprecated API from {@see \Ramsey\Uuid\Uuid} is in use here (on purpose): it will be removed once the
 * > deprecated API is gone from this class too.
 *
 * @internal this type is used internally for performance reasons and is not supposed to be directly referenced in consumer libraries.
 */
final class LazyUuidFromString implements TimeBasedUuidInterface
{
    public const VALID_REGEX = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/ms';

    /**
     * The factory that builds every wrapped UUID; it is created on first use.
     */
    private static ?UuidFactory $factory = null;

    private ?UuidInterface $unwrapped = null;

    /**
     * @param non-empty-string $uuid
     */
    public function __construct(private readonly string $uuid)
    {
    }

    public static function fromBytes(string $bytes): self
    {
        $base16Uuid = bin2hex($bytes);

        return new self(
            substr($base16Uuid, 0, 8)
            . '-'
            . substr($base16Uuid, 8, 4)
            . '-'
            . substr($base16Uuid, 12, 4)
            . '-'
            . substr($base16Uuid, 16, 4)
            . '-'
            . substr($base16Uuid, 20, 12)
        );
    }

    /**
     * @return array{string: non-empty-string}
     */
    public function __serialize(): array
    {
        return ['string' => $this->uuid];
    }

    /**
     * @throws InvalidUuidStringException if the data is not a UUID string in the standard format
     *
     * @inheritDoc
     */
    public function __unserialize(array $data): void
    {
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }

        assert(is_string($data['string']));

        $uuid = strtolower($data['string']);

        if (preg_match(self::VALID_REGEX, $uuid) !== 1) {
            throw new InvalidUuidStringException('Invalid UUID string: ' . $data['string']);
        }

        $this->uuid = $uuid;
    }

    public function getDateTime(): DateTimeInterface
    {
        $unwrapped = ($this->unwrapped ?? $this->unwrap());

        if ($unwrapped instanceof TimeBasedUuidInterface) {
            return $unwrapped->getDateTime();
        }

        throw new UnsupportedOperationException('Not a time-based UUID');
    }

    public function getUrn(): string
    {
        return ($this->unwrapped ?? $this->unwrap())->getUrn();
    }

    public function compareTo(UuidInterface $other): int
    {
        return strcmp($this->uuid, $other->toString()) <=> 0;
    }

    public function equals(?object $other): bool
    {
        if (!$other instanceof UuidInterface) {
            return false;
        }

        return $this->uuid === $other->toString();
    }

    public function getBytes(): string
    {
        /**
         * @var non-empty-string
         * @phpstan-ignore possiblyImpure.functionCall, possiblyImpure.functionCall
         */
        return (string) hex2bin(str_replace('-', '', $this->uuid));
    }

    public function getFields(): FieldsInterface
    {
        return ($this->unwrapped ?? $this->unwrap())->getFields();
    }

    public function getHex(): Hexadecimal
    {
        return ($this->unwrapped ?? $this->unwrap())->getHex();
    }

    public function getInteger(): IntegerObject
    {
        return ($this->unwrapped ?? $this->unwrap())->getInteger();
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }

    public function toUuidV1(): UuidV1
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        if ($instance instanceof UuidV1) {
            return $instance;
        }

        if (!$instance instanceof UuidV6) {
            throw new UnsupportedOperationException('Not a version 1 or version 6 UUID');
        }

        return $instance->toUuidV1();
    }

    public function toUuidV6(): UuidV6
    {
        $instance = ($this->unwrapped ?? $this->unwrap());

        if (!$instance instanceof UuidV6) {
            throw new UnsupportedOperationException('Not a version 6 UUID');
        }

        return $instance;
    }

    private function unwrap(): UuidInterface
    {
        self::$factory ??= new UuidFactory();

        return $this->unwrapped = self::$factory->fromString($this->uuid);
    }
}
