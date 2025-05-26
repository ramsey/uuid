<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;

class StaticNodeProviderTest extends TestCase
{
    /**
     * @param non-empty-string $expectedNode
     *
     * @dataProvider provideNodeForTest
     */
    public function testStaticNode(Hexadecimal $node, string $expectedNode): void
    {
        $staticNode = new StaticNodeProvider($node);

        $this->assertSame($expectedNode, $staticNode->getNode()->toString());
    }

    /**
     * @return array<array{node: Hexadecimal, expectedNode: non-empty-string}>
     */
    public function provideNodeForTest(): array
    {
        return [
            [
                'node' => new Hexadecimal('0'),
                'expectedNode' => '010000000000',
            ],
            [
                'node' => new Hexadecimal('1'),
                'expectedNode' => '010000000001',
            ],
            [
                'node' => new Hexadecimal('f2ffffffffff'),
                'expectedNode' => 'f3ffffffffff',
            ],
            [
                'node' => new Hexadecimal('ffffffffffff'),
                'expectedNode' => 'ffffffffffff',
            ],
        ];
    }

    public function testStaticNodeThrowsExceptionForTooLongNode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Static node value cannot be greater than 12 hexadecimal characters'
        );

        new StaticNodeProvider(new Hexadecimal('1000000000000'));
    }
}
