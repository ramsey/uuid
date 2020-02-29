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

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;

use function hexdec;

/**
 * DCE Security version, or version 2, UUIDs include local domain identifier,
 * local ID for the specified domain, and node values that are combined into a
 * 128-bit unsigned integer
 *
 * @link https://publications.opengroup.org/c311 DCE 1.1: Authentication and Security Services
 * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap5.htm#tagcjh_08_02_01_01 DCE 1.1, §5.2.1.1
 * @link https://pubs.opengroup.org/onlinepubs/9696989899/chap11.htm#tagcjh_14_05_01_01 DCE 1.1, §11.5.1.1
 * @link https://github.com/google/uuid Go package for UUIDs based on RFC 4122 and DCE 1.1: Auth and Security Services
 *
 * @psalm-immutable
 */
final class UuidV2 extends Uuid implements UuidInterface
{
    /**
     * Creates a version 2 (DCE Security) UUID
     *
     * @param Rfc4122FieldsInterface $fields The fields from which to construct a UUID
     * @param NumberConverterInterface $numberConverter The number converter to use
     *     for converting hex values to/from integers
     * @param CodecInterface $codec The codec to use when encoding or decoding
     *     UUID strings
     * @param TimeConverterInterface $timeConverter The time converter to use
     *     for converting timestamps extracted from a UUID to unix timestamps
     */
    public function __construct(
        Rfc4122FieldsInterface $fields,
        NumberConverterInterface $numberConverter,
        CodecInterface $codec,
        TimeConverterInterface $timeConverter
    ) {
        if ($fields->getVersion() !== Uuid::UUID_TYPE_DCE_SECURITY) {
            throw new InvalidArgumentException(
                'Fields used to create a UuidV2 must represent a '
                . 'version 2 (DCE Security) UUID'
            );
        }

        parent::__construct($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * Returns the local domain used to create this version 2 UUID
     */
    public function getLocalDomain(): int
    {
        /** @var Rfc4122FieldsInterface $fields */
        $fields = $this->getFields();

        return (int) hexdec($fields->getClockSeqLow()->toString());
    }

    /**
     * Returns the string name of the local domain
     */
    public function getLocalDomainName(): string
    {
        return Uuid::DCE_DOMAIN_NAMES[$this->getLocalDomain()];
    }

    /**
     * Returns the local identifier for the domain used to create this version 2 UUID
     */
    public function getLocalIdentifier(): IntegerObject
    {
        /** @var Rfc4122FieldsInterface $fields */
        $fields = $this->getFields();

        return new IntegerObject(
            $this->numberConverter->fromHex($fields->getTimeLow()->toString())
        );
    }
}
