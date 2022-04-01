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

namespace Ramsey\Uuid\Fields;

use ValueError;

use function base64_decode;
use function sprintf;
use function strlen;

/**
 * Provides common serialization functionality to fields
 *
 * @psalm-immutable
 */
trait SerializableFieldsTrait
{
    /**
     * @param string $bytes The bytes that comprise the fields
     */
    abstract public function __construct(string $bytes);

    /**
     * Returns the bytes that comprise the fields
     */
    abstract public function getBytes(): string;

    /**
     * @return array{bytes: string}
     */
    public function __serialize(): array
    {
        return ['bytes' => $this->getBytes()];
    }

    /**
     * @param array{bytes?: string} $data
     */
    public function __unserialize(array $data): void
    {
        if (!isset($data['bytes'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }

        if (strlen($data['bytes']) === 16) {
            $this->__construct($data['bytes']);
        } else {
            $this->__construct(base64_decode($data['bytes']));
        }
    }
}
