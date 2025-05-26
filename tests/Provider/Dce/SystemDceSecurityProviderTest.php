<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Dce;

use Mockery;
use Ramsey\Uuid\Exception\DceSecurityException;
use Ramsey\Uuid\Provider\Dce\SystemDceSecurityProvider;
use Ramsey\Uuid\Test\TestCase;
use phpmock\mockery\PHPMockery;

use function array_merge;

class SystemDceSecurityProviderTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetUidThrowsExceptionIfShellExecDisabled(): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('foo bar shell_exec baz');

        $provider = new SystemDceSecurityProvider();

        // Test that we catch the exception multiple times, but the ini_get()
        // function is called only once.
        $caughtException = 0;

        for ($i = 1; $i <= 5; $i++) {
            try {
                $provider->getUid();
            } catch (DceSecurityException $e) {
                $caughtException++;

                $this->assertSame(
                    'Unable to get a user identifier using the system DCE '
                    . 'Security provider; please provide a custom identifier or '
                    . 'use a different provider',
                    $e->getMessage()
                );
            }
        }

        $this->assertSame(5, $caughtException);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetUidForPosixThrowsExceptionIfShellExecReturnsNull(): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Linux');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('id -u')->once()->andReturnNull();

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a user identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getUid();
    }

    /**
     * @param mixed $value
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider provideWindowsBadValues
     */
    public function testGetUidForWindowsThrowsExceptionIfShellExecForWhoAmIReturnsBadValues($value): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Windows_NT');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('whoami /user /fo csv /nh')->once()->andReturn($value);

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a user identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getUid();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider provideWindowsGoodWhoAmIValues
     */
    public function testGetUidForWindowsWhenShellExecForWhoAmIReturnsGoodValues(
        string $value,
        string $expectedId
    ): void {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Windows_NT');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('whoami /user /fo csv /nh')->once()->andReturn($value);

        $provider = new SystemDceSecurityProvider();

        $uid = $provider->getUid();

        $this->assertSame($expectedId, $uid->toString());
        $this->assertSame($uid, $provider->getUid());
    }

    /**
     * @return array<array{value: non-empty-string, expectedId: non-empty-string}>
     */
    public function provideWindowsGoodWhoAmIValues(): array
    {
        return [
            [
                'value' => '"Melilot Sackville","S-1-5-21-7375663-6890924511-1272660413-2944159"',
                'expectedId' => '2944159',
            ],
            [
                'value' => '"Brutus Sandheaver","S-1-3-12-1234525106-3567804255-30012867-1437"',
                'expectedId' => '1437',
            ],
            [
                'value' => '"Cora Rumble","S-345"',
                'expectedId' => '345',
            ],
        ];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider providePosixTestValues
     */
    public function testGetUidForPosixSystems(string $os, string $id): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn($os);

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('id -u')->once()->andReturn($id);

        $provider = new SystemDceSecurityProvider();

        $uid = $provider->getUid();

        $this->assertSame($id, $uid->toString());
        $this->assertSame($uid, $provider->getUid());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetGidThrowsExceptionIfShellExecDisabled(): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('foo bar shell_exec baz');

        $provider = new SystemDceSecurityProvider();

        // Test that we catch the exception multiple times, but the ini_get()
        // function is called only once.
        $caughtException = 0;

        for ($i = 1; $i <= 5; $i++) {
            try {
                $provider->getGid();
            } catch (DceSecurityException $e) {
                $caughtException++;

                $this->assertSame(
                    'Unable to get a group identifier using the system DCE '
                    . 'Security provider; please provide a custom identifier or '
                    . 'use a different provider',
                    $e->getMessage()
                );
            }
        }

        $this->assertSame(5, $caughtException);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetGidForPosixThrowsExceptionIfShellExecReturnsNull(): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Linux');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('id -g')->once()->andReturnNull();

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a group identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getGid();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider providePosixTestValues
     */
    public function testGetGidForPosixSystems(string $os, string $id): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn($os);

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('id -g')->once()->andReturn($id);

        $provider = new SystemDceSecurityProvider();

        $gid = $provider->getGid();

        $this->assertSame($id, $gid->toString());
        $this->assertSame($gid, $provider->getGid());
    }

    /**
     * @param mixed $value
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider provideWindowsBadValues
     */
    public function testGetGidForWindowsThrowsExceptionWhenShellExecForNetUserReturnsBadValues($value): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Windows_NT');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec'
        )->with('net user %username% | findstr /b /i "Local Group Memberships"')->once()->andReturn($value);

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a group identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getGid();
    }

    /**
     * @param mixed $value
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider provideWindowsBadGroupValues
     */
    public function testGetGidForWindowsThrowsExceptionWhenShellExecForWmicGroupGetReturnsBadValues($value): void
    {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Windows_NT');

        $shellExec = PHPMockery::mock('Ramsey\Uuid\Provider\Dce', 'shell_exec');

        $shellExec
            ->with('net user %username% | findstr /b /i "Local Group Memberships"')
            ->once()
            ->andReturn('Local Group Memberships   *Users');

        $shellExec
            ->with(Mockery::pattern("/^wmic group get name,sid \| findstr \/b \/i (\"|\')Users(\"|\')$/"))
            ->once()
            ->andReturn($value);

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a group identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getGid();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider provideWindowsGoodNetUserAndWmicGroupValues
     */
    public function testGetGidForWindowsSucceeds(
        string $netUserResponse,
        string $wmicGroupResponse,
        string $expectedGroup,
        string $expectedId
    ): void {
        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get'
        )->with('disable_functions')->once()->andReturn('nothing');

        PHPMockery::mock(
            'Ramsey\Uuid\Provider\Dce',
            'constant'
        )->with('PHP_OS')->once()->andReturn('Windows_NT');

        $shellExec = PHPMockery::mock('Ramsey\Uuid\Provider\Dce', 'shell_exec');

        $shellExec
            ->with('net user %username% | findstr /b /i "Local Group Memberships"')
            ->once()
            ->andReturn($netUserResponse);

        $shellExec
            ->with(Mockery::pattern("/^wmic group get name,sid \| findstr \/b \/i (\"|\'){$expectedGroup}(\"|\')$/"))
            ->once()
            ->andReturn($wmicGroupResponse);

        $provider = new SystemDceSecurityProvider();

        $gid = $provider->getGid();

        $this->assertSame($expectedId, $gid->toString());
        $this->assertSame($gid, $provider->getGid());
    }

    /**
     * @return array<array{
     *     netUserResponse: non-empty-string,
     *     wmicGroupResponse: non-empty-string,
     *     expectedGroup: non-empty-string,
     *     expectedId: non-empty-string,
     * }>
     */
    public function provideWindowsGoodNetUserAndWmicGroupValues(): array
    {
        return [
            [
                'netUserResponse' => 'Local Group Memberships    *Administrators  *Users',
                'wmicGroupResponse' => 'Administrators  S-1-5-32-544',
                'expectedGroup' => 'Administrators',
                'expectedId' => '544',
            ],
            [
                'netUserResponse' => 'Local Group Memberships    Users',
                'wmicGroupResponse' => 'Users  S-1-5-32-545',
                'expectedGroup' => 'Users',
                'expectedId' => '545',
            ],
            [
                'netUserResponse' => 'Local Group Memberships    Guests  Nobody',
                'wmicGroupResponse' => 'Guests  S-1-5-32-546',
                'expectedGroup' => 'Guests',
                'expectedId' => '546',
            ],
            [
                'netUserResponse' => 'Local Group Memberships   Some Group  Another Group',
                'wmicGroupResponse' => 'Some Group    S-1-5-80-19088743-1985229328-4294967295-1324',
                'expectedGroup' => 'Some Group',
                'expectedId' => '1324',
            ],
        ];
    }

    /**
     * @return array<array{os: non-empty-string, id: non-empty-string}>
     */
    public function providePosixTestValues(): array
    {
        return [
            ['os' => 'Darwin', 'id' => '1042'],
            ['os' => 'FreeBSD', 'id' => '672'],
            ['os' => 'GNU', 'id' => '1008'],
            ['os' => 'Linux', 'id' => '567'],
            ['os' => 'NetBSD', 'id' => '7234'],
            ['os' => 'OpenBSD', 'id' => '2347'],
            ['os' => 'OS400', 'id' => '1234'],
        ];
    }

    /**
     * @return array<array{value: string | null}>
     */
    public function provideWindowsBadValues(): array
    {
        return [
            ['value' => null],
            ['value' => 'foobar'],
            ['value' => 'foo,bar,baz'],
            ['value' => ''],
            ['value' => '1234'],
            ['value' => 'Local Group Memberships'],
            ['value' => 'Local Group Memberships    ****  Foo'],
        ];
    }

    /**
     * @return array<array{value: string | null}>
     */
    public function provideWindowsBadGroupValues(): array
    {
        return array_merge(
            $this->provideWindowsBadValues(),
            [
                ['value' => 'Users  Not a valid SID string'],
                ['value' => 'Users  344aab9758bb0d018b93739e7893fb3a'],
            ]
        );
    }
}
