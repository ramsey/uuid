<?php

namespace Ramsey\Uuid\Test\Provider\Node;

use AspectMock\Proxy\FuncProxy;
use Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

/**
 * Tests for the SystemNodeProvider class
 *
 * The class under test make use of various native functions who's output is
 * dictated by which environment PHP runs on. Instead of having to run these
 * tests on each of these environments, the related functions are mocked (using
 * AspectMock). The following functions are concerned:
 *
 * - glob
 * - php_uname
 * - passthru
 * - file_get_contents
 * - ini_get
 *
 * On Linux systems `glob` would normally provide one or more paths were mac
 * address can be retrieved (using `file_get_contents`). On non-linux systems,
 * or when the `glob` fails, `passthru` is used to read the mac address from the
 * command for the relevant environment as provided by `php_uname`.
 *
 * Please note that, in order to have robust tests, (the output of) these
 * functions should ALWAYS be mocked and the amount of times each function
 * should be run should ALWAYS be specified.
 *
 * This will make the tests more verbose but also more bullet-proof.
 */
class SystemNodeProviderTest extends TestCase
{
    const MOCK_GLOB = 'glob';
    const MOCK_UNAME = 'php_uname';
    const MOCK_PASSTHRU = 'passthru';
    const MOCK_FILE_GET_CONTENTS = 'file_get_contents';
    const MOCK_INI_GET = 'ini_get';

    const PROVIDER_NAMESPACE = 'Ramsey\\Uuid\\Provider\\Node';

