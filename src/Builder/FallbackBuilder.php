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
use Ramsey\Uuid\Exception\BuilderNotFoundException;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;

/**
 * FallbackBuilder builds a UUID by stepping through a list of UUID builders
 * until a UUID can be constructed without exceptions
 *
 * @psalm-immutable
 */
class FallbackBuilder implements UuidBuilderInterface
{
    /**
     * @var UuidBuilderInterface[]
     */
    private $builders = [];

    /**
     * @param UuidBuilderInterface[] $builders An array of UUID builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    /**
     * Builds and returns a UuidInterface instance using the first builder that
     * succeeds
     *
     * @param CodecInterface $codec The codec to use for building this instance
     * @param string[] $fields An array of fields from which to construct an instance;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure.
     *
     * @return UuidInterface an instance of a UUID object
     */
    public function build(CodecInterface $codec, array $fields): UuidInterface
    {
        foreach ($this->builders as $builder) {
            try {
                return $builder->build($codec, $fields);
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }

        throw new BuilderNotFoundException(
            'Could not find a suitable builder for the provided codec and fields'
        );
    }
}
