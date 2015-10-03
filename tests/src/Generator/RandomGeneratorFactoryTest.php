<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsNonOpenSslGeneratorWithForceNoOpenSsl()
    {
        RandomGeneratorFactory::$forceNoRandomBytes = true;
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
