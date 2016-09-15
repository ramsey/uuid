<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Test\TestCase;

class FallbackNodeProviderTest extends TestCase
{
    public function testGetNodeCallsGetNodeOnEachProviderUntilNodeFound()
    {
        $providerWithNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn('57764a07f756');
        $providerWithoutNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willReturn(null);

        $provider = new FallbackNodeProvider([$providerWithoutNode, $providerWithNode]);
        $provider->getNode();
    }

    public function testGetNodeReturnsNodeFromFirstProviderWithNode()
    {
        $providerWithoutNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willReturn(null);
        $providerWithNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn('57764a07f756');
        $anotherProviderWithoutNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $anotherProviderWithoutNode->expects($this->never())
            ->method('getNode');

        $provider = new FallbackNodeProvider([$providerWithoutNode, $providerWithNode, $anotherProviderWithoutNode]);
        $node = $provider->getNode();
        $this->assertEquals('57764a07f756', $node);
    }

    public function testGetNodeReturnsNullWhenNoNodesFound()
    {
        $providerWithoutNode = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $providerWithoutNode->method('getNode')
            ->willReturn(null);

        $provider = new FallbackNodeProvider([$providerWithoutNode]);
        $node = $provider->getNode();
        $this->assertNull($node);
    }
}
