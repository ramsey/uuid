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

namespace Ramsey\Uuid\Benchmark;

use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

final class GuidConversionBench
{
    private const UUID_BYTES = [
        "\x1e\x94\x42\x33\x98\x10\x41\x38\x96\x22\x56\xe1\xf9\x0c\x56\xed",
    ];

    private UuidInterface $uuid;

    public function __construct()
    {
        $factory = new UuidFactory(new FeatureSet(useGuids: true));

        $this->uuid = $factory->fromBytes(self::UUID_BYTES[0]);

        assert($this->uuid instanceof Guid);
    }

    public function benchStringConversionOfGuid(): void
    {
        $this->uuid->toString();
    }
}
