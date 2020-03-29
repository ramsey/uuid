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

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Collection\CollectionInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * A collection of NodeProviderInterface objects
 */
class NodeProviderCollection extends AbstractCollection implements CollectionInterface
{
    public function getType(): string
    {
        return NodeProviderInterface::class;
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into
     *     a UuidInterface instance
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function unserialize($serialized): void
    {
        /** @var mixed[] $data */
        $data = unserialize($serialized, [
            'allowed_classes' => [
                Hexadecimal::class,
                RandomNodeProvider::class,
                StaticNodeProvider::class,
                SystemNodeProvider::class,
            ],
        ]);

        $this->data = $data;
    }
}
