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
use Ramsey\Uuid\UuidInterface;

use function hash;

/**
 * DefaultNameGenerator generates strings of binary data based on a namespace,
 * name, and hashing algorithm
 */
class DefaultNameGenerator implements NameGeneratorInterface
{
    public function generate(UuidInterface $ns, string $name, string $hashAlgorithm): string
    {
        /** @var string|bool $bytes */
        $bytes = @hash($hashAlgorithm, $ns->getBytes() . $name, true);

        if ($bytes === false) {
            throw new NameException(sprintf(
                'Unable to hash namespace and name with algorithm \'%s\'',
                $hashAlgorithm
            ));
        }

        return (string) $bytes;
    }
}
