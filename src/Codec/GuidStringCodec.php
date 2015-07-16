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

namespace Ramsey\Uuid\Codec;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

class GuidStringCodec extends StringCodec
{

    public function encode(UuidInterface $uuid)
    {
        $components = array_values($uuid->getFieldsHex());

        // Swap byte-order on the first three fields
        $this->swapFields($components);

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $components
        );
    }

    public function encodeBinary(UuidInterface $uuid)
    {
        $components = array_values($uuid->getFieldsHex());

        return hex2bin(implode('', $components));
    }

    public function decode($encodedUuid)
    {
        $components = $this->extractComponents($encodedUuid);

        $this->swapFields($components);

        return $this->getBuilder()->build($this, $this->getFields($components));
    }

    public function decodeBytes($bytes)
    {
        return parent::decode(bin2hex($bytes));
    }

    protected function swapFields(array & $components)
    {
        $hex = unpack('H*', pack('V', hexdec($components[0])));
        $components[0] = $hex[1];
        $hex = unpack('H*', pack('v', hexdec($components[1])));
        $components[1] = $hex[1];
        $hex = unpack('H*', pack('v', hexdec($components[2])));
        $components[2] = $hex[1];
    }
}
