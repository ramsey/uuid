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

namespace Ramsey\Uuid;

/**
 * The variant number describes the layout of the UUID
 *
 * The variant number has the following meaning:
 *
 * - 0 - Reserved for NCS backward compatibility
 * - 2 - The RFC 4122 variant
 * - 6 - Reserved, Microsoft Corporation backward compatibility
 * - 7 - Reserved for future definition
 *
 * For RFC 9562 (formerly RFC 4122) variant UUIDs, this value should always be the integer `2`.
 *
 * @link https://www.rfc-editor.org/rfc/rfc9562#section-4.1 RFC 9562, 4.1. Variant Field
 */
enum Variant: int
{
    /**
     * Reserved, NCS backward compatibility
     */
    case ReservedNcs = 0;

    /**
     * The UUID layout specified in RFC 9562 (formerly RFC 4122)
     */
    case Rfc9562 = 2;

    /**
     * Reserved, Microsoft Corporation backward compatibility
     */
    case ReservedMicrosoft = 6;

    /**
     * Reserved for future definition
     */
    case ReservedFuture = 7;

    /**
     * The UUID layout specified in RFC 9562 (formerly RFC 4122)
     *
     * This is an alias for {@see self::Rfc9562}.
     */
    public const Rfc4122 = self::Rfc9562; // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName
}
