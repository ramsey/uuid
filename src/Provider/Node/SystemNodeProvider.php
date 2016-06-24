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
 * SystemNodeProvider provides functionality to get the system node ID (MAC
 * address) using external system calls
 */
class SystemNodeProvider implements NodeProviderInterface
{
    /**
     * Returns the system node ID
     *
     * @return string|false System node ID as a hexadecimal string, or false if it is not found
     */
    public function getNode()
    {
        static $node = null;

        if ($node !== null) {
            return $node;
        }

        $pattern = '/[^:]([0-9A-Fa-f]{2}([:-])[0-9A-Fa-f]{2}(\2[0-9A-Fa-f]{2}){4})[^:]/';
        $matches = array();

        // Search the ifconfig output for all MAC addresses and return
        // the first one found
        $node = false;
        if (preg_match_all($pattern, $this->getIfconfig(), $matches, PREG_PATTERN_ORDER)) {
            $node = $matches[1][0];
            $node = str_replace([':', '-'], '', $node);
        }

        return $node;
    }

    /**
     * Returns the network interface configuration for the system
     *
     * @codeCoverageIgnore
     * @return string
     */
    protected function getIfconfig()
    {
        ob_start();
        switch (strtoupper(substr(php_uname('a'), 0, 3))) {
            case 'WIN':
                passthru('ipconfig /all 2>&1');
                break;
            case 'DAR':
                passthru('ifconfig 2>&1');
                break;
            case 'LIN':
            default:
                passthru('netstat -ie 2>&1');
                break;
        }

        return ob_get_clean();
    }
}
