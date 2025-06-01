<?php

namespace Ramsey\Uuid\Test;

use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\Variant;
use stdClass;

/**
 * These tests exist to ensure a seamless upgrade path from 3.x to 4.x. If any
 * of these tests fail in 4.x, then it's because we've changed functionality
 * in such a way that compatibility with 3.x is broken.
 *
 * Naturally, there are some BC-breaks between 3.x and 4.x, but these tests
 * ensure that the base-level functionality that satisfies 80% of use-cases
 * does not change. The remaining 20% of use-cases should refer to the README
 * for details on the easiest path to transition from 3.x to 4.x.
 *
 * @codingStandardsIgnoreFile
 */
class ExpectedBehaviorTest extends TestCase
{
    /**
     * @param mixed[] $args
     */
    #[DataProvider('provideStaticCreationMethods')]
    public function testStaticCreationMethodsAndStandardBehavior(string $method, array $args): void
    {
        /** @var UuidInterface $uuid */
        $uuid = Uuid::$method(...$args);

        $this->assertIsInt($uuid->compareTo(Uuid::uuid1()));
        $this->assertNotSame(0, $uuid->compareTo(Uuid::uuid4()));
        $this->assertSame(0, $uuid->compareTo(clone $uuid));
        $this->assertFalse($uuid->equals(new stdClass()));
        $this->assertTrue($uuid->equals(clone $uuid));

        $this->assertSame(
            (string) $uuid->getHex(),
            $uuid->getFields()->getTimeLow()->toString()
                . $uuid->getFields()->getTimeMid()->toString()
                . $uuid->getFields()->getTimeHiAndVersion()->toString()
                . $uuid->getFields()->getClockSeqHiAndReserved()->toString()
                . $uuid->getFields()->getClockSeqLow()->toString()
                . $uuid->getFields()->getNode()->toString()
        );

        $this->assertStringStartsWith('urn:uuid:', $uuid->getUrn());
        $this->assertSame('urn:uuid:' . (string) $uuid->getHex(), str_replace('-', '', $uuid->getUrn()));
        $this->assertSame((string) $uuid->getHex(), str_replace('-', '', $uuid->toString()));
        $this->assertSame((string) $uuid->getHex(), str_replace('-', '', (string) $uuid));

        $this->assertSame(
            $uuid->toString(),
            $uuid->getFields()->getTimeLow()->toString() . '-'
                . $uuid->getFields()->getTimeMid()->toString() . '-'
                . $uuid->getFields()->getTimeHiAndVersion()->toString() . '-'
                . $uuid->getFields()->getClockSeqHiAndReserved()->toString()
                . $uuid->getFields()->getClockSeqLow()->toString() . '-'
                . $uuid->getFields()->getNode()->toString()
        );

        $this->assertSame(
            (string) $uuid,
            $uuid->getFields()->getTimeLow()->toString() . '-'
                . $uuid->getFields()->getTimeMid()->toString() . '-'
                . $uuid->getFields()->getTimeHiAndVersion()->toString() . '-'
                . $uuid->getFields()->getClockSeqHiAndReserved()->toString()
                . $uuid->getFields()->getClockSeqLow()->toString() . '-'
                . $uuid->getFields()->getNode()->toString()
        );

        $this->assertSame(Variant::Rfc4122, $uuid->getFields()->getVariant());
        $this->assertSame(Version::tryFrom((int) substr($method, -1)), $uuid->getFields()->getVersion());
        $this->assertSame(1, preg_match('/^\d+$/', (string) $uuid->getInteger()));
    }

    /**
     * @return array<array{0: string, 1: mixed[]}>
     */
    public static function provideStaticCreationMethods(): array
    {
        return [
            ['uuid1', []],
            ['uuid1', ['00000fffffff']],
            ['uuid1', [null, 1234]],
            ['uuid1', ['00000fffffff', 1234]],
            ['uuid1', ['00000fffffff', null]],
            ['uuid1', [268435455]],
            ['uuid1', [268435455, 1234]],
            ['uuid1', [268435455, null]],
            ['uuid3', [Uuid::NAMESPACE_URL, 'https://example.com/foo']],
            ['uuid4', []],
            ['uuid5', [Uuid::NAMESPACE_URL, 'https://example.com/foo']],
        ];
    }

