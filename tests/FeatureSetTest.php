<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use JMac\Testing\Double;
use JMac\Testing\Matching\Argument;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DefaultNameGenerator;
use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Generator\UnixTimeGenerator;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
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
        $validator = Double::for(ValidatorInterface::class);

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
        $featureSet = new FeatureSet(false, false, false, true);

        $this->assertInstanceOf(PeclUuidTimeGenerator::class, $featureSet->getTimeGenerator());
    }

    public function testGetCalculator(): void
    {
        $featureSet = new FeatureSet();

        $this->assertInstanceOf(BrickMathCalculator::class, $featureSet->getCalculator());
    }

    public function testSetNodeProvider(): void
    {
        $nodeProvider = Double::for(NodeProviderInterface::class);
        $featureSet = new FeatureSet();
        $featureSet->setNodeProvider($nodeProvider);

        $this->assertSame($nodeProvider, $featureSet->getNodeProvider());
    }

    public function testGetUnixTimeGenerator(): void
    {
        $featureSet = new FeatureSet();

        $this->assertInstanceOf(UnixTimeGenerator::class, $featureSet->getUnixTimeGenerator());
    }

    public function testSetTimeProviderIsUsedForDceSecurityUuids(): void
    {
        $featureSet = new FeatureSet();
        $featureSet->setTimeProvider(new FixedTimeProvider(new Time(1_000_000_000, 0)));

        $uuid = (new UuidFactory($featureSet))->uuid2(Uuid::DCE_DOMAIN_ORG, new IntegerObject(1));

        $this->assertInstanceOf(UuidV2::class, $uuid);
        $this->assertSame('2001-09-09', $uuid->getDateTime()->format('Y-m-d'));
    }

    public function testSetNodeProviderIsUsedForDceSecurityUuids(): void
    {
        $featureSet = new FeatureSet();
        $featureSet->setNodeProvider(new StaticNodeProvider(new Hexadecimal('0000000000aa')));

        $uuid = (new UuidFactory($featureSet))->uuid2(Uuid::DCE_DOMAIN_ORG, new IntegerObject(1));

        $this->assertStringEndsWith('-0100000000aa', $uuid->toString());
    }

    public function testSetCalculatorRebuildsBuilderAndCodec(): void
    {
        $calculator = Double::for(CalculatorInterface::class)->strict();
        $calculator->expects('fromBase')
            ->with('ff6f8cb0c57d11e19b210800200c9a66', 16)
            ->times(2)
            ->returns(new IntegerObject(42));

        $featureSet = new FeatureSet(true);
        $featureSet->setCalculator($calculator);

        $decoded = $featureSet->getCodec()->decode('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $built = $featureSet->getBuilder()->build($featureSet->getCodec(), $decoded->getBytes());

        $this->assertInstanceOf(GuidBuilder::class, $featureSet->getBuilder());
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $built->toString());
        $this->assertSame('42', $decoded->getInteger()->toString());
        $this->assertSame('42', $built->getInteger()->toString());
    }

    public function testSetCalculatorRebuildsDceSecurityGenerator(): void
    {
        $calculator = Double::for(CalculatorInterface::class)->strict();
        $calculator->expects('toBase')->with(Argument::any(), 16)->returns('abcdef01');

        $featureSet = new FeatureSet();
        $featureSet->setCalculator($calculator);

        $bytes = $featureSet->getDceSecurityGenerator()->generate(Uuid::DCE_DOMAIN_ORG, new IntegerObject(1001));

        $this->assertSame('abcdef01', bin2hex(substr($bytes, 0, 4)));
    }
}
