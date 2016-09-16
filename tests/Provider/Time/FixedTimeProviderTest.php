<?php

namespace Ramsey\Uuid\Test\Provider\Time;

use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Test\TestCase;

class FixedTimeProviderTest extends TestCase
{

    public function testConstructorRequiresSecAndUsec()
    {
        $this->setExpectedException('InvalidArgumentException');
        $provider = new FixedTimeProvider([]);
    }

    public function testCurrentTimeReturnsTimestamp()
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $provider = new FixedTimeProvider($timestamp);
        $this->assertEquals($timestamp, $provider->currentTime());
    }

    public function testCurrentTimeReturnsTimestampAfterChange()
    {
        $timestamp = ['sec' => 1458844556, 'usec' => 200997];
        $provider = new FixedTimeProvider($timestamp);

        $newTimestamp = ['sec' => 1050804050, 'usec' => '30192'];
        $provider->setSec($newTimestamp['sec']);
        $provider->setUsec($newTimestamp['usec']);

        $this->assertEquals($newTimestamp, $provider->currentTime());
    }
}
