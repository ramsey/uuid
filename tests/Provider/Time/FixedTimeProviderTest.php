<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Provider\Time;

use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

class FixedTimeProviderTest extends TestCase
{
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
        $this->assertSame('200997', $provider->getTime()->getMicroseconds()->toString());

        $provider->setSec(1050804050);

        $this->assertSame('1050804050', $provider->getTime()->getSeconds()->toString());
        $this->assertSame('200997', $provider->getTime()->getMicroseconds()->toString());

        $provider->setUsec(30192);

        $this->assertSame('1050804050', $provider->getTime()->getSeconds()->toString());
        $this->assertSame('30192', $provider->getTime()->getMicroseconds()->toString());

        $this->assertNotSame($time, $provider->getTime());
    }
}
