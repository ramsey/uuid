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

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Provider\NodeProviderInterface;

/**
 * SystemNodeProvider retrieves the system node ID, if possible
 *
 * The system node ID, or host ID, is often the same as the MAC address for a
 * network interface on the host.
 */
class SystemNodeProvider implements NodeProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getNode()
    {
        static $node = null;

        if ($node !== null) {
            return $node;
        }

        $pattern = '/[^:]([0-9A-Fa-f]{2}([:-])[0-9A-Fa-f]{2}(\2[0-9A-Fa-f]{2}){4})[^:]/';
        $matches = [];

        // First, try a Linux-specific approach.
        $node = $this->getSysfs();

        // Search the ifconfig output for all MAC addresses and return
        // the first one found.
        if ($node === false) {
            if (preg_match_all($pattern, $this->getIfconfig(), $matches, PREG_PATTERN_ORDER)) {
                $node = $matches[1][0] ?? false;
            }
        }

        if ($node !== false) {
            $node = str_replace([':', '-'], '', $node);

            if (is_array($node)) {
                $node = $node[0] ?? false;
            }
        }

        return $node;
    }

    /**
     * Returns the network interface configuration for the system
     *
     * @codeCoverageIgnore
     */
    private function getIfconfig(): string
    {
        $disabledFunctions = strtolower((string) ini_get('disable_functions'));

        if (strpos($disabledFunctions, 'passthru') !== false) {
            return '';
        }

        ob_start();
        switch (strtoupper(substr(constant('PHP_OS'), 0, 3))) {
            case 'WIN':
                passthru('ipconfig /all 2>&1');

                break;
            case 'DAR':
                passthru('ifconfig 2>&1');

                break;
            case 'FRE':
                passthru('netstat -i -f link 2>&1');

                break;
            case 'LIN':
            default:
                passthru('netstat -ie 2>&1');

                break;
        }

        return (string) ob_get_clean();
    }

    /**
     * Returns MAC address from the first system interface via the sysfs interface
     *
     * @return string|bool
     */
    protected function getSysfs()
    {
        $mac = false;

        if (strtoupper(constant('PHP_OS')) === 'LINUX') {
            $addressPaths = glob('/sys/class/net/*/address', GLOB_NOSORT);

            if ($addressPaths === false || count($addressPaths) === 0) {
                return false;
            }

            $macs = [];

            array_walk($addressPaths, function (string $addressPath) use (&$macs): void {
                if (is_readable($addressPath)) {
                    $macs[] = file_get_contents($addressPath);
                }
            });

            $macs = array_map('trim', $macs);

            // Remove invalid entries.
            $macs = array_filter($macs, function (string $address) {
                return $address !== '00:00:00:00:00:00'
                    && preg_match('/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/i', $address);
            });

            $mac = reset($macs);
        }

        return $mac;
    }
}
