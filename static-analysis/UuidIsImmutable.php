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

namespace Ramsey\Uuid\StaticAnalysis;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * This is a static analysis fixture to verify that the API signature
 * of a UUID allows for pure operations. Almost all methods will seem to be
 * redundant or trivial: that's normal, we're just verifying the
 * transitivity of immutable type signatures.
 *
 * Please note that this does not guarantee that the internals of the UUID
 * library are pure/safe, but just that the declared API to the outside world
 * is seen as immutable.
 */
final class UuidIsImmutable
{
    /** @psalm-pure */
    public static function pureCompareTo(UuidInterface $a, UuidInterface $b): int
    {
        return $a->compareTo($b);
    }

    /** @psalm-pure */
    public static function pureEquals(UuidInterface $a, ?object $b): bool
    {
        return $a->equals($b);
    }

    /**
     * @return mixed[]
     *
     * @psalm-pure
     */
    public static function pureGetters(UuidInterface $a): array
    {
        return [
            $a->getBytes(),
            $a->getNumberConverter(),
            $a->getHex(),
            $a->getFieldsHex(),
            $a->getClockSeqHiAndReservedHex(),
            $a->getClockSeqLowHex(),
            $a->getClockSequenceHex(),
            $a->getDateTime(),
            $a->getInteger(),
            $a->getLeastSignificantBitsHex(),
            $a->getMostSignificantBitsHex(),
            $a->getNodeHex(),
            $a->getTimeHiAndVersionHex(),
            $a->getTimeLowHex(),
            $a->getTimeMidHex(),
            $a->getTimestampHex(),
            $a->getUrn(),
            $a->getVariant(),
            $a->getVersion(),
            $a->toString(),
            $a->__toString(),
        ];
    }

    /**
     * @return UuidInterface[]|bool[]
     *
     * @psalm-pure
     */
    public static function pureStaticUuidApi(): array
    {
        $id = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        return [
            Uuid::fromBytes($id->getBytes()),
            Uuid::fromInteger($id->getInteger()),
            Uuid::isValid('ff6f8cb0-c57d-11e1-9b21-0800200c9a66'),
        ];
    }
}
