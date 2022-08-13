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
 * For RFC 4122 variant UUIDs, this value should always be the integer `2`.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1
 */
enum Variant: int
{
    /**
     * Variant: reserved, NCS backward compatibility
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1
     */
    case ReservedNcs = 0;

    /**
     * Variant: the UUID layout specified in RFC 4122
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1
     */
    case Rfc4122 = 2;

    /**
     * Variant: reserved, Microsoft Corporation backward compatibility
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1
     */
    case ReservedMicrosoft = 6;

    /**
     * Variant: reserved for future definition
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.1 RFC 4122, § 4.1.1
     */
    case ReservedFuture = 7;
}
