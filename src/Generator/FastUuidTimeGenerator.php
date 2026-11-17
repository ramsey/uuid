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

use FastUuid\Uuid as FastUuid;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

use function dechex;
use function is_int;
use function preg_match;
use function str_pad;
use function strlen;

use const STR_PAD_LEFT;

/**
 * FastUuidTimeGenerator generates strings of binary data for time-based UUIDs, using ext-fast_uuid
 *
 * It takes the node from the node provider and the time from the system clock. Within one process, ext-fast_uuid
 * advances the timestamp by 100 nanoseconds when the clock has not moved since the last call, so consecutive calls
 * with the same node and clock sequence return different bytes.
 *
 * @link https://github.com/iliaal/fast_uuid ext-fast_uuid
 */
class FastUuidTimeGenerator implements TimeGeneratorInterface
{
    public function __construct(private NodeProviderInterface $nodeProvider)
    {
    }

    /**
     * @throws InvalidArgumentException if the parameters contain invalid values
     *
     * @inheritDoc
     */
    public function generate($node = null, ?int $clockSeq = null): string
    {
        if ($node instanceof Hexadecimal) {
            $node = $node->toString();
        }

        if ($node === null) {
            $node = $this->nodeProvider->getNode()->toString();
        }

        if (is_int($node)) {
            $node = dechex($node);
        }

        if (!preg_match('/\A[A-Fa-f0-9]+\z/', $node) || strlen($node) > 12) {
            throw new InvalidArgumentException('Invalid node value');
        }

        // DefaultTimeGenerator packs the clock sequence into 16 bits and the variant overwrites the top two.
        return FastUuid::uuid1(
            str_pad($node, 12, '0', STR_PAD_LEFT),
            $clockSeq === null ? null : $clockSeq & 0x3fff,
        )->getBytes();
    }
}
