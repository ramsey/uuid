<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use Mockery\MockInterface;
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
        $realCalculator = new BrickMathCalculator();
        $calculator = $this->mockCalculator($realCalculator);
        $calculator->expects('fromBase')->twice()->andReturnUsing([$realCalculator, 'fromBase']);

        $featureSet = new FeatureSet(true);
        $featureSet->setCalculator($calculator);

        $decoded = $featureSet->getCodec()->decode('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $built = $featureSet->getBuilder()->build($featureSet->getCodec(), $decoded->getBytes());

        $this->assertInstanceOf(GuidBuilder::class, $featureSet->getBuilder());
        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $built->toString());
        $this->assertSame('339532337419071774305803111643925486182', $decoded->getInteger()->toString());
        $this->assertSame('339532337419071774305803111643925486182', $built->getInteger()->toString());
    }

    public function testSetCalculatorRebuildsDceSecurityGenerator(): void
    {
        $realCalculator = new BrickMathCalculator();
        $calculator = $this->mockCalculator($realCalculator);
        $calculator->expects('toBase')->once()->andReturnUsing([$realCalculator, 'toBase']);

        $featureSet = new FeatureSet();
        $featureSet->setCalculator($calculator);

        $bytes = $featureSet->getDceSecurityGenerator()->generate(Uuid::DCE_DOMAIN_ORG, new IntegerObject(1001));

        $this->assertSame('000003e9', bin2hex(substr($bytes, 0, 4)));
    }

    /**
     * Returns a mock calculator that passes every call to the given calculator
     *
     * @return CalculatorInterface&MockInterface
     */
    private function mockCalculator(CalculatorInterface $realCalculator): CalculatorInterface
    {
        $calculator = Mockery::mock(CalculatorInterface::class);
        $calculator->allows('add')->andReturnUsing([$realCalculator, 'add'])->byDefault();
        $calculator->allows('subtract')->andReturnUsing([$realCalculator, 'subtract'])->byDefault();
        $calculator->allows('multiply')->andReturnUsing([$realCalculator, 'multiply'])->byDefault();
        $calculator->allows('divide')->andReturnUsing([$realCalculator, 'divide'])->byDefault();
        $calculator->allows('fromBase')->andReturnUsing([$realCalculator, 'fromBase'])->byDefault();
        $calculator->allows('toBase')->andReturnUsing([$realCalculator, 'toBase'])->byDefault();
        $calculator->allows('toHexadecimal')->andReturnUsing([$realCalculator, 'toHexadecimal'])->byDefault();
        $calculator->allows('toInteger')->andReturnUsing([$realCalculator, 'toInteger'])->byDefault();

        return $calculator;
    }
}
