<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DefaultNameGenerator;
use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;

class FeatureSetTest extends TestCase
{
    public function testGuidBuilderIsSelected(): void
    {
        $featureSet = new FeatureSet(true, true);

        $this->assertInstanceOf(GuidBuilder::class, $featureSet->getBuilder());
    }

    public function testFallbackBuilderIsSelected(): void
    {
        $featureSet = new FeatureSet(false, true);

        $this->assertInstanceOf(FallbackBuilder::class, $featureSet->getBuilder());
    }

    public function testSetValidatorSetsTheProvidedValidator(): void
    {
        $validator = Mockery::mock(ValidatorInterface::class);

        $featureSet = new FeatureSet();
        $featureSet->setValidator($validator);

        $this->assertSame($validator, $featureSet->getValidator());
    }

    public function testGetTimeConverter(): void
    {
        $featureSet = new FeatureSet();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertInstanceOf(TimeConverterInterface::class, $featureSet->getTimeConverter());
    }

    public function testDefaultNameGeneratorIsSelected(): void
    {
        $featureSet = new FeatureSet();

        $this->assertInstanceOf(DefaultNameGenerator::class, $featureSet->getNameGenerator());
    }

    public function testPeclUuidTimeGeneratorIsSelected(): void
    {
        $featureSet = new FeatureSet(false, false, false, false, true);

        $this->assertInstanceOf(PeclUuidTimeGenerator::class, $featureSet->getTimeGenerator());
    }

    public function testGetCalculator(): void
    {
        $featureSet = new FeatureSet();

        $this->assertInstanceOf(BrickMathCalculator::class, $featureSet->getCalculator());
    }

    public function testSetNodeProvider(): void
    {
        $nodeProvider = Mockery::mock(NodeProviderInterface::class);
        $featureSet = new FeatureSet();
        $featureSet->setNodeProvider($nodeProvider);

        $this->assertSame($nodeProvider, $featureSet->getNodeProvider());
    }

    public function testGetUnixTimeGenerator(): void
    {
        $featureSet = new FeatureSet();

        $this->assertInstanceOf(UnixTimeGenerator::class, $featureSet->getUnixTimeGenerator());
    }
}
