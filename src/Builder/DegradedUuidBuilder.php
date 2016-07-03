<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;

/**
 * DegradedUuidBuilder builds instances of DegradedUuid
 */
class DegradedUuidBuilder implements UuidBuilderInterface
{
    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;

    /**
     * The time converter to use for converting timestamps extracted from UUIDs to unix timestamps
     * @var TimeConverterInterface
     */
    protected $timeConverter;

    /**
     * Constructs the DegradedUuidBuilder
     *
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the DegradedUuid
     * @param TimeConverterInterface $timeConverter The time converter to use
     *     for converting timestamps extracted from a UUID to unix timestamps
     */
    public function __construct(NumberConverterInterface $numberConverter, TimeConverterInterface $timeConverter)
    {
        $this->numberConverter = $numberConverter;
        $this->timeConverter = $timeConverter;
    }

    /**
     * Builds a DegradedUuid
     *
     * @param CodecInterface $codec The codec to use for building this DegradedUuid
     * @param array $fields An array of fields from which to construct the DegradedUuid;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     * @return DegradedUuid
     */
    public function build(CodecInterface $codec, array $fields)
    {
        return new DegradedUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
    }
}
