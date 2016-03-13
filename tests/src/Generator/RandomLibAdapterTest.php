<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\RandomLibAdapter;
use Ramsey\Uuid\Test\TestCase;
use Mockery;

class RandomLibAdapterTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAdapterWithGeneratorDoesNotCreateGenerator()
    {
        $factory = Mockery::mock('overload:RandomLib\Factory');
        $factory->shouldNotReceive('getMediumStrengthGenerator')
                ->getMock();

        $generator = $this->getMockBuilder('RandomLib\Generator')
                          ->disableOriginalConstructor()
                          ->getMock();
        new RandomLibAdapter($generator);
    }

    public function testAdapterWithoutGeneratorGreatesGenerator()
    {
        $factory = Mockery::mock('overload:RandomLib\Factory');
        $factory->shouldReceive('getMediumStrengthGenerator')
                ->once()
                ->getMock();

        new RandomLibAdapter();
    }
}