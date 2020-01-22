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

namespace Ramsey\Uuid\Codec;

use Ramsey\Uuid\UuidInterface;

/**
 * GuidStringCodec encodes and decodes globally unique identifiers (GUID)
 *
 * @link https://en.wikipedia.org/wiki/Globally_unique_identifier Globally Unique Identifier
 */
class GuidStringCodec extends StringCodec
{
    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function decode(string $encodedUuid): UuidInterface
    {
        $bytes = $this->getBytes($encodedUuid);

        return $this->getBuilder()->build($this, $this->swapBytes($bytes));
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function decodeBytes(string $bytes): UuidInterface
    {
        // Specifically call parent::decode to preserve correct byte order
        return parent::decode(bin2hex($bytes));
    }

    /**
     * Swaps bytes according to the GUID rules
     *
     * @psalm-pure
     */
    private function swapBytes(string $bytes): string
    {
        return $bytes[3] . $bytes[2] . $bytes[1] . $bytes[0]
            . $bytes[5] . $bytes[4]
            . $bytes[7] . $bytes[6]
            . substr($bytes, 8);
    }
}
