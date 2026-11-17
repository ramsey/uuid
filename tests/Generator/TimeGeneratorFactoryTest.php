<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Generator\FastUuidTimeGenerator;
use Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

use function extension_loaded;

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

    public function testGeneratorReturnsDefaultTimeGeneratorWithoutExtension(): void
    {
        if (extension_loaded('fast_uuid')) {
            $this->markTestSkipped('The factory returns FastUuidTimeGenerator when ext-fast_uuid is loaded');
        }

        $this->assertInstanceOf(DefaultTimeGenerator::class, $this->getGenerator(new SystemTimeProvider()));
    }

    /**
     * @requires extension fast_uuid
     */
    public function testGeneratorReturnsFastUuidTimeGeneratorForSystemTime(): void
    {
        $this->assertInstanceOf(FastUuidTimeGenerator::class, $this->getGenerator(new SystemTimeProvider()));
    }

    /**
     * @requires extension fast_uuid
     */
    public function testGeneratorReturnsDefaultTimeGeneratorForOtherTimeProviders(): void
    {
        $this->assertInstanceOf(
            DefaultTimeGenerator::class,
            $this->getGenerator(new FixedTimeProvider(new Time(1, 0))),
        );
    }

    private function getGenerator(TimeProviderInterface $timeProvider): TimeGeneratorInterface
    {
        /** @var MockObject & NodeProviderInterface $nodeProvider */
        $nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();

        /** @var MockObject & TimeConverterInterface $timeConverter */
        $timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();

        return (new TimeGeneratorFactory($nodeProvider, $timeConverter, $timeProvider))->getGenerator();
    }
}
