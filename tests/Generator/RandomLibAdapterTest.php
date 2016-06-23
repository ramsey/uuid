<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\RandomLibAdapter;
use Ramsey\Uuid\Test\TestCase;
use Mockery;
use RandomLib\Factory as RandomLibFactory;
use RandomLib\Generator;

/**
 * Class RandomLibAdapterTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\RandomLibAdapter
 */
class RandomLibAdapterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAdapterWithGeneratorDoesNotCreateGenerator()
    {
        $factory = Mockery::mock('overload:' . RandomLibFactory::class);
        $factory->shouldNotReceive('getHighStrengthGenerator')
            ->getMock();

        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();
        new RandomLibAdapter($generator);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAdapterWithoutGeneratorGreatesGenerator()
    {
        $factory = Mockery::mock('overload:' . RandomLibFactory::class);
        $factory->shouldReceive('getHighStrengthGenerator')
            ->once()
            ->getMock();

        new RandomLibAdapter();
    }

    public function testGenerateUsesGenerator()
    {
        $length = 10;
        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with($length);

        $adapter = new RandomLibAdapter($generator);
        $adapter->generate($length);
    }

    public function testGenerateReturnsString()
    {
        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->willReturn('random-string');

        $adapter = new RandomLibAdapter($generator);
        $result = $adapter->generate(1);
        $this->assertEquals('random-string', $result);
    }
}
