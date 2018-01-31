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
 *
 * This class mostly tests happy-path (success scenario) and leaves various
 * sad-path (failure scenarios) untested.
 *
 * @TODO: Add tests for failure scenario's
 */
class SystemNodeProviderTest extends TestCase
{
    const MOCK_GLOB = 'glob';
    const MOCK_UNAME = 'php_uname';
    const MOCK_PASSTHRU = 'passthru';
    const MOCK_FILE_GET_CONTENTS = 'file_get_contents';

    const PROVIDER_NAMESPACE = 'Ramsey\\Uuid\\Provider\\Node';

    /** @var FuncProxy[] */
    private $functionProxies = [];

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetNodeReturnsSystemNodeFromMacAddress()
    {
        /*/ Arrange mocks for native functions /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () {echo "\nAA-BB-CC-DD-EE-FF\n";},
            'NOT LINUX'
        );

        /*/ Act upon the system under test/*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert the result match expectations /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

        $this->assertSame('AABBCCDDEEFF', $node);

        $this->assertTrue(ctype_xdigit($node), 'Node should be a hexadecimal string. Actual node: ' . $node);
        $length = strlen($node);
        $lengthError = 'Node should be 12 characters. Actual length: ' . $length . PHP_EOL . ' Actual node: ' . $node;
        $this->assertSame(12, $length, $lengthError);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider notationalFormatsDataProvider
     *
     * @param $formatted
     * @param $expected
     */
    public function testGetNodeReturnsNodeStrippedOfNotationalFormatting($formatted, $expected)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            null,
            null,
            function () use ($formatted) {echo "\n{$formatted}\n";},
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

        $this->assertEquals($expected, $node);
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
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

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
            function () {echo 'some string that does not match the mac address';},
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

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
            function () {echo 'some string that does not match the mac address';},
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $provider->getNode();
        $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider osCommandDataProvider
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
            $os
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
        $this->assertMockFunctions($fileGetContentsAssert, $globBodyAssert, [$command], [['a'], ['s']]);
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
            function () {echo "\nAA-BB-CC-DD-EE-FF\n";},
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();
        $node2 = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

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
            function () {echo "\nAA-BB-CC-DD-EE-FF\n";},
            'NOT LINUX'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();
        $node2 = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, null, ['netstat -ie 2>&1'], [['a'], ['s']]);

        $this->assertEquals($node, $node2);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     * @dataProvider osCommandDataProvider
     *
     * @param $os
     * @param $command
     */
    public function testCallGetsysfsOnLinux($os, $command)
    {
        /*/ Arrange /*/
        $this->arrangeMockFunctions(
            function () {
                static $macs = ["\n00:00:00:00:00:00\n", "\n01:02:03:04:05:06\n"];
                return array_shift($macs);
            },
            ['mock address path 1', 'mock address path 2'],
            function () {echo "\n01-02-03-04-05-06\n";},
            $os
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $fileGetContentsAssert = null;
        $globBodyAssert = null;
        $passthruBodyAssert = [$command];
        $unameBodyAssert = [['a'], ['s']];

        if ($os === 'Linux') {
            $fileGetContentsAssert = [['mock address path 1'], ['mock address path 2']];
            $globBodyAssert = ['/sys/class/net/*/address'];
            $passthruBodyAssert = null;
            $unameBodyAssert = ['s'];
        }
        $this->assertMockFunctions($fileGetContentsAssert, $globBodyAssert, $passthruBodyAssert, $unameBodyAssert);

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
            function () {echo "\n01-02-03-04-05-06\n";},
            'Linux'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, ['/sys/class/net/*/address'], ['netstat -ie 2>&1'], [['a'], ['s']]);

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
            function () {echo "\n01-02-03-04-05-06\n";},
            'Linux'
        );

        /*/ Act /*/
        $provider = new SystemNodeProvider();
        $node = $provider->getNode();

        /*/ Assert /*/
        $this->assertMockFunctions(null, ['/sys/class/net/*/address'], ['netstat -ie 2>&1'], [['a'], ['s']]);

        $this->assertEquals('010203040506', $node);
    }

    public function notationalFormatsDataProvider()
    {
        return [
            ['01-23-45-67-89-ab', '0123456789ab'],
            ['01:23:45:67:89:ab', '0123456789ab']
        ];
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
     * Replaces the return value for functions with the given value or callback.
     *
     * @param callback|mixed|null $fileGetContentsBody
     * @param callback|mixed|null $globBody
     * @param callback|mixed|null $passthruBody
     * @param callback|mixed|null $unameBody
     */
    private function arrangeMockFunctions($fileGetContentsBody, $globBody, $passthruBody, $unameBody)
    {
        $mockFunction = [
            self::MOCK_FILE_GET_CONTENTS => $fileGetContentsBody,
            self::MOCK_GLOB => $globBody,
            self::MOCK_PASSTHRU => $passthruBody,
            self::MOCK_UNAME => $unameBody,
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
     */
    private function assertMockFunctions($fileGetContentsAssert, $globBodyAssert, $passthruBodyAssert, $unameBodyAssert)
    {
        $mockFunctionAsserts = [
            self::MOCK_FILE_GET_CONTENTS => $fileGetContentsAssert,
            self::MOCK_GLOB => $globBodyAssert,
            self::MOCK_PASSTHRU => $passthruBodyAssert,
            self::MOCK_UNAME => $unameBodyAssert,
        ];

        array_walk($mockFunctionAsserts,  function ($asserts, $key) {
            if ($asserts === null) {
                $this->functionProxies[$key]->verifyNeverInvoked();
            } elseif(is_array($asserts)) {
                foreach ($asserts as $assert) {
                    $this->functionProxies[$key]->verifyInvokedOnce($assert);
                }
            } elseif(is_callable($asserts)) {
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
}
