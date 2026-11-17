<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\FastUuidRandomGenerator;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Ramsey\Uuid\Test\TestCase;

use function extension_loaded;

class RandomGeneratorFactoryTest extends TestCase
{
    public function testFactoryReturnsRandomBytesGenerator(): void
    {
        if (extension_loaded('fast_uuid')) {
            $this->markTestSkipped('The factory returns FastUuidRandomGenerator when ext-fast_uuid is loaded');
        }

        $generator = (new RandomGeneratorFactory())->getGenerator();

        $this->assertInstanceOf(RandomBytesGenerator::class, $generator);
    }

    /**
     * @requires extension fast_uuid
     */
    public function testFactoryReturnsFastUuidRandomGenerator(): void
    {
        $generator = (new RandomGeneratorFactory())->getGenerator();

        $this->assertInstanceOf(FastUuidRandomGenerator::class, $generator);
    }
}
