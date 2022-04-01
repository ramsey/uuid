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
 * Provides common functionality for handling the variant, as defined by RFC 4122
 *
 * @psalm-immutable
 */
trait VariantTrait
{
    /**
     * Returns the bytes that comprise the fields
     */
    abstract public function getBytes(): string;

    /**
     * Returns the variant identifier, according to RFC 4122, for the given bytes
     *
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1: Variant
     *
     * @return Variant The variant identifier, according to RFC 4122
     */
    public function getVariant(): Variant
    {
        if (strlen($this->getBytes()) !== 16) {
            throw new InvalidBytesException('Invalid number of bytes');
        }

        /** @var array $parts */
        $parts = unpack('n*', $this->getBytes());

        // $parts[5] is a 16-bit, unsigned integer containing the variant bits
        // of the UUID. We convert this integer into a string containing a
        // binary representation, padded to 16 characters. We analyze the first
        // three characters (three most-significant bits) to determine the
        // variant.
        $binary = str_pad(
            decbin((int) $parts[5]),
            16,
            '0',
            STR_PAD_LEFT
        );

        $msb = substr($binary, 0, 3);

        if ($msb === '111') {
            $variant = Variant::ReservedFuture;
        } elseif ($msb === '110') {
            $variant = Variant::ReservedMicrosoft;
        } elseif (str_starts_with($msb, '10')) {
            $variant = Variant::Rfc4122;
        } else {
            $variant = Variant::ReservedNcs;
        }

        return $variant;
    }
}
