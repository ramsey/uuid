<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Exception\NodeException;
use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Provider\Node\NodeProviderCollection;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

class FallbackNodeProviderTest extends TestCase
{
    public function testGetNodeCallsGetNodeOnEachProviderUntilNodeFound(): void
    {
        $providerWithNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn(new Hexadecimal('57764a07f756'));
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willThrowException(new NodeException());

        $provider = new FallbackNodeProvider(new NodeProviderCollection([$providerWithoutNode, $providerWithNode]));
        $provider->getNode();
    }

    public function testGetNodeReturnsNodeFromFirstProviderWithNode(): void
    {
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->expects($this->once())
            ->method('getNode')
            ->willThrowException(new NodeException());
        $providerWithNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithNode->expects($this->once())
            ->method('getNode')
            ->willReturn(new Hexadecimal('57764a07f756'));
        $anotherProviderWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $anotherProviderWithoutNode->expects($this->never())
            ->method('getNode');

        $provider = new FallbackNodeProvider(new NodeProviderCollection(
            [$providerWithoutNode, $providerWithNode, $anotherProviderWithoutNode]
        ));
        $node = $provider->getNode();

        $this->assertEquals('57764a07f756', $node);
    }

    public function testGetNodeThrowsExceptionWhenNoNodesFound(): void
    {
        $providerWithoutNode = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $providerWithoutNode->method('getNode')
            ->willThrowException(new NodeException());

        $provider = new FallbackNodeProvider(new NodeProviderCollection([$providerWithoutNode]));

        $this->expectException(NodeException::class);
        $this->expectExceptionMessage(
            'Unable to find a suitable node provider'
        );

        $provider->getNode();
    }
}
