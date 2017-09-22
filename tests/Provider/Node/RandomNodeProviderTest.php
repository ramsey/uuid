<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

class RandomNodeProviderTest extends TestCase
{
    private $num;
    private $node = '38a675685d50';

    protected function setUp()
    {
        $this->num = pack('H*', base_convert(decbin(3892974093781), 2, 16));

        $this->skipIfHhvm();
        parent::setUp();
    }

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
        $randomBytes = AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $this->num);
        $provider = new RandomNodeProvider();
        $provider->getNode();
        $randomBytes->verifyInvoked(6);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeFormatsRandomNumbersIntoHexString()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $this->num);
        $bin2Hex = AspectMock::func('Ramsey\Uuid\Provider\Node', 'bin2hex', $this->node);
        $provider = new RandomNodeProvider();
        $provider->getNode();
        $bin2Hex->verifyInvoked($this->num);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsHexString()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'random_bytes', $this->num);
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'bin2hex', $this->node);
        $provider = new RandomNodeProvider();
        $this->assertEquals($this->node, $provider->getNode());
    }
}
