<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use DateTimeInterface;
use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Generator\DceSecurityGenerator;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Provider\Dce\SystemDceSecurityProvider;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

use const PHP_VERSION_ID;

class UuidV2Test extends TestCase
{
    /**
     * @dataProvider provideTestVersions
     */
    public function testConstructorThrowsExceptionWhenFieldsAreNotValidForType(int $version): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'getVersion' => $version,
        ]);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Fields used to create a UuidV2 must represent a '
            . 'version 2 (DCE Security) UUID'
        );

        new UuidV2($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * @return array<array{version: int}>
     */
    public function provideTestVersions(): array
    {
        return [
            ['version' => 0],
            ['version' => 1],
            ['version' => 3],
            ['version' => 4],
            ['version' => 5],
            ['version' => 6],
            ['version' => 7],
            ['version' => 8],
            ['version' => 9],
        ];
    }

    /**
     * @dataProvider provideLocalDomainAndIdentifierForTests
     */
    public function testGetLocalDomainAndIdentifier(
        int $domain,
        IntegerObject $identifier,
        Time $time,
        int $expectedDomain,
        string $expectedDomainName,
        string $expectedIdentifier,
        string $expectedTimestamp,
        string $expectedTime
    ): void {
        $calculator = new BrickMathCalculator();
        $genericConverter = new GenericTimeConverter($calculator);
        $numberConverter = new GenericNumberConverter($calculator);
        $nodeProvider = new StaticNodeProvider(new Hexadecimal('1234567890ab'));
        $timeProvider = new FixedTimeProvider($time);
        $timeGenerator = new DefaultTimeGenerator($nodeProvider, $genericConverter, $timeProvider);
        $dceProvider = new SystemDceSecurityProvider();
        $dceGenerator = new DceSecurityGenerator($numberConverter, $timeGenerator, $dceProvider);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($timeGenerator);
        $factory->setDceSecurityGenerator($dceGenerator);

        /** @var UuidV2 $uuid */
        $uuid = $factory->uuid2($domain, $identifier);

        /** @var FieldsInterface $fields */
        $fields = $uuid->getFields();

        $this->assertSame($expectedDomain, $uuid->getLocalDomain());
        $this->assertSame($expectedDomainName, $uuid->getLocalDomainName());
        $this->assertInstanceOf(IntegerObject::class, $uuid->getLocalIdentifier());
        $this->assertSame($expectedIdentifier, $uuid->getLocalIdentifier()->toString());
        $this->assertSame($expectedTimestamp, $fields->getTimestamp()->toString());
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertSame($expectedTime, $uuid->getDateTime()->format('U.u'));
        $this->assertSame('1334567890ab', $fields->getNode()->toString());
    }

    /**
     * @return array<array{
     *     domain: int,
     *     identifier: IntegerObject,
     *     time: Time,
     *     expectedDomain: int,
     *     expectedDomainName: non-empty-string,
     *     expectedIdentifier: non-empty-string,
     *     expectedTimestamp: non-empty-string,
     *     expectedTime: non-empty-string,
     * }>
     */
    public function provideLocalDomainAndIdentifierForTests(): array
    {
        // https://github.com/php/php-src/issues/7758
        $isGH7758Fixed = PHP_VERSION_ID >= 80107;

        return [
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('12345678'),
                'time' => new Time(0, 0),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '12345678',
                'expectedTimestamp' => '1b21dd200000000',
                'expectedTime' => $isGH7758Fixed ? '-33.276237' : '-32.723763',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_GROUP,
                'identifier' => new IntegerObject('87654321'),
                'time' => new Time(0, 0),
                'expectedDomain' => 1,
                'expectedDomainName' => 'group',
                'expectedIdentifier' => '87654321',
                'expectedTimestamp' => '1b21dd200000000',
                'expectedTime' => $isGH7758Fixed ? '-33.276237' : '-32.723763',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_ORG,
                'identifier' => new IntegerObject('1'),
                'time' => new Time(0, 0),
                'expectedDomain' => 2,
                'expectedDomainName' => 'org',
                'expectedIdentifier' => '1',
                'expectedTimestamp' => '1b21dd200000000',
                'expectedTime' => $isGH7758Fixed ? '-33.276237' : '-32.723763',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('0'),
                'time' => new Time(1583208664, 444109),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '0',
                'expectedTimestamp' => '1ea5d0500000000',
                'expectedTime' => '1583208664.444109',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('2147483647'),
                'time' => new Time(1583208879, 500000),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '2147483647',
                // This time is the same as in the previous test because of the
                // loss of precision by setting the lowest 32 bits to zeros.
                'expectedTimestamp' => '1ea5d0500000000',
                'expectedTime' => '1583208664.444109',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('4294967295'),
                'time' => new Time(1583208879, 500000),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '4294967295',
                // This time is the same as in the previous test because of the
                // loss of precision by setting the lowest 32 bits to zeros.
                'expectedTimestamp' => '1ea5d0500000000',
                'expectedTime' => '1583208664.444109',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('4294967295'),
                'time' => new Time(1583209093, 940838),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '4294967295',
                // This time is the same as in the previous test because of the
                // loss of precision by setting the lowest 32 bits to zeros.
                'expectedTimestamp' => '1ea5d0500000000',
                'expectedTime' => '1583208664.444109',
            ],
            [
                'domain' => Uuid::DCE_DOMAIN_PERSON,
                'identifier' => new IntegerObject('4294967295'),
                'time' => new Time(1583209093, 940839),
                'expectedDomain' => 0,
                'expectedDomainName' => 'person',
                'expectedIdentifier' => '4294967295',
                'expectedTimestamp' => '1ea5d0600000000',
                'expectedTime' => '1583209093.940838',
            ],
        ];
    }
}
