<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class TimeGeneratorFactoryTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\TimeGeneratorFactory
 */
class TimeGeneratorFactoryTest extends TestCase
{
    public function testGeneratorReturnsNewGenerator()
    {
        $timeProvider = $this->getMockBuilder(TimeProviderInterface::class)->getMock();
        $nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();
        $this->assertInstanceOf(TimeGeneratorInterface::class, $generator);
    }
}
