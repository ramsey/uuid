<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\MockObject\Stub;
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
        /** @var Stub & TimeProviderInterface $timeProvider */
        $timeProvider = $this->createStub(TimeProviderInterface::class);

        /** @var Stub & NodeProviderInterface $nodeProvider */
        $nodeProvider = $this->createStub(NodeProviderInterface::class);

        /** @var Stub & TimeConverterInterface $timeConverter */
        $timeConverter = $this->createStub(TimeConverterInterface::class);

        $factory = new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider);
        $generator = $factory->getGenerator();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertInstanceOf(TimeGeneratorInterface::class, $generator);
    }
}
