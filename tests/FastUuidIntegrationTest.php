<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\Converter\Number\FastUuidNumberConverter;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\FastUuidRandomGenerator;
use Ramsey\Uuid\Generator\FastUuidTimeGenerator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\UuidFactory;

/**
 * @requires extension fast_uuid
 */
class FastUuidIntegrationTest extends TestCase
{
    public function testDefaultFeatureSetUsesFastUuidGenerators(): void
    {
        $features = new FeatureSet();

        $this->assertInstanceOf(FastUuidRandomGenerator::class, $features->getRandomGenerator());
        $this->assertInstanceOf(FastUuidTimeGenerator::class, $features->getTimeGenerator());
        $this->assertInstanceOf(FastUuidNumberConverter::class, $features->getNumberConverter());
    }

    public function testCustomCalculatorKeepsGenericNumberConverter(): void
    {
        $features = new FeatureSet();
        $features->setCalculator($this->createMock(CalculatorInterface::class));

        $this->assertInstanceOf(GenericNumberConverter::class, $features->getNumberConverter());
    }

    public function testDefaultFactoryKeepsLazyUuids(): void
    {
        $factory = new UuidFactory();

        $this->assertInstanceOf(LazyUuidFromString::class, $factory->uuid4());
        $this->assertInstanceOf(LazyUuidFromString::class, $factory->uuid1());
        $this->assertInstanceOf(LazyUuidFromString::class, $factory->uuid7());
    }

    public function testDefaultFactoryHonorsNodeAndClockSequence(): void
    {
        $uuid = (new UuidFactory())->uuid1(new Hexadecimal('0123456789ab'), 0x1234);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertSame(1, $fields->getVersion());
        $this->assertSame('0123456789ab', $fields->getNode()->toString());
        $this->assertSame('1234', $fields->getClockSeq()->toString());
    }

    public function testFixedTimeProviderStillControlsTheTimestamp(): void
    {
        $features = new FeatureSet();
        $features->setTimeProvider(new FixedTimeProvider(new Time(1, 0)));

        $uuid = (new UuidFactory($features))->uuid1();

        $this->assertSame('1', $uuid->getDateTime()->format('U'));
    }
}
