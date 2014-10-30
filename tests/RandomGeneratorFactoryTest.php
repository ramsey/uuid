<?php

namespace Rhumsaa\Uuid;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsNonOpenSslGeneratorWithForceNoOpenSsl()
    {
        RandomGeneratorFactory::$forceNoOpensslRandomPseudoBytes = true;

        $generator = RandomGeneratorFactory::getGenerator();

        $this->assertNotInstanceOf('\Rhumsaa\Uuid\Generator\OpenSslGenerator', $generator);
    }

    public function testFactoryReturnsOpenSslGeneratorIfAvailable()
    {
        RandomGeneratorFactory::$forceNoOpensslRandomPseudoBytes = false;

        $generator = RandomGeneratorFactory::getGenerator();

        $this->assertInstanceOf('\Rhumsaa\Uuid\Generator\OpenSslGenerator', $generator);
    }
}
