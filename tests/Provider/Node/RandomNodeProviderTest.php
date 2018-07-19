<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

class RandomNodeProviderTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        AspectMock::clean();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeUsesRandomBytes()
    {
        $this->skip64BitTest();

        $hexNode = '38a675685d5';
        $bytes = pack('H*', $hexNode);
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
    public function testGetNodeSetsMulticastBit()
    {
        $this->skip64BitTest();

        $bytes = pack('H*', base_convert(decbin(3892974093781), 2, 16));
        $expectedBytesHex = '38a675685d50';
        $decimal = 62287585500496;
        $expectedNode = '39a675685d50';

        AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $hexDec = AspectMock::func('Ramsey\Uuid\Provider\Node', 'hexdec', $decimal);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $hexDec->verifyInvoked([$expectedBytesHex]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeAlreadyHasMulticastBit()
    {
        $this->skip64BitTest();

        $bytes = pack('H*', base_convert(decbin(4492974093781), 2, 16));
        $expectedBytesHex = '4161a1ff5d50';
        $decimal = 71887585500496;

        // We expect the same hex value for the node.
        $expectedNode = $expectedBytesHex;

        AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $hexDec = AspectMock::func('Ramsey\Uuid\Provider\Node', 'hexdec', $decimal);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $hexDec->verifyInvoked([$expectedBytesHex]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeSetsMulticastBitForLowNodeValue()
    {
        $this->skip64BitTest();

        $bytes = pack('H*', base_convert(decbin(1), 2, 16));
        $expectedBytesHex = '10';
        $decimal = 16;
        $expectedNode = '010000000010';

        AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $bytes);
        $hexDec = AspectMock::func('Ramsey\Uuid\Provider\Node', 'hexdec', $decimal);
        $provider = new RandomNodeProvider();

        $this->assertSame($expectedNode, $provider->getNode());
        $hexDec->verifyInvoked([$expectedBytesHex]);
    }

    public function testGetNodeAlwaysSetsMulticastBit()
    {
        $this->skip64BitTest();

        $provider = new RandomNodeProvider();

        $this->assertSame('010000000000', sprintf('%012x', hexdec($provider->getNode()) & 0x010000000000));
    }
}
