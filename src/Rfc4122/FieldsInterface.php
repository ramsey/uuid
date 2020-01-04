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

use Ramsey\Uuid\Fields\FieldsInterface as BaseFieldsInterface;

/**
 * RFC 4122 defines fields for a specific variant of UUID
 *
 * The fields of an RFC 4122 variant UUID are:
 *
 * * **time_low**: The low field of the timestamp, an unsigned 32-bit integer
 * * **time_mid**: The middle field of the timestamp, an unsigned 16-bit integer
 * * **time_hi_and_version**: The high field of the timestamp multiplexed with
 *   the version number, an unsigned 16-bit integer
 * * **clock_seq_hi_and_reserved**: The high field of the clock sequence
 *   multiplexed with the variant, an unsigned 8-bit integer
 * * **clock_seq_low**: The low field of the clock sequence, an unsigned
 *   8-bit integer
 * * **node**: The spatially unique node identifier, an unsigned 48-bit
 *   integer
 *
 * @link http://tools.ietf.org/html/rfc4122#section-4.1 RFC 4122, § 4.1: Format
 *
 * @psalm-immutable
 */
interface FieldsInterface extends BaseFieldsInterface
{
    /**
     * Returns the high field of the clock sequence multiplexed with the variant
     */
    public function getClockSeqHiAndReserved(): string;

    /**
     * Returns the low field of the clock sequence
     */
    public function getClockSeqLow(): string;

    /**
     * Returns the node field
     */
    public function getNode(): string;

    /**
     * Returns the high field of the timestamp multiplexed with the version
     */
    public function getTimeHiAndVersion(): string;

    /**
     * Returns the low field of the timestamp
     */
    public function getTimeLow(): string;

    /**
     * Returns the middle field of the timestamp
     */
    public function getTimeMid(): string;

    /**
     * Returns the variant
     *
     * For RFC 4122 variant UUIDs, this value should always be the integer `2`.
     */
    public function getVariant(): int;

    /**
     * Returns the version
     *
     * The version number describes how the UUID was generated and has the
     * following meaning:
     *
     * 1. Time-based UUID
     * 2. DCE security UUID
     * 3. Name-based UUID hashed with MD5
     * 4. Randomly generated UUID
     * 5. Name-based UUID hashed with SHA-1
     */
    public function getVersion(): ?int;

    /**
     * Returns true if these fields represent a nil UUID
     *
     * The nil UUID is special form of UUID that is specified to have all 128
     * bits set to zero.
     */
    public function isNil(): bool;
}
