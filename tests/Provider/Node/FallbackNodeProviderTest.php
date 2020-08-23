<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Exception\NodeException;
use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Provider\Node\NodeProviderCollection;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Provider\Node\SystemNodeProvider;
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

        $this->assertSame('57764a07f756', $node->toString());
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

    public function testSerializationOfNodeProviderCollection(): void
    {
        $staticNodeProvider = new StaticNodeProvider(new Hexadecimal('aabbccddeeff'));
        $randomNodeProvider = new RandomNodeProvider();
        $systemNodeProvider = new SystemNodeProvider();

        $nodeProviderCollection = new NodeProviderCollection(
            [
                $staticNodeProvider,
                $randomNodeProvider,
                $systemNodeProvider,
            ]
        );

        $serializedNodeProviderCollection = serialize($nodeProviderCollection);

        /** @var NodeProviderCollection $unserializedNodeProviderCollection */
        $unserializedNodeProviderCollection = unserialize($serializedNodeProviderCollection);

        $this->assertInstanceOf(NodeProviderCollection::class, $unserializedNodeProviderCollection);

        foreach ($unserializedNodeProviderCollection as $nodeProvider) {
            $this->assertInstanceOf(NodeProviderInterface::class, $nodeProvider);
            $this->assertInstanceOf(Hexadecimal::class, $nodeProvider->getNode());
        }
    }
}
