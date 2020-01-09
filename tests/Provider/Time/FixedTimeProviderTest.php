<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Time;

use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

class FixedTimeProviderTest extends TestCase
{
    public function testConstructorRequiresSecAndUsec(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array must contain sec and usec keys.');

        new FixedTimeProvider([]);
    }

    public function testCurrentTimeReturnsTimestamp(): void
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $provider = new FixedTimeProvider($timestamp);

        $this->assertEquals($timestamp, $provider->currentTime());
    }

    public function testCurrentTimeReturnsTimestampAfterChange(): void
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $provider = new FixedTimeProvider($timestamp);

        $newTimestamp = ['sec' => 1050804050, 'usec' => 30192];
        $provider->setSec($newTimestamp['sec']);
        $provider->setUsec($newTimestamp['usec']);

        $this->assertEquals($newTimestamp, $provider->currentTime());
    }

    public function testGetTimeReturnsTime(): void
    {
        $time = new Time(1458844556, 200997);
        $provider = new FixedTimeProvider($time);

        $this->assertSame($time, $provider->getTime());
    }

    public function testGetTimeReturnsTimeAfterChange(): void
    {
        $time = new Time(1458844556, 200997);
        $provider = new FixedTimeProvider($time);

        $this->assertSame('1458844556', $provider->getTime()->getSeconds()->toString());
        $this->assertSame('200997', $provider->getTime()->getMicroSeconds()->toString());

        $provider->setSec(1050804050);

        $this->assertSame('1050804050', $provider->getTime()->getSeconds()->toString());
        $this->assertSame('200997', $provider->getTime()->getMicroSeconds()->toString());

        $provider->setUsec(30192);

        $this->assertSame('1050804050', $provider->getTime()->getSeconds()->toString());
        $this->assertSame('30192', $provider->getTime()->getMicroSeconds()->toString());

        $this->assertNotSame($time, $provider->getTime());
    }
}
