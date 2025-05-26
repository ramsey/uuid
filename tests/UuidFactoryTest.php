<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\DefaultNameGenerator;
use Ramsey\Uuid\Generator\NameGeneratorInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\Validator\ValidatorInterface;

use function hex2bin;
use function strtoupper;

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

        $this->assertSame($uuidString, $uuid->toString());
    }

    public function testGettersReturnValueFromFeatureSet(): void
    {
        $codec = Mockery::mock(CodecInterface::class);
        $nodeProvider = Mockery::mock(NodeProviderInterface::class);
        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $timeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $unixTimeGenerator = Mockery::mock(TimeGeneratorInterface::class);
        $nameGenerator = Mockery::mock(NameGeneratorInterface::class);
        $dceSecurityGenerator = Mockery::mock(DceSecurityGeneratorInterface::class);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $builder = Mockery::mock(UuidBuilderInterface::class);
        $validator = Mockery::mock(ValidatorInterface::class);

        $featureSet = Mockery::mock(FeatureSet::class, [
            'getCodec' => $codec,
            'getNodeProvider' => $nodeProvider,
            'getRandomGenerator' => $randomGenerator,
            'getTimeConverter' => $timeConverter,
            'getTimeGenerator' => $timeGenerator,
            'getNameGenerator' => $nameGenerator,
            'getDceSecurityGenerator' => $dceSecurityGenerator,
            'getNumberConverter' => $numberConverter,
            'getBuilder' => $builder,
            'getValidator' => $validator,
            'getUnixTimeGenerator' => $unixTimeGenerator,
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
        $this->assertSame($codec, $uuidFactory->getCodec());

        /** @var MockObject & TimeGeneratorInterface $timeGenerator */
        $timeGenerator = $this->getMockBuilder(TimeGeneratorInterface::class)->getMock();
        $uuidFactory->setTimeGenerator($timeGenerator);
        $this->assertSame($timeGenerator, $uuidFactory->getTimeGenerator());

        /** @var MockObject & NumberConverterInterface $numberConverter */
        $numberConverter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $uuidFactory->setNumberConverter($numberConverter);
        $this->assertSame($numberConverter, $uuidFactory->getNumberConverter());

        /** @var MockObject & UuidBuilderInterface $uuidBuilder */
        $uuidBuilder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $uuidFactory->setUuidBuilder($uuidBuilder);
        $this->assertSame($uuidBuilder, $uuidFactory->getUuidBuilder());
    }

    /**
     * @dataProvider provideDateTime
     */
    public function testFromDateTime(
        DateTimeInterface $dateTime,
        ?Hexadecimal $node,
        ?int $clockSeq,
        string $expectedUuidFormat,
        string $expectedTime
    ): void {
        $factory = new UuidFactory();

        $uuid = $factory->fromDateTime($dateTime, $node, $clockSeq);

        $this->assertStringMatchesFormat($expectedUuidFormat, $uuid->toString());
        $this->assertSame($expectedTime, $uuid->getDateTime()->format('U.u'));
    }

    /**
     * @return array<array{0: DateTimeInterface, 1: Hexadecimal | null, 2: int | null, 3: string, 4: string}>
     */
    public function provideDateTime(): array
    {
        return [
            [
                new DateTimeImmutable('2012-07-04 02:14:34.491000'),
                null,
                null,
                'ff6f8cb0-c57d-11e1-%s',
                '1341368074.491000',
            ],
            [
                new DateTimeImmutable('1582-10-16 16:34:04'),
                new Hexadecimal('0800200c9a66'),
                15137,
                '0901e600-0154-1000-%cb21-0800200c9a66',
                '-12219146756.000000',
            ],
            [
                new DateTime('5236-03-31 21:20:59.999999'),
                new Hexadecimal('00007ffffffe'),
                1641,
                'ff9785f6-ffff-1fff-%c669-00007ffffffe',
                '103072857659.999999',
            ],
            [
                new DateTime('1582-10-15 00:00:00'),
                new Hexadecimal('00007ffffffe'),
                1641,
                '00000000-0000-1000-%c669-00007ffffffe',
                '-12219292800.000000',
            ],
            [
                new DateTimeImmutable('@103072857660.684697'),
                new Hexadecimal('0'),
                0,
                'fffffffa-ffff-1fff-%c000-000000000000',
                '103072857660.684697',
            ],
            [
                new DateTimeImmutable('5236-03-31 21:21:00.684697'),
                null,
                null,
                'fffffffa-ffff-1fff-%s',
                '103072857660.684697',
            ],
        ];
    }

    public function testFactoryReturnsDefaultNameGenerator(): void
    {
        $factory = new UuidFactory();

        $this->assertInstanceOf(DefaultNameGenerator::class, $factory->getNameGenerator());
    }

    public function testFactoryReturnsSetNameGenerator(): void
    {
        $factory = new UuidFactory();

        $this->assertInstanceOf(DefaultNameGenerator::class, $factory->getNameGenerator());

        $nameGenerator = Mockery::mock(NameGeneratorInterface::class);
        $factory->setNameGenerator($nameGenerator);

        $this->assertSame($nameGenerator, $factory->getNameGenerator());
    }
}