    public function testUuidVersion1MethodBehavior(): void
    {
        /** @var UuidV1 $uuid */
        $uuid = Uuid::uuid1('00000fffffff', 0x3fff);

        $this->assertInstanceOf('DateTimeInterface', $uuid->getDateTime());
        $this->assertSame('00000fffffff', $uuid->getFields()->getNode()->toString());
        $this->assertSame('3fff', $uuid->getFields()->getClockSeq()->toString());
    }

    /**
     * @param non-empty-string $uuid
     */
    #[DataProvider('provideIsValid')]
    public function testIsValid(string $uuid, bool $expected): void
    {
        $this->assertSame($expected, Uuid::isValid($uuid), "{$uuid} is not a valid UUID");
        $this->assertSame($expected, Uuid::isValid(strtoupper($uuid)), strtoupper($uuid) . ' is not a valid UUID');
    }

    /**
     * @return array<array{0: non-empty-string, 1: bool}>
     */
    public static function provideIsValid(): array
    {
        return [
            // RFC 4122 UUIDs
            ['00000000-0000-0000-0000-000000000000', true],
            ['ff6f8cb0-c57d-11e1-8b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-11e1-ab21-0800200c9a66', true],
            ['ff6f8cb0-c57d-11e1-bb21-0800200c9a66', true],
            ['ff6f8cb0-c57d-21e1-8b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-21e1-ab21-0800200c9a66', true],
            ['ff6f8cb0-c57d-21e1-bb21-0800200c9a66', true],
            ['ff6f8cb0-c57d-31e1-8b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-31e1-ab21-0800200c9a66', true],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', true],
            ['ff6f8cb0-c57d-41e1-8b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', true],
            ['ff6f8cb0-c57d-41e1-bb21-0800200c9a66', true],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-51e1-ab21-0800200c9a66', true],
            ['ff6f8cb0-c57d-51e1-bb21-0800200c9a66', true],

            // Non RFC 4122 UUIDs
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', true],
            ['00000000-0000-0000-0000-000000000000', true],
            ['ff6f8cb0-c57d-01e1-0b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-1b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-2b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-3b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-4b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-5b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-6b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-7b21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-db21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-eb21-0800200c9a66', true],
            ['ff6f8cb0-c57d-01e1-fb21-0800200c9a66', true],

            // Other valid patterns
            ['{ff6f8cb0-c57d-01e1-fb21-0800200c9a66}', true],
            ['urn:uuid:ff6f8cb0-c57d-01e1-fb21-0800200c9a66', true],

            // Invalid UUIDs
            ['ffffffffffffffffffffffffffffffff', false],
            ['00000000000000000000000000000000', false],
            ['0', false],
            ['foobar', false],
            ['ff6f8cb0c57d51e1bb210800200c9a66', false],
            ['gf6f8cb0-c57d-51e1-bb21-0800200c9a66', false],
        ];
    }

    /**
     * @param non-empty-string $string
     * @param numeric-string $integer
     */
    #[DataProvider('provideFromStringInteger')]
    public function testSerialization(string $string, ?int $version, int $variant, string $integer): void
    {
        $uuid = Uuid::fromString($string);

        $serialized = serialize($uuid);

        /** @var UuidInterface $unserialized */
        $unserialized = unserialize($serialized);

        $this->assertSame(0, $uuid->compareTo($unserialized));
        $this->assertTrue($uuid->equals($unserialized));
        $this->assertSame("\"{$string}\"", json_encode($uuid));
    }

