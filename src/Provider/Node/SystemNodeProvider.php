<?php

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Provider\NodeProviderInterface;

class SystemNodeProvider implements NodeProviderInterface
{
    public function getNode()
    {
        $node = null;
        $pattern = '/[^:]([0-9A-Fa-f]{2}([:-])[0-9A-Fa-f]{2}(\2[0-9A-Fa-f]{2}){4})[^:]/';
        $matches = array();

        // Search the ifconfig output for all MAC addresses and return
        // the first one found
        if (preg_match_all($pattern, $this->getIfconfig(), $matches, PREG_PATTERN_ORDER)) {
            $node = $matches[1][0];
            $node = str_replace(':', '', $node);
            $node = str_replace('-', '', $node);
        }

        return $node;
    }

    /**
     * Returns the network interface configuration for the system
     *
     * @todo Needs evaluation and possibly modification to ensure this works
     *       well across multiple platforms.
     * @codeCoverageIgnore
     * @return string
     */
    protected function getIfconfig()
    {
        switch (strtoupper(substr(php_uname('a'), 0, 3))) {
            case 'WIN':
                $ifconfig = `ipconfig /all 2>&1`;
                break;
            case 'DAR':
                $ifconfig = `ifconfig 2>&1`;
                break;
            case 'LIN':
            default:
                $ifconfig = `netstat -ie 2>&1`;
                break;
        }

        return $ifconfig;
    }
}
