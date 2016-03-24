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
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        $this->assertTrue(ctype_xdigit($node), 'Node is not a hexadecimal string. Actual node: ' . $node);
        $this->assertTrue(strlen($node) === 12, 'Node is 12 characters long. Actual length: ' . strlen
            ($node) . PHP_EOL . ' Actual node: ' . $node);
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
        //Stubbing a protected method so we can stub the results of getIfconfig
        $provider = new class extends SystemNodeProvider
        {
            public $config;

            protected function getIfconfig()
            {
                return $this->config;
            }
        };
        $provider->config = PHP_EOL . $formatted . PHP_EOL;

        $node = $provider->getNode();
        $this->assertEquals($expected, $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFirstMacAddressFound()
    {
        //Stubbing a protected method so we can stub having multiple addresses to match
        $provider = new class extends SystemNodeProvider
        {
            public $config;

            protected function getIfconfig()
            {
                return $this->config;
            }
        };
        $provider->config = PHP_EOL . 'AA-BB-CC-DD-EE-FF' . PHP_EOL .
            '00-11-22-33-44-55' . PHP_EOL .
            'FF-11-EE-22-DD-33' . PHP_EOL;

        $node = $provider->getNode();
        $this->assertEquals('AABBCCDDEEFF', $node);
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
        AspectMock::func('Ramsey\Uuid\Provider\Node', 'php_uname', $os);
        $passthru = AspectMock::func('Ramsey\Uuid\Provider\Node', 'passthru', 'whatever');

        $provider = new SystemNodeProvider();
        $provider->getNode();
        $passthru->verifyInvokedOnce([$command]);
    }

}
