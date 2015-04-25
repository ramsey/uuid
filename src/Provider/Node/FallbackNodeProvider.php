<?php

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Uuid\Provider\NodeProviderInterface;

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
