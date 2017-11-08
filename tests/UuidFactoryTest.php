<?php

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\UuidFactory;

class UuidFactoryTest extends TestCase
{
    public function testParsesUuidCorrectly()
    {
        $factory = new UuidFactory();

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testParsesGuidCorrectly()
    {
        $factory = new UuidFactory(new FeatureSet(true));

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testFromStringParsesUuidInLowercase()
    {
        $uuidString = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        $uuidUpper = strtoupper($uuidString);
        $factory = new UuidFactory(new FeatureSet(true));

        $uuid = $factory->fromString($uuidUpper);

        $this->assertEquals($uuidString, $uuid->toString());
    }

    public function testGettersReturnValueFromFeatureSet()
    {
        $codec = $this->getMockBuilder('Ramsey\Uuid\Codec\CodecInterface')->getMock();
        $nodeProvider = $this->getMockBuilder('Ramsey\Uuid\Provider\NodeProviderInterface')->getMock();
        $randomGenerator = $this->getMockBuilder('Ramsey\Uuid\Generator\RandomGeneratorInterface')->getMock();
        $timeGenerator = $this->getMockBuilder('Ramsey\Uuid\Generator\TimeGeneratorInterface')->getMock();

        $featureSet = $this->getMockBuilder('Ramsey\Uuid\FeatureSet')->getMock();
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

    public function testSettersSetValueForGetters()
    {
        $uuidFactory = new UuidFactory();

        $codec = $this->getMockBuilder('Ramsey\Uuid\Codec\CodecInterface')->getMock();
        $uuidFactory->setCodec($codec);
        $this->assertEquals($codec, $uuidFactory->getCodec());

        $timeGenerator = $this->getMockBuilder('Ramsey\Uuid\Generator\TimeGeneratorInterface')->getMock();
        $uuidFactory->setTimeGenerator($timeGenerator);
        $this->assertEquals($timeGenerator, $uuidFactory->getTimeGenerator());

        $numberConverter = $this->getMockBuilder('Ramsey\Uuid\Converter\NumberConverterInterface')->getMock();
        $uuidFactory->setNumberConverter($numberConverter);
        $this->assertEquals($numberConverter, $uuidFactory->getNumberConverter());

        $uuidBuilder = $this->getMockBuilder('Ramsey\Uuid\Builder\UuidBuilderInterface')->getMock();
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $this->assertEquals($uuidBuilder, $uuidFactory->getUuidBuilder());
    }
}
