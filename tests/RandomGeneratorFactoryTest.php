<?php

namespace Ramsey\Uuid;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsNonOpenSslGeneratorWithForceNoOpenSsl()
    {
        RandomGeneratorFactory::$forceNoOpensslRandomPseudoBytes = true;

        $generator = RandomGeneratorFactory::getGenerator();

        $this->assertNotInstanceOf('\Ramsey\Uuid\Generator\OpenSslGenerator', $generator);
    }

    public function testFactoryReturnsOpenSslGeneratorIfAvailable()
    {
        RandomGeneratorFactory::$forceNoOpensslRandomPseudoBytes = false;

        $generator = RandomGeneratorFactory::getGenerator();

        $this->assertInstanceOf('\Ramsey\Uuid\Generator\OpenSslGenerator', $generator);
    }
}
