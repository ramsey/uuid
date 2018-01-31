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
 * @TODO: Replace mock of the class-under-test with an actual object instance
 */
class SystemNodeProviderTest extends TestCase
{
    const MOCK_GLOB = 'glob';
    const MOCK_UNAME = 'php_uname';
    const MOCK_PASSTHRU = 'passthru';
    const MOCK_FILE_GET_CONTENTS = 'file_get_contents';
    const PROVIDER_NAMESPACE = 'Ramsey\\Uuid\\Provider\\Node';

    /**
     * @var FuncProxy[]
     */
    private $functionProxies = [
        self::MOCK_FILE_GET_CONTENTS => null,
        self::MOCK_GLOB => null,
        self::MOCK_PASSTHRU => null,
        self::MOCK_UNAME => null,
    ];

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
     * @param mixed| callback $fileGetContentsBody
     * @param mixed| callback $globBody
     * @param mixed| callback $passthruBody
     * @param mixed| callback $unameBody
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
