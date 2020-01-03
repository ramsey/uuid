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

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * NonstandardUuidBuilder builds instances of NonstandardUuid
 *
 * @psalm-immutable
 */
class NonstandardUuidBuilder implements UuidBuilderInterface
{
    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;

    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;

    /**
     * @param NumberConverterInterface $numberConverter The number converter to
     *     use when constructing the NonstandardUuid
     * @param TimeConverterInterface $timeConverter The time converter to use
     *     for converting timestamps extracted from a UUID to Unix timestamps
     */
    public function __construct(
        NumberConverterInterface $numberConverter,
        TimeConverterInterface $timeConverter
    ) {
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }

    /**
     * Builds and returns a NonstandardUuid
     *
     * @param CodecInterface $codec The codec to use for building this instance
     * @param string[] $fields An array of fields from which to construct an instance;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     *
     * @return NonstandardUuid The NonstandardUuidBuilder returns an instance of
     *     Ramsey\Uuid\Nonstandard\NonstandardUuid
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface
    {
        return new NonstandardUuid(
            $fields,
            $this->numberConverter,
            $codec,
            $this->timeConverter
        );
    }
}
