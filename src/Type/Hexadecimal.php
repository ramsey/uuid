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

namespace Ramsey\Uuid\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;

use function preg_match;
use function sprintf;
use function str_starts_with;
use function strtolower;
use function substr;

/**
 * A value object representing a hexadecimal number
 *
 * This class exists for type-safety purposes, to ensure that hexadecimal numbers
 * returned from ramsey/uuid methods as strings are truly hexadecimal and not some
 * other kind of string.
 *
 * @psalm-immutable
 */
final class Hexadecimal implements TypeInterface
{
    /**
     * @var non-empty-string
     */
    private readonly string $value;

    /**
     * @param non-empty-string|self $value The hexadecimal value to store
     */
    public function __construct(self | string $value)
    {
        $this->value = $value instanceof self ? (string) $value : $this->prepareValue($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    /**
     * @return array{string: string}
     */
    public function __serialize(): array
    {
        return ['string' => $this->value];
    }

    /**
     * @inheritDoc
     */
    public function __unserialize(array $data): void
    {
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }

        assert(is_string($data['string']) && $data['string'] !== '');

        $this->value = $this->prepareValue($data['string']);
    }

    /**
     * @param non-empty-string $value
     *
     * @return non-empty-string
     */
    private function prepareValue(string $value): string
    {
        $value = strtolower($value);

        if (str_starts_with($value, '0x')) {
            $value = substr($value, 2);
        }

        if (!preg_match('/^[A-Fa-f0-9]+$/', $value)) {
            throw new InvalidArgumentException(
                'Value must be a hexadecimal number'
            );
        }

        /** @var non-empty-string */
        return $value;
    }
}
