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

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * DefaultUuidBuilder builds instances of Uuid
 */
class DefaultUuidBuilder implements UuidBuilderInterface
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
     * Constructs the DefaultUuidBuilder
     *
     * @param NumberConverterInterface $numberConverter The number converter to
     *     use when constructing the Uuid
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
     * Builds and returns a Uuid
     *
     * @param CodecInterface $codec The codec to use for building this Uuid instance
     * @param string[] $fields An array of fields from which to construct a Uuid instance;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     *
     * @return Uuid The DefaultUuidBuilder returns an instance of Ramsey\Uuid\Uuid
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface
    {
        return new Uuid(
            $fields,
            $this->numberConverter,
            $codec,
            $this->timeConverter
        );
    }
}