    /** @var FuncProxy[] */
    private $functionProxies = [];

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideValidNetStatOutput
     *
     * @param string $netstatOutput
     * @param string $expected
     */
    public function testGetNodeReturnsSystemNodeFromMacAddress($netstatOutput, $expected)
    {
        /*/ Arrange mocks for native functions /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () use ($netstatOutput) {
                echo $netstatOutput;
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act upon the system under test/*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert the result match expectations /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertSame($expected, $node);

        $message = vsprintf(
            'Node should be a hexadecimal string of 12 characters. Actual node: %s (length: %s)',
            [$node, strlen($node),]
        );
        $this->assertRegExp('/^[A-Fa-f0-9]{12}$/', $node, $message);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideInvalidNetStatOutput
     *
     * @param string $netstatOutput
     */
    public function testGetNodeShouldNotReturnsSystemNodeForInvalidMacAddress($netstatOutput)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () use ($netstatOutput) {
                echo $netstatOutput;
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertFalse($node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideNotationalFormats
     *
     * @param string $formatted
     * @param string $expected
     */
    public function testGetNodeReturnsNodeStrippedOfNotationalFormatting($formatted, $expected)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () use ($formatted) {
                echo "\n{$formatted}\n";
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertEquals($expected, $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideInvalidNotationalFormats
     *
     * @param string $formatted
     */
    public function testGetNodeDoesNotAcceptIncorrectNotationalFormatting($formatted)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () use ($formatted) {
                echo "\n{$formatted}\n";
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertEquals(false, $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFirstMacAddressFound()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {
                echo "\nAA-BB-CC-DD-EE-FF\n00-11-22-33-44-55\nFF-11-EE-22-DD-33\n";
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertEquals('AABBCCDDEEFF', $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFalseWhenNodeIsNotFound()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {
                echo 'some string that does not match the mac address';
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertFalse($node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeWillNotExecuteSystemCallIfFailedFirstTime()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {
                echo 'some string that does not match the mac address';
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $provider->getNode();
        $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideCommandPerOs
     *
     * @param $os
     * @param $command
     */
    public function testGetNodeGetsNetworkInterfaceConfig($os, $command)
    {
        $this->skipIfHhvm();

        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            'whatever',
            ['mock address path'],
            'whatever',
            $os,
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $provider->getNode();

        /*/ Assert /*/
        $globBodyAssert = null;
        $fileGetContentsAssert = null;
        if ($os === 'Linux') {
            $globBodyAssert = ['/sys/class/net/*/address'];
            $fileGetContentsAssert = ['mock address path'];
        }
        $this->assertMockFunctions(
            $fileGetContentsAssert,
            $globBodyAssert,
            [$command],
            [['a'], ['s']],
            ['disable_functions']
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsSameNodeUponSubsequentCalls()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {
                echo "\nAA-BB-CC-DD-EE-FF\n";
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();
        $node2 = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertEquals($node, $node2);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSubsequentCallsToGetNodeDoNotRecallIfconfig()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {
                echo "\nAA-BB-CC-DD-EE-FF\n";
            },
            'NOT LINUX',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();
        $node2 = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']], ['disabled_functions']);

        $this->assertEquals($node, $node2);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider provideCommandPerOs
     *
     * @param $os
     * @param $command
     */
    public function testCallGetsysfsOnLinux($os, $command)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            function () {
                static $macs = ["00:00:00:00:00:00\n", "01:02:03:04:05:06\n"];
                return array_shift($macs);
            },
            ['mock address path 1', 'mock address path 2'],
            function () {
                echo "\n01-02-03-04-05-06\n";
            },
            $os,
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $fileGetContentsAssert = null;
        $globBodyAssert = null;
        $passthruBodyAssert = [$command];
        $unameBodyAssert = [['a'], ['s']];
        $iniGetDisableFunctionsAssert = ['disable_functions'];

        if ($os === 'Linux') {
            $fileGetContentsAssert = [['mock address path 1'], ['mock address path 2']];
            $globBodyAssert = ['/sys/class/net/*/address'];
            $passthruBodyAssert = null;
            $unameBodyAssert = ['s'];
            $iniGetDisableFunctionsAssert = null;
        }
        $this->assertMockFunctions(
            $fileGetContentsAssert,
            $globBodyAssert,
            $passthruBodyAssert,
            $unameBodyAssert,
            $iniGetDisableFunctionsAssert
        );

        $this->assertEquals('010203040506', $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallGetsysfsOnLinuxWhenGlobReturnsFalse()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            false,
            function () {
                echo "\n01-02-03-04-05-06\n";
            },
            'Linux',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(
            null,
            ['/sys/class/net/*/address'],
            ['netstat -ie 2>&1'],
            [['a'], ['s']],
            ['disabled_functions']
        );

        $this->assertEquals('010203040506', $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCallGetsysfsOnLinuxWhenGlobReturnsEmptyArray()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            [],
            function () {
                echo "\n01-02-03-04-05-06\n";
            },
            'Linux',
            'nothing disabled'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(
            null,
            ['/sys/class/net/*/address'],
            ['netstat -ie 2>&1'],
            [['a'], ['s']],
            ['disabled_functions']
        );

        $this->assertEquals('010203040506', $node);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsFalseWhenPassthruIsDisabled()
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            null,
            'NOT LINUX',
            'PASSTHRU,some_other_function'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(
            null,
            null,
            null,
            [['s']],
            ['disabled_functions']
        );

        $this->assertFalse($node);
    }

    /**
     * Replaces the return value for functions with the given value or callback.
     *
     * @param callback|mixed|null $fileGetContentsBody
     * @param callback|mixed|null $globBody
     * @param callback|mixed|null $passthruBody
     * @param callback|mixed|null $unameBody
     * @param callback|mixed|null $iniGetDisableFunctionsBody
     */
    private function arrangeMockFunctions(
        $fileGetContentsBody,
        $globBody,
        $passthruBody,
        $unameBody,
        $iniGetDisableFunctionsBody
    ) {
        $mockFunction = [
            self::MOCK_FILE_GET_CONTENTS => $fileGetContentsBody,
            self::MOCK_GLOB => $globBody,
            self::MOCK_PASSTHRU => $passthruBody,
            self::MOCK_UNAME => $unameBody,
            self::MOCK_INI_GET => $iniGetDisableFunctionsBody,
        ];

        array_walk($mockFunction, function ($body, $key) {
            $this->functionProxies[$key] = AspectMock::func(self::PROVIDER_NAMESPACE, $key, $body);
        });
    }

    /**
     * Verifies that each function was called exactly once for each assert given.
     *
     * Provide a NULL to assert a function is never called.
     *
     * @param array|callable|null $fileGetContentsAssert
     * @param array|callable|null $globBodyAssert
     * @param array|callable|null $passthruBodyAssert
     * @param array|callable|null $unameBodyAssert
     * @param array|callable|null $iniGetDisableFunctionsAssert
     */
    private function assertMockFunctions(
        $fileGetContentsAssert,
        $globBodyAssert,
        $passthruBodyAssert,
        $unameBodyAssert,
        $iniGetDisableFunctionsAssert
    ) {
        $mockFunctionAsserts = [
            self::MOCK_FILE_GET_CONTENTS => $fileGetContentsAssert,
            self::MOCK_GLOB => $globBodyAssert,
            self::MOCK_PASSTHRU => $passthruBodyAssert,
            self::MOCK_UNAME => $unameBodyAssert,
            self::MOCK_INI_GET => $iniGetDisableFunctionsAssert,
        ];

        array_walk($mockFunctionAsserts, function ($asserts, $key) {
            if ($asserts === null) {
                $this->functionProxies[$key]->verifyNeverInvoked();
            } elseif (is_array($asserts)) {
                foreach ($asserts as $assert) {
                    $this->functionProxies[$key]->verifyInvokedOnce($assert);
                }
            } elseif (is_callable($asserts)) {
                $this->functionProxies[$key]->verifyInvokedOnce($asserts);
            } else {
                $error = vsprintf(
                    'Given parameter for %s must be an array, a callback or NULL, "%s" given.',
                    [$key, gettype($asserts)]
                );
                throw new \InvalidArgumentException($error);
            }
        });
    }

    /**
     * Provides the command that should be executed per supported OS
     *
     * @return array[]
     */
    public function provideCommandPerOs()
    {
        return [
            'windows' => ['Windows', 'ipconfig /all 2>&1'],
            'mac' => ['Darwhat', 'ifconfig 2>&1'],
            'linux' => ['Linux', 'netstat -ie 2>&1'],
            'freebsd' => ['FreeBSD', 'netstat -i -f link 2>&1'],
            'anything_else' => ['someotherxyz', 'netstat -ie 2>&1'],
            'Linux when `glob` fails' => ['LIN', 'netstat -ie 2>&1'],
        ];
    }

    /**
     * Values that are NOT parsed to a mac address by the class under test
     *
     * @return array[]
     */
    public function provideInvalidNetStatOutput()
    {
        return [
            'Not an octal value'                              => [
                "The program 'netstat' is currently not installed.' .
                ' You can install it by typing:\nsudo apt install net-tools\n"
            ],
            'One character too short'                         => ["\nA-BB-CC-DD-EE-FF\n"],
            'One tuple too short'                             => ["\nBB-CC-DD-EE-FF\n"],
            'With colon, with linebreak, without space'       => ["\n:AA-BB-CC-DD-EE-FF\n"],
            'With colon, without linebreak, with space'       => [' : AA-BB-CC-DD-EE-FF'],
            'With colon, without linebreak, without space'    => [':AA-BB-CC-DD-EE-FF'],
            'Without colon, without linebreak, without space' => ['AA-BB-CC-DD-EE-FF'],
            'Without leading linebreak'                       => ["AA-BB-CC-DD-EE-FF\n"],
            'Without leading whitespace'                      => ['AA-BB-CC-DD-EE-FF '],
            'Without trailing linebreak'                      => ["\nAA-BB-CC-DD-EE-FF"],
            'Without trailing whitespace'                     => [' AA-BB-CC-DD-EE-FF'],
        ];
    }

    /**
     * Provides notations that the class under test should NOT attempt to strip
     *
     * @return array[]
     */
    public function provideInvalidNotationalFormats()
    {
        return [
            ['01:23-45-67-89-ab'],
            ['01:23:45-67-89-ab'],
            ['01:23:45:67-89-ab'],
            ['01:23:45:67:89-ab'],
            ['01-23:45:67:89:ab'],
            ['01-23-45:67:89:ab'],
            ['01-23-45-67:89:ab'],
            ['01-23-45-67-89:ab'],
        ];
    }

    /**
     * Provides mac addresses that the class under test should strip notational format from
     *
     * @return array[]
     */
    public function provideNotationalFormats()
    {
        return [
            ['01-23-45-67-89-ab', '0123456789ab'],
            ['01:23:45:67:89:ab', '0123456789ab']
        ];
    }

    /**
     * Values that are parsed to a mac address by the class under test
     *
     * @return array[]
     */
    public function provideValidNetStatOutput()
    {
        return [
            /*/ Full output of related command /*/
            'Full output - Linux' => [<<<'TXT'
                Kernel Interface table
                docker0   Link encap:Ethernet  HWaddr 01:23:45:67:89:ab  
                          inet addr:172.17.0.1  Bcast:0.0.0.0  Mask:255.255.0.0
                          UP BROADCAST MULTICAST  MTU:1500  Metric:1
                          RX packets:0 errors:0 dropped:0 overruns:0 frame:0
                          TX packets:0 errors:0 dropped:0 overruns:0 carrier:0
                          collisions:0 txqueuelen:0 
                          RX bytes:0 (0.0 B)  TX bytes:0 (0.0 B)
                
                enp3s0    Link encap:Ethernet  HWaddr fe:dc:ba:98:76:54  
                          inet addr:10.0.0.1  Bcast:10.0.0.255  Mask:255.255.255.0
                          inet6 addr: ffee::ddcc:bbaa:9988:7766/64 Scope:Link
                          UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
                          RX packets:943077 errors:0 dropped:0 overruns:0 frame:0
                          TX packets:2168039 errors:0 dropped:0 overruns:0 carrier:0
                          collisions:0 txqueuelen:1000 
                          RX bytes:748596414 (748.5 MB)  TX bytes:2930448282 (2.9 GB)
                
                lo        Link encap:Local Loopback  
                          inet addr:127.0.0.1  Mask:255.0.0.0
                          inet6 addr: ::1/128 Scope:Host
                          UP LOOPBACK RUNNING  MTU:65536  Metric:1
                          RX packets:8302 errors:0 dropped:0 overruns:0 frame:0
                          TX packets:8302 errors:0 dropped:0 overruns:0 carrier:0
                          collisions:0 txqueuelen:1000 
                          RX bytes:1094983 (1.0 MB)  TX bytes:1094983 (1.0 MB)
TXT
                , '0123456789ab'],
            'Full output - MacOS' => [<<<'TXT'
                lo0: flags=8049<UP,LOOPBACK,RUNNING,MULTICAST> mtu 16384
                    options=1203<RXCSUM,TXCSUM,TXSTATUS,SW_TIMESTAMP>
                    inet 127.0.0.1 netmask 0xff000000 
                    inet6 ::1 prefixlen 128 
                    inet6 fe80::1%lo0 prefixlen 64 scopeid 0x1 
                    nd6 options=201<PERFORMNUD,DAD>
                gif0: flags=8010<POINTOPOINT,MULTICAST> mtu 1280
                stf0: flags=0<> mtu 1280
                EHC29: flags=0<> mtu 0
                XHC20: flags=0<> mtu 0
                EHC26: flags=0<> mtu 0
                en0: flags=8863<UP,BROADCAST,SMART,RUNNING,SIMPLEX,MULTICAST> mtu 1500
                    options=10b<RXCSUM,TXCSUM,VLAN_HWTAGGING,AV>
                    ether 10:dd:b1:b4:e4:8e 
                    inet6 fe80::c70:76f5:aa1:5db1%en0 prefixlen 64 secured scopeid 0x7 
                    inet 10.53.8.112 netmask 0xfffffc00 broadcast 10.53.11.255
                    nd6 options=201<PERFORMNUD,DAD>
                    media: autoselect (1000baseT <full-duplex>)
                    status: active
                en1: flags=8863<UP,BROADCAST,SMART,RUNNING,SIMPLEX,MULTICAST> mtu 1500
                    ether ec:35:86:38:c8:c2 
                    inet6 fe80::aa:d44f:5f5f:7fd4%en1 prefixlen 64 secured scopeid 0x8 
                    inet 10.53.17.196 netmask 0xfffffc00 broadcast 10.53.19.255
                    nd6 options=201<PERFORMNUD,DAD>
                    media: autoselect
                    status: active
                p2p0: flags=8843<UP,BROADCAST,RUNNING,SIMPLEX,MULTICAST> mtu 2304
                    ether 0e:35:86:38:c8:c2 
                    media: autoselect
                    status: inactive
                awdl0: flags=8943<UP,BROADCAST,RUNNING,PROMISC,SIMPLEX,MULTICAST> mtu 1484
                    ether ea:ab:ae:25:f5:d0 
                    inet6 fe80::e8ab:aeff:fe25:f5d0%awdl0 prefixlen 64 scopeid 0xa 
                    nd6 options=201<PERFORMNUD,DAD>
                    media: autoselect
                    status: active
                en2: flags=8963<UP,BROADCAST,SMART,RUNNING,PROMISC,SIMPLEX,MULTICAST> mtu 1500
                    options=60<TSO4,TSO6>
                    ether 32:00:18:9b:dc:60 
                    media: autoselect <full-duplex>
                    status: inactive
                en3: flags=8963<UP,BROADCAST,SMART,RUNNING,PROMISC,SIMPLEX,MULTICAST> mtu 1500
                    options=60<TSO4,TSO6>
                    ether 32:00:18:9b:dc:61 
                    media: autoselect <full-duplex>
                    status: inactive
                bridge0: flags=8822<BROADCAST,SMART,SIMPLEX,MULTICAST> mtu 1500
                    options=63<RXCSUM,TXCSUM,TSO4,TSO6>
                    ether 32:00:18:9b:dc:60 
                    Configuration:
                        id 0:0:0:0:0:0 priority 0 hellotime 0 fwddelay 0
                        maxage 0 holdcnt 0 proto stp maxaddr 100 timeout 1200
                        root id 0:0:0:0:0:0 priority 0 ifcost 0 port 0
                        ipfilter disabled flags 0x2
                    member: en2 flags=3<LEARNING,DISCOVER>
                            ifmaxaddr 0 port 11 priority 0 path cost 0
                    member: en3 flags=3<LEARNING,DISCOVER>
                            ifmaxaddr 0 port 12 priority 0 path cost 0
                    media: <unknown type>
                    status: inactive
                utun0: flags=8051<UP,POINTOPOINT,RUNNING,MULTICAST> mtu 2000
                    options=6403<RXCSUM,TXCSUM,CHANNEL_IO,PARTIAL_CSUM,ZEROINVERT_CSUM>
                    inet6 fe80::57c6:d692:9d41:d28f%utun0 prefixlen 64 scopeid 0xe 
                    nd6 options=201<PERFORMNUD,DAD>
TXT
                , '10ddb1b4e48e'],
            'Full output - Window' => [<<<'TXT'
                Windows IP Configuration
                
                   Host Name . . . . . . . . . . . . : MSEDGEWIN10
                   Primary Dns Suffix  . . . . . . . : 
                   Node Type . . . . . . . . . . . . : Hybrid
                   IP Routing Enabled. . . . . . . . : No
                   WINS Proxy Enabled. . . . . . . . : No
                   DNS Suffix Search List. . . . . . : network.lan
                
                Ethernet adapter Ethernet:
                
                   Connection-specific DNS Suffix  . : network.lan
                   Description . . . . . . . . . . . : Intel(R) PRO/1000 MT Desktop Adapter
                   Physical Address. . . . . . . . . : 08-00-27-B8-42-C6
                   DHCP Enabled. . . . . . . . . . . : Yes
                   Autoconfiguration Enabled . . . . : Yes
                   Link-local IPv6 Address . . . . . : fe80::606a:ae33:7ce1:b5e9%3(Preferred) 
                   IPv4 Address. . . . . . . . . . . : 10.0.2.15(Preferred) 
                   Subnet Mask . . . . . . . . . . . : 255.255.255.0
                   Lease Obtained. . . . . . . . . . : Tuesday, January 30, 2018 11:25:31 PM
                   Lease Expires . . . . . . . . . . : Wednesday, January 31, 2018 11:25:27 PM
                   Default Gateway . . . . . . . . . : 10.0.2.2
                   DHCP Server . . . . . . . . . . . : 10.0.2.2
                   DHCPv6 IAID . . . . . . . . . . . : 34078759
                   DHCPv6 Client DUID. . . . . . . . : 00-01-00-01-21-40-72-3F-08-00-27-B8-42-C6
                   DNS Servers . . . . . . . . . . . : 10.0.2.3
                   NetBIOS over Tcpip. . . . . . . . : Enabled
                
                Tunnel adapter isatap.network.lan:
                
                   Media State . . . . . . . . . . . : Media disconnected
                   Connection-specific DNS Suffix  . : network.lan
                   Description . . . . . . . . . . . : Microsoft ISATAP Adapter
                   Physical Address. . . . . . . . . : 00-00-00-00-00-00-00-E0
                   DHCP Enabled. . . . . . . . . . . : No
                   Autoconfiguration Enabled . . . . : Yes
TXT
                , '080027B842C6'],
            'Full output - FreeBSD' => [<<<'TXT'
                Name    Mtu Network       Address              Ipkts Ierrs Idrop    Opkts Oerrs  Coll
                em0    1500 <Link#1>      08:00:27:71:a1:00    65514     0     0    42918     0     0
                em1    1500 <Link#2>      08:00:27:d0:60:a0     1199     0     0      535     0     0
                lo0   16384 <Link#3>      lo0                      4     0     0        4     0     0
TXT
                , '08002771a100'],

            /*/ The single line that is relevant /*/
            'Linux  - single line'  => ["\ndocker0   Link encap:Ethernet  HWaddr 01:23:45:67:89:ab\n", '0123456789ab'],
            'MacOS  - Single line ' => ["\nether 10:dd:b1:b4:e4:8e\n", '10ddb1b4e48e'],
            'Window - single line' => ["\nPhysical Address. . . . . . . . . : 08-00-27-B8-42-C6\n", '080027B842C6'],

            /*/ Minimal subsets of the single line to show the differences /*/
            'with colon, with linebreak, with space'          => ["\n : AA-BB-CC-DD-EE-FF\n", 'AABBCCDDEEFF'],
            'without colon, with linebreak, with space'       => ["\n AA-BB-CC-DD-EE-FF \n",  'AABBCCDDEEFF'],
            'without colon, with linebreak, without space'    => ["\nAA-BB-CC-DD-EE-FF\n",    'AABBCCDDEEFF'],
            'without colon, without linebreak, with space'    => [' AA-BB-CC-DD-EE-FF ',      'AABBCCDDEEFF'],

            /*/ Other accepted variations /*/
            'Actual mac - 1'               => ["\n52:54:00:14:91:69\n",    '525400149169'],
            'Actual mac - 2'               => ["\n00:16:3e:a9:73:f0\n",    '00163ea973f0'],
            'FF:FF:FF:FF:FF:FF'            => ["\nFF:FF:FF:FF:FF:FF\n",    'FFFFFFFFFFFF'],

            /*/ Incorrect variations that are also accepted /*/
            'Local host'                   => ["\n00:00:00:00:00:00\n",    '000000000000'],
            'Too long -- extra character'  => ["\nABC-01-23-45-67-89\n",   'BC0123456789'],
            'Too long -- extra tuple'      => ["\n01-AA-BB-CC-DD-EE-FF\n", '01AABBCCDDEE'],
        ];
    }
}
