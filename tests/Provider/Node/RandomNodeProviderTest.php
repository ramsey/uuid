<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Node;

use AspectMock\Test as AspectMock;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Test\TestCase;

class RandomNodeProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        AspectMock::clean();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeUsesRandomBytes(): void
    {
        $bytes = hex2bin('38a675685d50');
        $expectedNode = '39a675685d50';

        $randomBytes = AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $provider = new RandomNodeProvider();
        $node = $provider->getNode();

        $this->assertSame($expectedNode, $node);
        $randomBytes->verifyInvoked([6]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeSetsMulticastBit(): void
    {
        $bytes = hex2bin('38a675685d50');

        // Expected node has the multicast bit set, and it wasn't set in the bytes.
        $expectedNode = '39a675685d50';

        $randomBytes = AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $randomBytes->verifyInvoked([6]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeAlreadyHasMulticastBit(): void
    {
        $bytesHex = '4161a1ff5d50';
        $bytes = hex2bin($bytesHex);

        // We expect the same hex value for the node.
        $expectedNode = $bytesHex;

        $randomBytes = AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $randomBytes->verifyInvoked([6]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeSetsMulticastBitForLowNodeValue(): void
    {
        $bytes = hex2bin('100000000001');
        $expectedNode = '110000000001';

        $randomBytes = AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $randomBytes->verifyInvoked([6]);
    }

    public function testGetNodeAlwaysSetsMulticastBit(): void
    {
        $provider = new RandomNodeProvider();
        $nodeHex = $provider->getNode();

        // Convert what we got into bytes so that we can mask out everything
        // except the multicast bit. If the multicast bit doesn't exist, this
        // test will fail appropriately.
        $nodeBytes = (string) hex2bin((string) $nodeHex);

        // Split the node bytes for math on 32-bit systems.
        $nodeMsb = substr($nodeBytes, 0, 3);
        $nodeLsb = substr($nodeBytes, 3);

        // Only set bits that match the mask so we can see that the multicast
        // bit is always set.
        $nodeMsb = sprintf('%06x', hexdec(bin2hex($nodeMsb)) & 0x010000);
        $nodeLsb = sprintf('%06x', hexdec(bin2hex($nodeLsb)) & 0x000000);

        // Recombine the node bytes.
        $node = $nodeMsb . $nodeLsb;

        $this->assertSame('010000000000', $node);
    }
}
