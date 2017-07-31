<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsRandomBytesGenerator()
    {
        $generator = RandomGeneratorFactory::getGenerator();

        $this->assertInstanceOf(RandomBytesGenerator::class, $generator);
    }
}
