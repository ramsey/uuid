<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\TimeGeneratorFactory;
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
        $timeProvider = $this->getMockBuilder('Ramsey\Uuid\Provider\TimeProviderInterface')->getMock();
        $nodeProvider = $this->getMockBuilder('Ramsey\Uuid\Provider\NodeProviderInterface')->getMock();
        $timeConverter = $this->getMockBuilder('Ramsey\Uuid\Converter\TimeConverterInterface')->getMock();

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();
        $this->assertInstanceOf('Ramsey\Uuid\Generator\TimeGeneratorInterface', $generator);
    }
}
