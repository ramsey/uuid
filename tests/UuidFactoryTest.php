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
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Ramsey\Uuid\Generator\DefaultNameGenerator;
use Ramsey\Uuid\Generator\NameGeneratorInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;

use function bin2hex;
use function hex2bin;
use function str_replace;
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

    public function testFromStrictStringWithInvalidUuidString(): void
    {
        $this->expectException(InvalidUuidStringException::class);

        $factory = new UuidFactory(new FeatureSet(true));

        $factory->fromStrictString('00000000000000000000000000000000');
    }

    public function testFromStrictStringWithValidUuidString(): void
    {
        $factory = new UuidFactory(new FeatureSet(true));

        $this->assertSame(
            'ff6f8cb0-c57d-11e1-9b21-0800200c9a66',
            $factory->fromStrictString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66')
                ->toString()
        );

        $this->assertSame(
            '00000000-0000-0000-0000-000000000000',
            $factory->fromStrictString('00000000-0000-0000-0000-000000000000')
                ->toString()
        );
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

    /**
     * @param callable(UuidFactory): UuidInterface $generate
     *
     * @dataProvider provideGuidGenerators
     */
    public function testGuidFactoryGeneratesValidGuids(callable $generate, int $version, string $expected): void
    {
        $features = new FeatureSet(true);
        $features->setTimeProvider(new FixedTimeProvider(new Time(1341368074, 491000)));
        $features->setNodeProvider(new StaticNodeProvider(new Hexadecimal('0800200c9a66')));
        $factory = new UuidFactory($features);

        $randomGenerator = Mockery::mock(RandomGeneratorInterface::class);
        $randomGenerator->allows('generate')->andReturn((string) hex2bin('000102030405060708090a0b0c0d0e0f'));
        $factory->setRandomGenerator($randomGenerator);

        $guid = $generate($factory);

        $fields = $guid->getFields();

        $this->assertInstanceOf(Guid::class, $guid);
        $this->assertInstanceOf(Rfc4122FieldsInterface::class, $fields);
        $this->assertSame($version, $fields->getVersion());
        $this->assertSame(Uuid::RFC_4122, $fields->getVariant());
        $this->assertStringMatchesFormat($expected, $guid->toString());
        $this->assertTrue($guid->equals($factory->fromBytes($guid->getBytes())));

        $hex = str_replace('-', '', $guid->toString());
        $this->assertSame(
            substr($hex, 6, 2) . substr($hex, 4, 2) . substr($hex, 2, 2) . substr($hex, 0, 2)
                . substr($hex, 10, 2) . substr($hex, 8, 2) . substr($hex, 14, 2) . substr($hex, 12, 2)
                . substr($hex, 16),
            bin2hex($guid->getBytes()),
        );
    }

    /**
     * @return array<string, array{0: callable(UuidFactory): UuidInterface, 1: int, 2: string}>
     */
    public function provideGuidGenerators(): array
    {
        return [
            'uuid1' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid1(null, 0x1b21),
                1,
                'ff6f8cb0-c57d-11e1-9b21-0900200c9a66',
            ],
            'uuid3' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid3(Uuid::fromString(Uuid::NAMESPACE_URL), 'foo'),
                3,
                Uuid::uuid3(Uuid::NAMESPACE_URL, 'foo')->toString(),
            ],
            'uuid4' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid4(),
                4,
                '00010203-0405-4607-8809-0a0b0c0d0e0f',
            ],
            'uuid5' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid5(Uuid::fromString(Uuid::NAMESPACE_URL), 'foo'),
                5,
                Uuid::uuid5(Uuid::NAMESPACE_URL, 'foo')->toString(),
            ],
            'uuid6' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid6(null, 0x1b21),
                6,
                '1e1c57df-f6f8-6cb0-9b21-0900200c9a66',
            ],
            'uuid7' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid7(new DateTimeImmutable('@1341368074.491')),
                7,
                '01384fc4-80fb-7%x-%x-%x',
            ],
            'uuid8' => [
                fn (UuidFactory $f): UuidInterface => $f->uuid8((string) hex2bin('000102030405060708090a0b0c0d0e0f')),
                8,
                '00010203-0405-8607-8809-0a0b0c0d0e0f',
            ],
        ];
    }
}
