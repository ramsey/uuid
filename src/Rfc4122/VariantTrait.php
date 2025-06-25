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

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Exception\InvalidBytesException;
use Ramsey\Uuid\Variant;

use function decbin;
use function str_pad;
use function str_starts_with;
use function strlen;
use function substr;
use function unpack;

use const STR_PAD_LEFT;

/**
 * Provides common functionality for handling the variant, as defined by RFC 9562 (formerly RFC 4122)
 *
 * @immutable
 */
trait VariantTrait
{
    /**
     * Returns the bytes that comprise the fields
     *
     * @pure
     */
    abstract public function getBytes(): string;

    /**
     * Returns the variant
     *
     * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
     */
    public function getVariant(): Variant
    {
        if (strlen($this->getBytes()) !== 16) {
            throw new InvalidBytesException('Invalid number of bytes');
        }

        // According to RFC 9562, sections {@link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 4.1} and
        // {@link https://www.rfc-editor.org/rfc/rfc9562#section-5.10 5.10}, the Max UUID falls within the range
        // of the future variant.
        if ($this->isMax()) {
            return Variant::ReservedFuture;
        }

        // According to RFC 9562, sections {@link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 4.1} and
        // {@link https://www.rfc-editor.org/rfc/rfc9562#section-5.9 5.9}, the Nil UUID falls within the range
        // of the Apollo NCS variant.
        if ($this->isNil()) {
            return Variant::ReservedNcs;
        }

        /** @var int[] $parts */
        $parts = unpack('n*', $this->getBytes());

        // $parts[5] is a 16-bit, unsigned integer containing the variant bits of the UUID. We convert this integer into
        // a string containing a binary representation, padded to 16 characters. We analyze the first three characters
        // (three most-significant bits) to determine the variant.
        $msb = substr(str_pad(decbin($parts[5]), 16, '0', STR_PAD_LEFT), 0, 3);

        if ($msb === '111') {
            return Variant::ReservedFuture;
        } elseif ($msb === '110') {
            return Variant::ReservedMicrosoft;
        } elseif (str_starts_with($msb, '10')) {
            return Variant::Rfc4122;
        }

        return Variant::ReservedNcs;
    }
}