    /**
     * @param non-empty-string $string
     * @param numeric-string $integer
     */
    #[DataProvider('provideFromStringInteger')]
    public function testFromBytes(string $string, ?int $version, int $variant, string $integer): void
    {
        /** @var non-empty-string $bytes */
        $bytes = hex2bin(str_replace('-', '', $string));

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromBytes($bytes);

        $this->assertSame($string, $uuid->toString());
        $this->assertSame(Version::tryFrom($version ?? 0), $uuid->getFields()->getVersion());
        $this->assertSame(Variant::from($variant), $uuid->getFields()->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getFields()->getTimeLow()->toString());
        $this->assertSame($components[1], $uuid->getFields()->getTimeMid()->toString());
        $this->assertSame($components[2], $uuid->getFields()->getTimeHiAndVersion()->toString());
        $this->assertSame(
            $components[3],
            $uuid->getFields()->getClockSeqHiAndReserved()->toString()
            . $uuid->getFields()->getClockSeqLow()->toString(),
        );
        $this->assertSame($components[4], $uuid->getFields()->getNode()->toString());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @param non-empty-string $string
     * @param numeric-string $integer
     */
    #[DataProvider('provideFromStringInteger')]
    public function testFromInteger(string $string, ?int $version, int $variant, string $integer): void
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromInteger($integer);

        $this->assertSame($string, $uuid->toString());
        $this->assertSame(Version::tryFrom($version ?? 0), $uuid->getFields()->getVersion());
        $this->assertSame(Variant::from($variant), $uuid->getFields()->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getFields()->getTimeLow()->toString());
        $this->assertSame($components[1], $uuid->getFields()->getTimeMid()->toString());
        $this->assertSame($components[2], $uuid->getFields()->getTimeHiAndVersion()->toString());
        $this->assertSame(
            $components[3],
            $uuid->getFields()->getClockSeqHiAndReserved()->toString()
            . $uuid->getFields()->getClockSeqLow()->toString(),
        );
        $this->assertSame($components[4], $uuid->getFields()->getNode()->toString());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @param non-empty-string $string
     * @param numeric-string $integer
     */
    #[DataProvider('provideFromStringInteger')]
    public function testFromString(string $string, ?int $version, int $variant, string $integer): void
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        /** @var UuidInterface $uuid */
        $uuid = Uuid::fromString($string);

        $this->assertSame($string, $uuid->toString());
        $this->assertSame(Version::tryFrom($version ?? 0), $uuid->getFields()->getVersion());
        $this->assertSame(Variant::from($variant), $uuid->getFields()->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getFields()->getTimeLow()->toString());
        $this->assertSame($components[1], $uuid->getFields()->getTimeMid()->toString());
        $this->assertSame($components[2], $uuid->getFields()->getTimeHiAndVersion()->toString());
        $this->assertSame(
            $components[3],
            $uuid->getFields()->getClockSeqHiAndReserved()->toString()
            . $uuid->getFields()->getClockSeqLow()->toString(),
        );
        $this->assertSame($components[4], $uuid->getFields()->getNode()->toString());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @return array<array{0: non-empty-string, 1: int | null, 2: int, 3: numeric-string}>
     */
    public static function provideFromStringInteger(): array
    {
        return [
            ['00000000-0000-0000-0000-000000000000', null, 0, '0'],
            ['ff6f8cb0-c57d-11e1-8b21-0800200c9a66', 1, 2, '339532337419071774304650190139318639206'],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', 1, 2, '339532337419071774305803111643925486182'],
            ['ff6f8cb0-c57d-11e1-ab21-0800200c9a66', 1, 2, '339532337419071774306956033148532333158'],
            ['ff6f8cb0-c57d-11e1-bb21-0800200c9a66', 1, 2, '339532337419071774308108954653139180134'],
            ['ff6f8cb0-c57d-21e1-8b21-0800200c9a66', 2, 2, '339532337419071849862513916053642058342'],
            ['ff6f8cb0-c57d-21e1-9b21-0800200c9a66', 2, 2, '339532337419071849863666837558248905318'],
            ['ff6f8cb0-c57d-21e1-ab21-0800200c9a66', 2, 2, '339532337419071849864819759062855752294'],
            ['ff6f8cb0-c57d-21e1-bb21-0800200c9a66', 2, 2, '339532337419071849865972680567462599270'],
            ['ff6f8cb0-c57d-31e1-8b21-0800200c9a66', 3, 2, '339532337419071925420377641967965477478'],
            ['ff6f8cb0-c57d-31e1-9b21-0800200c9a66', 3, 2, '339532337419071925421530563472572324454'],
            ['ff6f8cb0-c57d-31e1-ab21-0800200c9a66', 3, 2, '339532337419071925422683484977179171430'],
            ['ff6f8cb0-c57d-31e1-bb21-0800200c9a66', 3, 2, '339532337419071925423836406481786018406'],
            ['ff6f8cb0-c57d-41e1-8b21-0800200c9a66', 4, 2, '339532337419072000978241367882288896614'],
            ['ff6f8cb0-c57d-41e1-9b21-0800200c9a66', 4, 2, '339532337419072000979394289386895743590'],
            ['ff6f8cb0-c57d-41e1-ab21-0800200c9a66', 4, 2, '339532337419072000980547210891502590566'],
            ['ff6f8cb0-c57d-41e1-bb21-0800200c9a66', 4, 2, '339532337419072000981700132396109437542'],
            ['ff6f8cb0-c57d-51e1-8b21-0800200c9a66', 5, 2, '339532337419072076536105093796612315750'],
            ['ff6f8cb0-c57d-51e1-9b21-0800200c9a66', 5, 2, '339532337419072076537258015301219162726'],
            ['ff6f8cb0-c57d-51e1-ab21-0800200c9a66', 5, 2, '339532337419072076538410936805826009702'],
            ['ff6f8cb0-c57d-51e1-bb21-0800200c9a66', 5, 2, '339532337419072076539563858310432856678'],
            ['ff6f8cb0-c57d-01e1-0b21-0800200c9a66', null, 0, '339532337419071698737563092188140444262'],
            ['ff6f8cb0-c57d-01e1-1b21-0800200c9a66', null, 0, '339532337419071698738716013692747291238'],
            ['ff6f8cb0-c57d-01e1-2b21-0800200c9a66', null, 0, '339532337419071698739868935197354138214'],
            ['ff6f8cb0-c57d-01e1-3b21-0800200c9a66', null, 0, '339532337419071698741021856701960985190'],
            ['ff6f8cb0-c57d-01e1-4b21-0800200c9a66', null, 0, '339532337419071698742174778206567832166'],
            ['ff6f8cb0-c57d-01e1-5b21-0800200c9a66', null, 0, '339532337419071698743327699711174679142'],
            ['ff6f8cb0-c57d-01e1-6b21-0800200c9a66', null, 0, '339532337419071698744480621215781526118'],
            ['ff6f8cb0-c57d-01e1-7b21-0800200c9a66', null, 0, '339532337419071698745633542720388373094'],
            ['ff6f8cb0-c57d-01e1-cb21-0800200c9a66', null, 6, '339532337419071698751398150243422607974'],
            ['ff6f8cb0-c57d-01e1-db21-0800200c9a66', null, 6, '339532337419071698752551071748029454950'],
            ['ff6f8cb0-c57d-01e1-eb21-0800200c9a66', null, 7, '339532337419071698753703993252636301926'],
            ['ff6f8cb0-c57d-01e1-fb21-0800200c9a66', null, 7, '339532337419071698754856914757243148902'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', null, 7, '340282366920938463463374607431768211455'],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGetSetFactory(): void
    {
        $this->assertInstanceOf('Ramsey\Uuid\UuidFactory', Uuid::getFactory());

        $factory = Mockery::mock('Ramsey\Uuid\UuidFactory');
        Uuid::setFactory($factory);

        $this->assertSame($factory, Uuid::getFactory());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testFactoryProvidesFunctionality(): void
    {
        $uuid = Mockery::mock('Ramsey\Uuid\UuidInterface');

        $factory = Mockery::mock('Ramsey\Uuid\UuidFactoryInterface', [
            'uuid1' => $uuid,
            'uuid3' => $uuid,
            'uuid4' => $uuid,
            'uuid5' => $uuid,
            'fromBytes' => $uuid,
            'fromString' => $uuid,
            'fromInteger' => $uuid,
        ]);

        Uuid::setFactory($factory);

        /** @var non-empty-string $bytes */
        $bytes = hex2bin('ffffffffffffffffffffffffffffffff');

        $this->assertSame($uuid, Uuid::uuid1('ffffffffffff', 0x3fff));
        $this->assertSame($uuid, Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::uuid4());
        $this->assertSame($uuid, Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::fromBytes($bytes));
        $this->assertSame($uuid, Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
        $this->assertSame($uuid, Uuid::fromInteger('340282366920938463463374607431768211455'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testUsingCustomCodec(): void
    {
        $mockUuid = Mockery::mock('Ramsey\Uuid\UuidInterface');

        $codec = Mockery::mock('Ramsey\Uuid\Codec\CodecInterface', [
            'encode' => 'abcd1234',
            'encodeBinary' => hex2bin('abcd1234'),
            'decode' => $mockUuid,
            'decodeBytes' => $mockUuid,
        ]);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        /** @var non-empty-string $bytes */
        $bytes = hex2bin('f00ba2');

        $this->assertSame('abcd1234', $uuid->toString());
        $this->assertSame(hex2bin('abcd1234'), $uuid->getBytes());
        $this->assertSame($mockUuid, Uuid::fromString('f00ba2'));
        $this->assertSame($mockUuid, Uuid::fromBytes($bytes));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testUsingCustomRandomGenerator(): void
    {
        $generator = Mockery::mock('Ramsey\Uuid\Generator\RandomGeneratorInterface', [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setRandomGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        $this->assertSame('01234567-abcd-4432-9cba-0123456789ab', $uuid->toString());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testUsingCustomTimeGenerator(): void
    {
        $generator = Mockery::mock('Ramsey\Uuid\Generator\TimeGeneratorInterface', [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1();

        $this->assertSame('01234567-abcd-1432-9cba-0123456789ab', $uuid->toString());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testUsingDefaultTimeGeneratorWithCustomProviders(): void
    {
        $nodeProvider = Mockery::mock('Ramsey\Uuid\Provider\NodeProviderInterface', [
            'getNode' => new Hexadecimal('0123456789ab'),
        ]);

        $timeConverter = Mockery::mock('Ramsey\Uuid\Converter\TimeConverterInterface');
        $timeConverter
            ->shouldReceive('calculateTime')
            ->andReturnUsing(function (int $seconds, int $microseconds) {
                return new Hexadecimal('abcd' . dechex($microseconds) . dechex($seconds));
            });

        $timeProvider = Mockery::mock('Ramsey\Uuid\Provider\TimeProviderInterface', [
            'currentTime' => [
                'sec' => 1578522046,
                'usec' => 10000,
            ],
            'getTime' => new Time(1578522046, 10000),
        ]);

        $generator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1(null, 4095);

        $this->assertSame('5e1655be-2710-1bcd-8fff-0123456789ab', $uuid->toString());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testHelperFunctions(): void
    {
        $uuid1 = Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion1Uuid',
        ]);
        $uuid3 = Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion3Uuid',
        ]);
        $uuid4 = Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion4Uuid',
        ]);
        $uuid5 = Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion5Uuid',
        ]);

        $factory = Mockery::mock('Ramsey\Uuid\UuidFactoryInterface', [
            'uuid1' => $uuid1,
            'uuid3' => $uuid3,
            'uuid4' => $uuid4,
            'uuid5' => $uuid5,
        ]);

        Uuid::setFactory($factory);

        $this->assertSame('aVersion1Uuid', \Ramsey\Uuid\v1('ffffffffffff', 0x3fff));
        $this->assertSame('aVersion3Uuid', \Ramsey\Uuid\v3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame('aVersion4Uuid', \Ramsey\Uuid\v4());
        $this->assertSame('aVersion5Uuid', \Ramsey\Uuid\v5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
    }

    #[DataProvider('provideUuidConstantTests')]
    public function testUuidConstants(string $constantName, int | string $expected): void
    {
        $this->assertSame($expected, constant("Ramsey\\Uuid\\Uuid::{$constantName}"));
    }

    /**
     * @return array<array{string, int | string}>
     */
    public static function provideUuidConstantTests(): array
    {
        return [
            ['NAMESPACE_DNS', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_URL', '6ba7b811-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_X500', '6ba7b814-9dad-11d1-80b4-00c04fd430c8'],
            ['NIL', '00000000-0000-0000-0000-000000000000'],
            ['MAX', 'ffffffff-ffff-ffff-ffff-ffffffffffff'],
        ];
    }
}
