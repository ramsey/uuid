<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Generator\RandomLibAdapter;
use Ramsey\Uuid\Test\TestCase;
use RandomLib\Factory as RandomLibFactory;
use RandomLib\Generator;

class RandomLibAdapterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAdapterWithGeneratorDoesNotCreateGenerator(): void
    {
        $factory = Mockery::mock('overload:' . RandomLibFactory::class);
        $factory->shouldNotReceive('getHighStrengthGenerator');

        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertInstanceOf(RandomLibAdapter::class, new RandomLibAdapter($generator));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAdapterWithoutGeneratorCreatesGenerator(): void
    {
        $generator = Mockery::mock(Generator::class);

        /** @var RandomLibFactory&MockInterface $factory */
        $factory = Mockery::mock('overload:' . RandomLibFactory::class);
        $factory->expects()->getHighStrengthGenerator()->andReturns($generator);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertInstanceOf(RandomLibAdapter::class, new RandomLibAdapter());
    }

    public function testGenerateUsesGenerator(): void
    {
        $length = 10;
        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with($length)
            ->willReturn('foo');

        $adapter = new RandomLibAdapter($generator);
        $adapter->generate($length);
    }

    public function testGenerateReturnsString(): void
    {
        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->willReturn('random-string');

        $adapter = new RandomLibAdapter($generator);
        $result = $adapter->generate(1);
        $this->assertSame('random-string', $result);
    }
}
