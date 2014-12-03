<?php

namespace Rhumsaa\Uuid\Provider\Node;

use Rhumsaa\Uuid\Provider\NodeProviderInterface;

class RandomNodeProvider implements NodeProviderInterface
{
    public function getNode()
    {
        // if $node is still null (couldn't get from system), randomly generate
        // a node value, according to RFC 4122, Section 4.5
        return sprintf('%06x%06x', mt_rand(0, 1 << 24), mt_rand(0, 1 << 24));
    }
}
