<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

class RandomNodeProviderTest extends TestCase
{
    private $num = 16532480;
    private $node = 'fc4400fc4400';

    public function setUp()
    {
        $this->skipIfHhvm();
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        AspectMock::clean();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeUsesMtRand()
    {
        $mtRand = AspectMock::func('Ramsey\Uuid\Provider\Node', 'mt_rand', $this->num);
        $provider = new RandomNodeProvider();
        $provider->getNode();
        $mtRand->verifyInvokedMultipleTimes(2, [0, 1 << 24]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeFormatsRandomNumbersIntoHexString()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'mt_rand', $this->num);
        $sprintf = AspectMock::func('Ramsey\Uuid\Provider\Node', 'sprintf', $this->node);
        $provider = new RandomNodeProvider();
        $provider->getNode();
        $sprintf->verifyInvoked(['%06x%06x', $this->num, $this->num]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsHexString()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'mt_rand', $this->num);
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'sprintf', $this->node);
        $provider = new RandomNodeProvider();
        $this->assertEquals($this->node, $provider->getNode());
    }
}
