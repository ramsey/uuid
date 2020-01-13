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

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * UuidBuilder builds instances of RFC 4122 UUIDs
 *
 * @psalm-immutable
 */
class UuidBuilder implements UuidBuilderInterface
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
     * @param string[] $fields An array of fields from which to construct a Uuid instance
     *
     * @return Uuid The DefaultUuidBuilder returns an instance of Ramsey\Uuid\Uuid
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface
    {
        try {
            $fields = new Fields((string) hex2bin(implode('', $fields)));

            switch ($fields->getVersion()) {
                case 1:
                    $versionClass = UuidV1::class;

                    break;
                case 2:
                    $versionClass = Uuid::class;

                    break;
                case 3:
                    $versionClass = UuidV3::class;

                    break;
                case 4:
                    $versionClass = UuidV4::class;

                    break;
                case 5:
                    $versionClass = UuidV5::class;

                    break;
                default:
                    throw new UnsupportedOperationException(
                        'The UUID version in the given fields is not supported '
                        . 'by this UUID builder'
                    );
            }

            return new $versionClass(
                $fields,
                $this->numberConverter,
                $codec,
                $this->timeConverter
            );
        } catch (\Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
