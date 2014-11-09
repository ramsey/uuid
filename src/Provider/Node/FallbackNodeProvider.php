<?php

namespace Rhumsaa\Uuid\Provider\Node;

use Rhumsaa\Uuid\Provider\NodeProviderInterface;

class FallbackNodeProvider implements NodeProviderInterface
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
