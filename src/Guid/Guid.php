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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Guid represents a UUID with "native" (little-endian) byte order
 *
 * From Wikipedia:
 *
 * > The first three fields are unsigned 32- and 16-bit integers and are subject
 * > to swapping, while the last two fields consist of uninterpreted bytes, not
 * > subject to swapping. This byte swapping applies even for versions 3, 4, and
 * > 5, where the canonical fields do not correspond to the content of the UUID.
 *
 * @link https://en.wikipedia.org/wiki/Universally_unique_identifier#Variants UUID Variants on Wikipedia
 *
 * @psalm-immutable
 */
class Guid extends Uuid implements UuidInterface
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
        $this->fields = new GuidFields((string) hex2bin(implode('', $fields)));
        $this->codec = $codec;
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }
}
