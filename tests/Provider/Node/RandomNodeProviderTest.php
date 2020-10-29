<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Node;

use Exception;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use phpmock\mockery\PHPMockery;

use function bin2hex;
use function hex2bin;
use function hexdec;
use function sprintf;
use function substr;

class RandomNodeProviderTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeUsesRandomBytes(): void
    {
        $bytes = hex2bin('38a675685d50');
        $expectedNode = '39a675685d50';

        PHPMockery::mock('Ramsey\Uuid\Provider\Node', 'random_bytes')
            ->once()
            ->with(6)
            ->andReturn($bytes);

        $provider = new RandomNodeProvider();
        $node = $provider->getNode();

        $this->assertSame($expectedNode, $node->toString());
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

        PHPMockery::mock('Ramsey\Uuid\Provider\Node', 'random_bytes')
            ->once()
            ->with(6)
            ->andReturn($bytes);

        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode()->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeSetsMulticastBitForLowNodeValue(): void
    {
        $bytes = hex2bin('100000000001');
        $expectedNode = '110000000001';

        PHPMockery::mock('Ramsey\Uuid\Provider\Node', 'random_bytes')
            ->once()
            ->with(6)
            ->andReturn($bytes);

        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode()->toString());
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

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeThrowsExceptionWhenExceptionThrownByRandombytes(): void
    {
        PHPMockery::mock('Ramsey\Uuid\Provider\Node', 'random_bytes')
            ->once()
            ->andThrow(new Exception('Could not gather sufficient random data'));

        $provider = new RandomNodeProvider();

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $provider->getNode();
    }
}
