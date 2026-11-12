<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Dce;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Ramsey\Uuid\Exception\DceSecurityException;
use Ramsey\Uuid\Provider\Dce\SystemDceSecurityProvider;
use Ramsey\Uuid\Test\MocksFunctions;
use Ramsey\Uuid\Test\TestCase;

use function array_merge;
use function preg_match;

class SystemDceSecurityProviderTest extends TestCase
{
    use MocksFunctions;

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGetUidThrowsExceptionIfShellExecDisabled(): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'foo bar shell_exec baz',
        );

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

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGetUidForPosixThrowsExceptionIfShellExecReturnsNull(): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Linux',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['id -u'],
            null,
        );

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
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWindowsBadValues')]
    public function testGetUidForWindowsThrowsExceptionIfShellExecForWhoAmIReturnsBadValues($value): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Windows_NT',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['whoami /user /fo csv /nh'],
            $value,
        );

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a user identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getUid();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWindowsGoodWhoAmIValues')]
    public function testGetUidForWindowsWhenShellExecForWhoAmIReturnsGoodValues(
        string $value,
        string $expectedId
    ): void {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Windows_NT',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['whoami /user /fo csv /nh'],
            $value,
        );

        $provider = new SystemDceSecurityProvider();

        $uid = $provider->getUid();

        $this->assertSame($expectedId, $uid->toString());
        $this->assertSame($uid, $provider->getUid());
    }

    /**
     * @return array<array{value: non-empty-string, expectedId: non-empty-string}>
     */
    public static function provideWindowsGoodWhoAmIValues(): array
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

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('providePosixTestValues')]
    public function testGetUidForPosixSystems(string $os, string $id): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            $os,
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['id -u'],
            $id,
        );

        $provider = new SystemDceSecurityProvider();

        $uid = $provider->getUid();

        $this->assertSame($id, $uid->toString());
        $this->assertSame($uid, $provider->getUid());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGetGidThrowsExceptionIfShellExecDisabled(): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'foo bar shell_exec baz',
        );

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

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGetGidForPosixThrowsExceptionIfShellExecReturnsNull(): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Linux',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['id -g'],
            null,
        );

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a group identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getGid();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('providePosixTestValues')]
    public function testGetGidForPosixSystems(string $os, string $id): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            $os,
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['id -g'],
            $id,
        );

        $provider = new SystemDceSecurityProvider();

        $gid = $provider->getGid();

        $this->assertSame($id, $gid->toString());
        $this->assertSame($gid, $provider->getGid());
    }

    /**
     * @param mixed $value
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWindowsBadValues')]
    public function testGetGidForWindowsThrowsExceptionWhenShellExecForNetUserReturnsBadValues($value): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Windows_NT',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['net user %username% | findstr /b /i "Local Group Memberships"'],
            $value,
        );

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
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWindowsBadGroupValues')]
    public function testGetGidForWindowsThrowsExceptionWhenShellExecForWmicGroupGetReturnsBadValues($value): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Windows_NT',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['net user %username% | findstr /b /i "Local Group Memberships"'],
            'Local Group Memberships   *Users',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            [fn (string $command) => preg_match(
                "/^wmic group get name,sid \| findstr \/b \/i (\"|\')Users(\"|\')$/",
                $command,
            ) === 1,
            ],
            $value,
        );

        $provider = new SystemDceSecurityProvider();

        $this->expectException(DceSecurityException::class);
        $this->expectExceptionMessage(
            'Unable to get a group identifier using the system DCE '
            . 'Security provider; please provide a custom identifier or '
            . 'use a different provider'
        );

        $provider->getGid();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('provideWindowsGoodNetUserAndWmicGroupValues')]
    public function testGetGidForWindowsSucceeds(
        string $netUserResponse,
        string $wmicGroupResponse,
        string $expectedGroup,
        string $expectedId
    ): void {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'ini_get',
            ['disable_functions'],
            'nothing',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'constant',
            ['PHP_OS'],
            'Windows_NT',
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            ['net user %username% | findstr /b /i "Local Group Memberships"'],
            $netUserResponse,
        );

        $this->expectFunctionCall(
            'Ramsey\Uuid\Provider\Dce',
            'shell_exec',
            [fn (string $command) => preg_match(
                "/^wmic group get name,sid \| findstr \/b \/i (\"|\'){$expectedGroup}(\"|\')$/",
                $command,
            ) === 1,
            ],
            $wmicGroupResponse,
        );

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
    public static function provideWindowsGoodNetUserAndWmicGroupValues(): array
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
    public static function providePosixTestValues(): array
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
    public static function provideWindowsBadValues(): array
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
    public static function provideWindowsBadGroupValues(): array
    {
        return array_merge(
            self::provideWindowsBadValues(),
            [
                ['value' => 'Users  Not a valid SID string'],
                ['value' => 'Users  344aab9758bb0d018b93739e7893fb3a'],
            ]
        );
    }
}
