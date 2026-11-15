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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Exception\NameException;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\UuidInterface;
use ValueError;

use function hash;
use function substr;

/**
 * DefaultNameGenerator generates strings of binary data based on a namespace, name, and hashing algorithm
 */
class DefaultNameGenerator implements NameGeneratorInterface
{
    /**
     * @pure
     */
    public function generate(UuidInterface $ns, string $name, string $hashAlgorithm): string
    {
        try {
            $namespaceBytes = $ns->getBytes();

            // GUIDs store the first three fields in little-endian byte order.
            // Name-based UUIDs require the namespace in network byte order.
            if ($ns instanceof Guid) {
                $namespaceBytes = $namespaceBytes[3] . $namespaceBytes[2] . $namespaceBytes[1] . $namespaceBytes[0]
                    . $namespaceBytes[5] . $namespaceBytes[4] . $namespaceBytes[7] . $namespaceBytes[6]
                    . substr($namespaceBytes, 8);
            }

            return hash($hashAlgorithm, $namespaceBytes . $name, true);
        } catch (ValueError $e) {
            throw new NameException(
                message: sprintf('Unable to hash namespace and name with algorithm \'%s\'', $hashAlgorithm),
                previous: $e,
            );
        }
    }
}
