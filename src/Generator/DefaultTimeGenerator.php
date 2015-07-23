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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\UuidFactory;

class DefaultTimeGenerator implements TimeGeneratorInterface
{
    public function generate(UuidFactory $factory, $node = null, $clockSeq = null)
    {
        $node = $this->getValidNode($node, $factory);

        if ($clockSeq === null) {
            // Not using "stable storage"; see RFC 4122, Section 4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }

        // Create a 60-bit time value as a count of 100-nanosecond intervals
        // since 00:00:00.00, 15 October 1582
        $timeOfDay = $factory->getTimeProvider()->currentTime();
        $uuidTime = $factory->getTimeConverter()->calculateTime($timeOfDay['sec'], $timeOfDay['usec']);

        $timeHi = $factory->applyVersion($uuidTime['hi'], 1);
        $clockSeqHi = $factory->applyVariant($clockSeq >> 8);

        $hex = vsprintf(
            '%08s%04s%04s%02s%02s%012s',
            array(
                $uuidTime['low'],
                $uuidTime['mid'],
                sprintf('%04x', $timeHi),
                sprintf('%02x', $clockSeqHi),
                sprintf('%02x', $clockSeq & 0xff),
                $node,
            )
        );

        return hex2bin($hex);
    }

    protected function getValidNode($node, UuidFactory $factory)
    {
        if ($node === null) {
            $node = $factory->getNodeProvider()->getNode();
        }

        // Convert the node to hex, if it is still an integer
        if (is_int($node)) {
            $node = sprintf('%012x', $node);
        }

        if (! ctype_xdigit($node) || strlen($node) > 12) {
            throw new \InvalidArgumentException('Invalid node value');
        }

        return strtolower(sprintf('%012s', $node));
    }
}
