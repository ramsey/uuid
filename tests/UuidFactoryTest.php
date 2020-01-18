<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\Validator\ValidatorInterface;

class UuidFactoryTest extends TestCase
{
    public function testParsesUuidCorrectly(): void
    {
        $factory = new UuidFactory();

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
        $this->assertSame(hex2bin('ff6f8cb0c57d11e19b210800200c9a66'), $uuid->getBytes());
    }

    public function testParsesGuidCorrectly(): void
    {
        $factory = new UuidFactory(new FeatureSet(true));

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
        $this->assertSame(hex2bin('b08c6fff7dc5e1119b210800200c9a66'), $uuid->getBytes());
    }

    public function testFromStringParsesUuidInLowercase(): void
    {
        $uuidString = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuidUpper = strtoupper($uuidString);
        $factory = new UuidFactory(new FeatureSet(true));

        $uuid = $factory->fromString($uuidUpper);

        $this->assertEquals($uuidString, $uuid->toString());
    }

    public function testGettersReturnValueFromFeatureSet(): void
    {
        $codec = Mockery::mock(CodecInterface::class);
        $nodeProvider = Mockery::mock(NodeProviderInterface::class);
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $dceSecurityGenerator = Mockery::mock(DceSecurityGeneratorInterface::class);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $builder = Mockery::mock(UuidBuilderInterface::class);
        $validator = Mockery::mock(ValidatorInterface::class);

        $featureSet = Mockery::mock(FeatureSet::class, [
            'getCodec' => $codec,
            'getNodeProvider' => $nodeProvider,
            'getRandomGenerator' => $randomGenerator,
            'getTimeGenerator' => $timeGenerator,
            'getDceSecurityGenerator' => $dceSecurityGenerator,
            'getNumberConverter' => $numberConverter,
            'getBuilder' => $builder,
            'getValidator' => $validator,
        ]);

        $uuidFactory = new UuidFactory($featureSet);
        $this->assertSame(
            $codec,
            $uuidFactory->getCodec(),
            'getCodec did not return CodecInterface from FeatureSet'
        );

        $this->assertSame(
            $nodeProvider,
            $uuidFactory->getNodeProvider(),
            'getNodeProvider did not return NodeProviderInterface from FeatureSet'
        );

        $this->assertSame(
            $randomGenerator,
            $uuidFactory->getRandomGenerator(),
            'getRandomGenerator did not return RandomGeneratorInterface from FeatureSet'
        );

        $this->assertSame(
            $timeGenerator,
            $uuidFactory->getTimeGenerator(),
            'getTimeGenerator did not return TimeGeneratorInterface from FeatureSet'
        );
    }

    public function testSettersSetValueForGetters(): void
    {
        $uuidFactory = new UuidFactory();

        /** @var MockObject & CodecInterface $codec */
        $codec = $this->getMockBuilder(CodecInterface::class)->getMock();
        $uuidFactory->setCodec($codec);
        $this->assertEquals($codec, $uuidFactory->getCodec());

        /** @var MockObject & TimeGeneratorInterface $timeGenerator */
        $timeGenerator = $this->getMockBuilder(TimeGeneratorInterface::class)->getMock();
        $uuidFactory->setTimeGenerator($timeGenerator);
        $this->assertEquals($timeGenerator, $uuidFactory->getTimeGenerator());

        /** @var MockObject & NumberConverterInterface $numberConverter */
        $numberConverter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $uuidFactory->setNumberConverter($numberConverter);
        $this->assertEquals($numberConverter, $uuidFactory->getNumberConverter());

        /** @var MockObject & UuidBuilderInterface $uuidBuilder */
        $uuidBuilder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $this->assertEquals($uuidBuilder, $uuidFactory->getUuidBuilder());
    }
}
