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

namespace Ramsey\Uuid;

interface CodecInterface
{
    /**
     * @return string
     */
    public function encode(UuidInterface $uuid);

    /**
     * @return string
     */
    public function encodeBinary(UuidInterface $uuid);

    /**
     * @return UuidInterface
     */
    public function decode($encodedUuid);

    /**
     * @param string $bytes
     * @return UuidInterface
     */
    public function decodeBytes($bytes);
}
