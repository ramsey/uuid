<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Test\TestCase;

class FallbackNodeProviderTest extends TestCase
{
    public function testGetNodeCallsGetNodeOnEachProviderUntilNodeFound()
    {
        $providerWithNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn('57764a07f756');
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willReturn(null);

        $provider = new FallbackNodeProvider([$providerWithoutNode, $providerWithNode]);
        $provider->getNode();
    }

    public function testGetNodeReturnsNodeFromFirstProviderWithNode()
    {
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willReturn(null);
        $providerWithNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn('57764a07f756');
        $anotherProviderWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $anotherProviderWithoutNode->expects($this->never())
            ->method('getNode');

        $provider = new FallbackNodeProvider([$providerWithoutNode, $providerWithNode, $anotherProviderWithoutNode]);
        $node = $provider->getNode();
        $this->assertEquals('57764a07f756', $node);
    }

    public function testGetNodeReturnsNullWhenNoNodesFound()
    {
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->method('getNode')
            ->willReturn(null);

        $provider = new FallbackNodeProvider([$providerWithoutNode]);
        $node = $provider->getNode();
        $this->assertNull($node);
    }
}
