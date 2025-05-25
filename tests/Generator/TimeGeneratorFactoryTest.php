<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;

class TimeGeneratorFactoryTest extends TestCase
{
    public function testGeneratorReturnsNewGenerator(): void
    {
        /** @var MockObject & TimeProviderInterface $timeProvider */
        $timeProvider = $this->getMockBuilder(TimeProviderInterface::class)->getMock();

        /** @var MockObject & NodeProviderInterface $nodeProvider */
        $nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();

        /** @var MockObject & TimeConverterInterface $timeConverter */
        $timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertInstanceOf(TimeGeneratorInterface::class, $generator);
    }
}
