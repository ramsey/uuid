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

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Provider\NodeProviderInterface;

/**
 * RandomNodeProvider provides functionality to generate a random node ID, in
 * the event that the node ID could not be obtained from the host system
 *
 * @link http://tools.ietf.org/html/rfc4122#section-4.5
 */
class RandomNodeProvider implements NodeProviderInterface
{
    /**
     * Returns the system node ID
     *
     * @return string System node ID as a hexadecimal string
     */
    public function getNode()
    {
        return sprintf('%06x%06x', mt_rand(0, 0xffffff), mt_rand(0, 0xffffff) | 0x01);
    }
}
