<?php

namespace Rhumsaa\Uuid;

use Rhumsaa\Uuid\Provider\Time\SystemTimeProvider;
use Rhumsaa\Uuid\Provider\Time\FixedTimeProvider;
use Rhumsaa\Uuid\Generator\CombGenerator;

class PeclUuidTest extends UuidTest
{
    protected function setUp()
    {
        Uuid::setFactory(new PeclUuidFactory(new UuidFactory()));

        RandomGeneratorFactory::$forceNoOpensslRandomPseudoBytes = false;
    }
}
