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

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * GuidBuilder builds instances of Guid
 *
 * @psalm-immutable
 */
class GuidBuilder implements UuidBuilderInterface
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
     *     use when constructing the Guid
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
     * Builds and returns a Guid
     *
     * @param CodecInterface $codec The codec to use for building this Guid instance
     * @param string[] $fields An array of fields from which to construct a Guid instance;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     *
     * @return Guid The GuidBuilder returns an instance of Ramsey\Uuid\Guid\Guid
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface
    {
        return new Guid(
            $fields,
            $this->numberConverter,
            $codec,
            $this->timeConverter
        );
    }
}
