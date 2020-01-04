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

namespace Ramsey\Uuid\Guid;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\UuidInterface;

/**
 * DegradedGuid represents a GUID on 32-bit systems
 *
 * Some of the functionality of a DegradedGuid is not present or degraded, since
 * 32-bit systems are unable to perform the necessary mathematical operations or
 * represent the integers appropriately.
 *
 * @psalm-immutable
 */
class DegradedGuid extends DegradedUuid implements UuidInterface
{
    /**
     * @param string[] $fields
     */
    public function __construct(
        array $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        $this->fields = new Fields((string) hex2bin(implode('', $fields)));
        $this->codec = $codec;
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }
}
