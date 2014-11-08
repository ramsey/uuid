<?php

namespace Rhumsaa\Uuid\Node;

use Rhumsaa\Uuid\NodeProvider;

class FallbackNodeProvider
{
    private $nodeProviders;

    public function __construct(array $providers)
    {
        $this->nodeProviders = $providers;
    }

    public function getNode()
    {
        foreach ($this->nodeProviders as $provider) {
            if ($node = $provider->getNode()) {
                return $node;
            }
        }

        return null;
    }
}
