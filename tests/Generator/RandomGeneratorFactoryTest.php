<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Ramsey\Uuid\Test\TestCase;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsRandomBytesGenerator(): void
    {
        $generator = (new RandomGeneratorFactory())->getGenerator();

        self::assertInstanceOf(RandomBytesGenerator::class, $generator);
    }
}
