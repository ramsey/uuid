<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

class SystemNodeProviderTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsSystemNodeFromMacAddress()
    {
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $provider->expects($this->once())
            ->method('getIfconfig')
            ->willReturn(PHP_EOL . 'AA-BB-CC-DD-EE-FF' . PHP_EOL);

        $node = $provider->getNode();

        $this->assertTrue(ctype_xdigit($node), 'Node should be a hexadecimal string. Actual node: ' . $node);
        $length = strlen($node);
        $lengthError = 'Node should be 12 characters. Actual length: ' . $length . PHP_EOL . ' Actual node: ' . $node;
        $this->assertSame(12, $length, $lengthError);
    }


    public function notationalFormatsDataProvider()
    {
        return [
            ['01-23-45-67-89-ab', '0123456789ab'],
            ['01:23:45:67:89:ab', '0123456789ab']
        ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider notationalFormatsDataProvider
     * @param $formatted
     * @param $expected
     */
    public function testGetNodeReturnsNodeStrippedOfNotationalFormatting($formatted, $expected)
    {
        //Using a stub to provide data for the protected method that gets the node
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();
        $provider->method('getIfconfig')
            ->willReturn(PHP_EOL . $formatted . PHP_EOL);

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $node = $provider->getNode();
        $this->assertEquals($expected, $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFirstMacAddressFound()
    {
        //Using a stub to provide data for the protected method that gets the node
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();
        $provider->method('getIfconfig')
            ->willReturn(PHP_EOL . 'AA-BB-CC-DD-EE-FF' . PHP_EOL .
                '00-11-22-33-44-55' . PHP_EOL .
                'FF-11-EE-22-DD-33' . PHP_EOL);

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $node = $provider->getNode();
        $this->assertEquals('AABBCCDDEEFF', $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFalseWhenNodeIsNotFound()
    {
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getIfconfig')
            ->willReturn('some string that does not match the mac address');

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $node = $provider->getNode();
        $this->assertFalse($node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeWillNotExecuteSystemCallIfFailedFirstTime()
    {
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getIfconfig')
            ->willReturn('some string that does not match the mac address');

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $provider->getNode();
        $provider->getNode();
    }

    public function osCommandDataProvider()
    {
        return [
            'windows' => ['Windows', 'ipconfig /all 2>&1'],
            'mac' => ['Darwhat', 'ifconfig 2>&1'],
            'linux' => ['Linux', 'netstat -ie 2>&1'],
            'anything_else' => ['someotherxyz', 'netstat -ie 2>&1']
        ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider osCommandDataProvider
     * @param $os
     * @param $command
     */
    public function testGetNodeGetsNetworkInterfaceConfig($os, $command)
    {
        $this->skipIfHhvm();
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'php_uname', $os);
        $passthru = AspectMock::func('Ramsey\Uuid\Provider\Node', 'passthru', 'whatever');

        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getSysfs'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);

        $provider->getNode();
        $passthru->verifyInvokedOnce([$command]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsSameNodeUponSubsequentCalls()
    {
        //Using a stub to provide data for the protected method that gets the node
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig'])
            ->getMock();
        $provider->method('getIfconfig')
            ->willReturn(PHP_EOL . 'AA-BB-CC-DD-EE-FF' . PHP_EOL);

        $node = $provider->getNode();
        $node2 = $provider->getNode();
        $this->assertEquals($node, $node2);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSubsequentCallsToGetNodeDoNotRecallIfconfig()
    {
        //Using a mock to verify the provider only gets the node from ifconfig one time
        $provider = $this->getMockBuilder('Ramsey\Uuid\Provider\Node\SystemNodeProvider')
            ->setMethods(['getIfconfig','getSysfs'])
            ->getMock();
        $provider->expects($this->once())
            ->method('getIfconfig')
            ->willReturn(PHP_EOL . 'AA-BB-CC-DD-EE-FF' . PHP_EOL);
        $provider->expects($this->once())
            ->method('getSysfs')
            ->willReturn(false);
        $provider->getNode();
        $provider->getNode();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider osCommandDataProvider
     * @param $os
     * @param $command
     */
    public function testCallGetsysfsOnLinux($os)
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'php_uname', $os);
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'glob', [
            'data://text/plain,00:00:00:00:00:00',
            'data://text/plain,01:02:03:04:05:06',
        ]);

        $provider = \Mockery::mock('Ramsey\Uuid\Provider\Node\SystemNodeProvider');
        $provider->shouldAllowMockingProtectedMethods();
        $provider->shouldReceive('getNode')->passthru();

        if ($os === 'Linux') {
            $provider->shouldReceive('getIfconfig')->never();
            $provider->shouldReceive('getSysfs')->passthru();
        } else {
            $provider->shouldReceive('getIfconfig')->once()->andReturn(PHP_EOL . '01-02-03-04-05-06' . PHP_EOL);
            $provider->shouldReceive('getSysfs')->once()->andReturn(false);
        }

        $this->assertEquals('010203040506', $provider->getNode());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallGetsysfsOnLinuxWhenGlobReturnsFalse()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'php_uname', 'Linux');
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'glob', false);

        $provider = \Mockery::mock('Ramsey\Uuid\Provider\Node\SystemNodeProvider');
        $provider->shouldAllowMockingProtectedMethods();
        $provider->shouldReceive('getNode')->passthru();
        $provider->shouldReceive('getSysfs')->passthru();

        $provider->shouldReceive('getIfconfig')->once()->andReturn(PHP_EOL . '01-02-03-04-05-06' . PHP_EOL);

        $this->assertEquals('010203040506', $provider->getNode());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallGetsysfsOnLinuxWhenGlobReturnsEmptyArray()
    {
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'php_uname', 'Linux');
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'glob', []);

        $provider = \Mockery::mock('Ramsey\Uuid\Provider\Node\SystemNodeProvider');
        $provider->shouldAllowMockingProtectedMethods();
        $provider->shouldReceive('getNode')->passthru();
        $provider->shouldReceive('getSysfs')->passthru();

        $provider->shouldReceive('getIfconfig')->once()->andReturn(PHP_EOL . '01-02-03-04-05-06' . PHP_EOL);

        $this->assertEquals('010203040506', $provider->getNode());
    }
}
