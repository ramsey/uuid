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

/**
 * The version number describes how the UUID was generated
 *
 * The version number has the following meaning:
 *
 * 1. Time-based UUID
 * 2. DCE security UUID
 * 3. Name-based UUID hashed with MD5
 * 4. Randomly generated UUID
 * 5. Name-based UUID hashed with SHA-1
 * 6. Reordered time-based UUID
 * 7. Unix Epoch time-based UUID
 * 8. Implementation-specific custom UUID
 *
 * @link http://tools.ietf.org/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3: Version
 *
 * phpcs:disable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
 */
enum Version: int
{
    /**
     * Version 1 (time-based) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3
     */
    case Time = 1;

    /**
     * Version 2 (DCE Security) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3
     */
    case DceSecurity = 2;

    /**
     * Version 3 (name-based and hashed with MD5) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3
     */
    case HashMd5 = 3;

    /**
     * Version 4 (random) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3
     */
    case Random = 4;

    /**
     * Version 5 (name-based and hashed with SHA1) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4122#section-4.1.3 RFC 4122, § 4.1.3
     */
    case HashSha1 = 5;

    /**
     * Version 6 (reordered time-based) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-04#section-4 draft-peabody-dispatch-new-uuid-format-04, § 4
     * @link https://github.com/uuid6/uuid6-ietf-draft UUID version 6 IETF draft
     * @link http://gh.peabody.io/uuidv6/ "Version 6" UUIDs
     */
    case ReorderedTime = 6;

    /**
     * Version 7 (Unix Epoch time-based) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-04#section-4 draft-peabody-dispatch-new-uuid-format-04, § 4
     */
    case UnixTime = 7;

    /**
     * Version 8 (implementation-specific custom) UUID
     *
     * @link https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-04#section-4 draft-peabody-dispatch-new-uuid-format-04, § 4
     */
    case Custom = 8;

    /**
     * Alias for {@see self::Time}
     */
    public const V1 = self::Time;

    /**
     * Alias for {@see self::DceSecurity}
     */
    public const V2 = self::DceSecurity;

    /**
     * Alias for {@see self::HashMd5}
     */
    public const V3 = self::HashMd5;

    /**
     * Alias for {@see self::Random}
     */
    public const V4 = self::Random;

    /**
     * Alias for {@see self::HashSha1}
     */
    public const V5 = self::HashSha1;

    /**
     * Alias for {@see self::ReorderedTime}
     */
    public const V6 = self::ReorderedTime;

    /**
     * Alias for {@see self::UnixTime}
     */
    public const V7 = self::UnixTime;

    /**
     * Alias for {@see self::Custom}
     */
    public const V8 = self::Custom;

    /**
     * Alias for {@see self::ReorderedTime}
     */
    public const Peabody = self::ReorderedTime;
}
