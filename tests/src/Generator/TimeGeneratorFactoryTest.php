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
        $timeProvider = $this->createMock('Ramsey\Uuid\Provider\TimeProviderInterface');
        $nodeProvider = $this->createMock('Ramsey\Uuid\Provider\NodeProviderInterface');
        $timeConverter = $this->createMock('Ramsey\Uuid\Converter\TimeConverterInterface');

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();
        $this->assertInstanceOf('Ramsey\Uuid\Generator\TimeGeneratorInterface', $generator);
    }
}
