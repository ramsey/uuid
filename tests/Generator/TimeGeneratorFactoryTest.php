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
        $timeProvider = $this->getMock('Ramsey\Uuid\Provider\TimeProviderInterface');
        $nodeProvider = $this->getMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $timeConverter = $this->getMock('Ramsey\Uuid\Converter\TimeConverterInterface');

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();
        $this->assertInstanceOf('Ramsey\Uuid\Generator\TimeGeneratorInterface', $generator);
    }
}
