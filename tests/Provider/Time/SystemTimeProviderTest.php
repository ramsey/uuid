<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Time;

use AspectMock\Test as AspectMock;
use Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

class SystemTimeProviderTest extends TestCase
{
    public function testCurrentTimeReturnsTimestampArray(): void
    {
        $provider = new SystemTimeProvider();
        $time = $provider->currentTime();
        $this->assertArrayHasKey('sec', $time);
        $this->assertArrayHasKey('usec', $time);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testCurrentTimeUsesGettimeofday(): void
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $func = AspectMock::func('Ramsey\Uuid\Provider\Time', 'gettimeofday', $timestamp);
        $provider = new SystemTimeProvider();

        $this->assertSame($timestamp, $provider->currentTime());
        $func->verifyInvokedOnce();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetTimeUsesGettimeofday(): void
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $func = AspectMock::func('Ramsey\Uuid\Provider\Time', 'gettimeofday', $timestamp);
        $provider = new SystemTimeProvider();
        $time = $provider->getTime();

        $this->assertInstanceOf(Time::class, $time);
        $this->assertSame('1458844556', $time->getSeconds()->toString());
        $this->assertSame('200997', $time->getMicroSeconds()->toString());

        $func->verifyInvokedOnce();
    }
}
