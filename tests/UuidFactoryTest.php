<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\UuidFactory;

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

        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
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
        $codec = $this->getMockBuilder(CodecInterface::class)->getMock();
        $nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $timeGenerator = $this->getMockBuilder(TimeGeneratorInterface::class)->getMock();

        $featureSet = $this->getMockBuilder(FeatureSet::class)->getMock();
        $featureSet->method('getCodec')->willReturn($codec);
        $featureSet->method('getNodeProvider')->willReturn($nodeProvider);
        $featureSet->method('getRandomGenerator')->willReturn($randomGenerator);
        $featureSet->method('getTimeGenerator')->willReturn($timeGenerator);

        $uuidFactory = new UuidFactory($featureSet);
        $this->assertEquals(
            $codec,
            $uuidFactory->getCodec(),
            'getCodec did not return CodecInterface from FeatureSet'
        );

        $this->assertEquals(
            $nodeProvider,
            $uuidFactory->getNodeProvider(),
            'getNodeProvider did not return NodeProviderInterface from FeatureSet'
        );

        $this->assertEquals(
            $randomGenerator,
            $uuidFactory->getRandomGenerator(),
            'getRandomGenerator did not return RandomGeneratorInterface from FeatureSet'
        );

        $this->assertEquals(
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
