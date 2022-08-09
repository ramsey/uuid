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

use function abs;
use function assert;
use function is_numeric;
use function sprintf;
use function str_starts_with;

/**
 * A value object representing a decimal
 *
 * This class exists for type-safety purposes, to ensure that decimals
 * returned from ramsey/uuid methods as strings are truly decimals and not some
 * other kind of string.
 *
 * To support values as true decimals and not as floats or doubles, we store the
 * decimals as strings.
 *
 * @psalm-immutable
 */
final class Decimal implements NumberInterface
{
    /**
     * @var numeric-string
     */
    private readonly string $value;

    private bool $isNegative = false;

    public function __construct(float | int | self | string $value)
    {
        $this->value = $value instanceof self ? (string) $value : $this->prepareValue($value);
    }

    public function isNegative(): bool
    {
        return $this->isNegative;
    }

    /**
     * @return numeric-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return numeric-string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return numeric-string
     */
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

        assert(is_string($data['string']));

        $this->value = $this->prepareValue($data['string']);
    }

    /**
     * @return numeric-string
     */
    private function prepareValue(float | int | string $value): string
    {
        $value = (string) $value;

        if (!is_numeric($value)) {
            throw new InvalidArgumentException(
                'Value must be a signed decimal or a string containing only '
                . 'digits 0-9 and, optionally, a decimal point or sign (+ or -)'
            );
        }

        // Remove the leading +-symbol.
        if (str_starts_with($value, '+')) {
            $value = substr($value, 1);
        }

        // For cases like `-0` or `-0.0000`, convert the value to `0`.
        if (abs((float) $value) === 0.0) {
            $value = '0';
        }

        if (str_starts_with($value, '-')) {
            $this->isNegative = true;
        }

        assert(is_numeric($value));

        return $value;
    }
}
