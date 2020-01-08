<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use DateTimeImmutable;
use Mockery;
use Moontoast\Math\BigNumber;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;
use stdClass;

use function Ramsey\Uuid\v1;
use function Ramsey\Uuid\v3;
use function Ramsey\Uuid\v4;
use function Ramsey\Uuid\v5;

/**
 * These tests exist to ensure a seamless upgrade path from 3.x to 4.x. If any
 * of these tests fail in 4.x, then it's because we've changed functionality
 * in such a way that compatibility with 3.x is broken.
 *
 * Naturally, there are some BC-breaks between 3.x and 4.x, but these tests
 * ensure that the base-level functionality that satisfies 80% of use-cases
 * does not change. The remaining 20% of use-cases should refer to the README
 * for details on the easiest path to transition from 3.x to 4.x.
 */
class ExpectedBehaviorTest extends TestCase
{
    /**
     * @param mixed[] $args
     *
     * @dataProvider provideStaticCreationMethods
     */
    public function testStaticCreationMethodsAndStandardBehavior(string $method, array $args): void
    {
        $uuid = Uuid::$method(...$args);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertIsInt($uuid->compareTo(Uuid::uuid1()));
        $this->assertNotSame(0, $uuid->compareTo(Uuid::uuid4()));
        $this->assertSame(0, $uuid->compareTo(clone $uuid));
        $this->assertFalse($uuid->equals(new stdClass()));
        $this->assertTrue($uuid->equals(clone $uuid));
        $this->assertIsString($uuid->getBytes());
        $this->assertInstanceOf(NumberConverterInterface::class, $uuid->getNumberConverter());
        $this->assertIsString($uuid->getHex());
        $this->assertIsArray($uuid->getFieldsHex());
        $this->assertArrayHasKey('time_low', $uuid->getFieldsHex());
        $this->assertArrayHasKey('time_mid', $uuid->getFieldsHex());
        $this->assertArrayHasKey('time_hi_and_version', $uuid->getFieldsHex());
        $this->assertArrayHasKey('clock_seq_hi_and_reserved', $uuid->getFieldsHex());
        $this->assertArrayHasKey('clock_seq_low', $uuid->getFieldsHex());
        $this->assertArrayHasKey('node', $uuid->getFieldsHex());
        $this->assertIsString($uuid->getTimeLowHex());
        $this->assertIsString($uuid->getTimeMidHex());
        $this->assertIsString($uuid->getTimeHiAndVersionHex());
        $this->assertIsString($uuid->getClockSeqHiAndReservedHex());
        $this->assertIsString($uuid->getClockSeqLowHex());
        $this->assertIsString($uuid->getNodeHex());
        $this->assertSame($uuid->getFieldsHex()['time_low'], $uuid->getTimeLowHex());
        $this->assertSame($uuid->getFieldsHex()['time_mid'], $uuid->getTimeMidHex());
        $this->assertSame($uuid->getFieldsHex()['time_hi_and_version'], $uuid->getTimeHiAndVersionHex());
        $this->assertSame($uuid->getFieldsHex()['clock_seq_hi_and_reserved'], $uuid->getClockSeqHiAndReservedHex());
        $this->assertSame($uuid->getFieldsHex()['clock_seq_low'], $uuid->getClockSeqLowHex());
        $this->assertSame($uuid->getFieldsHex()['node'], $uuid->getNodeHex());
        $this->assertSame(substr($uuid->getHex(), 16), $uuid->getLeastSignificantBitsHex());
        $this->assertSame(substr($uuid->getHex(), 0, 16), $uuid->getMostSignificantBitsHex());

        $this->assertSame(
            $uuid->getHex(),
            $uuid->getTimeLowHex()
            . $uuid->getTimeMidHex()
            . $uuid->getTimeHiAndVersionHex()
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex()
            . $uuid->getNodeHex()
        );

        $this->assertSame(
            $uuid->getHex(),
            $uuid->getFieldsHex()['time_low']
            . $uuid->getFieldsHex()['time_mid']
            . $uuid->getFieldsHex()['time_hi_and_version']
            . $uuid->getFieldsHex()['clock_seq_hi_and_reserved']
            . $uuid->getFieldsHex()['clock_seq_low']
            . $uuid->getFieldsHex()['node']
        );

        $this->assertIsString($uuid->getUrn());
        $this->assertStringStartsWith('urn:uuid:', $uuid->getUrn());
        $this->assertSame('urn:uuid:' . $uuid->getHex(), str_replace('-', '', $uuid->getUrn()));
        $this->assertSame($uuid->getHex(), str_replace('-', '', $uuid->toString()));
        $this->assertSame($uuid->getHex(), str_replace('-', '', (string) $uuid));

        $this->assertSame(
            $uuid->toString(),
            $uuid->getTimeLowHex() . '-'
            . $uuid->getTimeMidHex() . '-'
            . $uuid->getTimeHiAndVersionHex() . '-'
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex() . '-'
            . $uuid->getNodeHex()
        );

        $this->assertSame(
            (string) $uuid,
            $uuid->getTimeLowHex() . '-'
            . $uuid->getTimeMidHex() . '-'
            . $uuid->getTimeHiAndVersionHex() . '-'
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex() . '-'
            . $uuid->getNodeHex()
        );

        $this->assertSame(2, $uuid->getVariant());
        $this->assertSame((int) substr($method, -1), $uuid->getVersion());
        $this->assertTrue(ctype_digit((string) $uuid->getInteger()));
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideStaticCreationMethods(): array
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
        /** @var Uuid $uuid */
        $uuid = Uuid::uuid1('00000fffffff', 0xffff);

        $this->assertInstanceOf(DateTimeImmutable::class, $uuid->getDateTime());
        $this->assertSame('00000fffffff', $uuid->getNodeHex());
        $this->assertSame('3fff', $uuid->getClockSequenceHex());
        $this->assertSame('16383', (string) $uuid->getClockSequence());
    }

    public function testUuidVersion1MethodBehavior64Bit(): void
    {
        $this->skip64BitTest();

        /** @var Uuid $uuid */
        $uuid = Uuid::uuid1('ffffffffffff', 0xffff);

        $this->assertInstanceOf(DateTimeImmutable::class, $uuid->getDateTime());
        $this->assertSame('ffffffffffff', $uuid->getNodeHex());
        $this->assertSame('281474976710655', (string) $uuid->getNode());
        $this->assertSame('3fff', $uuid->getClockSequenceHex());
        $this->assertSame('16383', (string) $uuid->getClockSequence());
        $this->assertTrue(ctype_digit((string) $uuid->getTimestamp()));
    }

    /**
     * @param mixed $uuid
     *
     * @dataProvider provideIsValid
     */
    public function testIsValid($uuid, bool $expected): void
    {
        $this->assertSame($expected, Uuid::isValid((string) $uuid), "{$uuid} is not a valid UUID");
        $this->assertSame(
            $expected,
            Uuid::isValid(strtoupper((string) $uuid)),
            strtoupper((string) $uuid) . ' is not a valid UUID'
        );
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideIsValid(): array
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
            [0, false],
            ['foobar', false],
            ['ff6f8cb0c57d51e1bb210800200c9a66', false],
            ['gf6f8cb0-c57d-51e1-bb21-0800200c9a66', false],
        ];
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testSerialization(string $string): void
    {
        $uuid = Uuid::fromString($string);

        $serialized = serialize($uuid);
        $unserialized = unserialize($serialized);

        $this->assertSame(0, $uuid->compareTo($unserialized));
        $this->assertTrue($uuid->equals($unserialized));
        $this->assertSame("\"{$string}\"", json_encode($uuid));
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testNumericReturnValues(string $string): void
    {
        $this->skip64BitTest();

        $leastSignificantBitsHex = substr(str_replace('-', '', $string), 16);
        $mostSignificantBitsHex = substr(str_replace('-', '', $string), 0, 16);
        $leastSignificantBits = BigNumber::convertToBase10($leastSignificantBitsHex, 16);
        $mostSignificantBits = BigNumber::convertToBase10($mostSignificantBitsHex, 16);

        $components = explode('-', $string);
        array_walk($components, function (&$value) {
            $value = BigNumber::convertToBase10($value, 16);
        });

        $clockSeq = (int) $components[3] & 0x3fff;
        $clockSeqHiAndReserved = (int) $components[3] >> 8;
        $clockSeqLow = (int) $components[3] & 0x00ff;

        /** @var Uuid $uuid */
        $uuid = Uuid::fromString($string);

        $this->assertSame($components[0], (string) $uuid->getTimeLow());
        $this->assertSame($components[1], (string) $uuid->getTimeMid());
        $this->assertSame($components[2], (string) $uuid->getTimeHiAndVersion());
        $this->assertSame((string) $clockSeq, (string) $uuid->getClockSequence());
        $this->assertSame((string) $clockSeqHiAndReserved, (string) $uuid->getClockSeqHiAndReserved());
        $this->assertSame((string) $clockSeqLow, (string) $uuid->getClockSeqLow());
        $this->assertSame($components[4], (string) $uuid->getNode());
        $this->assertSame($leastSignificantBits, (string) $uuid->getLeastSignificantBits());
        $this->assertSame($mostSignificantBits, (string) $uuid->getMostSignificantBits());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromBytes(string $string, ?int $version, int $variant, string $integer): void
    {
        $bytes = (string) hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromBytes($bytes);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame($string, $uuid->toString());
        $this->assertSame($version, $uuid->getVersion());
        $this->assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getTimeLowHex());
        $this->assertSame($components[1], $uuid->getTimeMidHex());
        $this->assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        $this->assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        $this->assertSame($components[4], $uuid->getNodeHex());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromInteger(string $string, ?int $version, int $variant, string $integer): void
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromInteger($integer);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame($string, $uuid->toString());
        $this->assertSame($version, $uuid->getVersion());
        $this->assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getTimeLowHex());
        $this->assertSame($components[1], $uuid->getTimeMidHex());
        $this->assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        $this->assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        $this->assertSame($components[4], $uuid->getNodeHex());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromString(string $string, ?int $version, int $variant, string $integer): void
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromString($string);

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame($string, $uuid->toString());
        $this->assertSame($version, $uuid->getVersion());
        $this->assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        $this->assertSame($components[0], $uuid->getTimeLowHex());
        $this->assertSame($components[1], $uuid->getTimeMidHex());
        $this->assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        $this->assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        $this->assertSame($components[4], $uuid->getNodeHex());
        $this->assertSame($integer, (string) $uuid->getInteger());
        $this->assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideFromStringInteger(): array
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

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetSetFactory(): void
    {
        $this->assertInstanceOf(UuidFactory::class, Uuid::getFactory());

        $factory = Mockery::mock(UuidFactory::class);
        Uuid::setFactory($factory);

        $this->assertSame($factory, Uuid::getFactory());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testFactoryProvidesFunctionality(): void
    {
        $uuid = Mockery::mock(UuidInterface::class);

        $factory = Mockery::mock(UuidFactoryInterface::class, [
            'uuid1' => $uuid,
            'uuid3' => $uuid,
            'uuid4' => $uuid,
            'uuid5' => $uuid,
            'fromBytes' => $uuid,
            'fromString' => $uuid,
            'fromInteger' => $uuid,
        ]);

        Uuid::setFactory($factory);

        $this->assertSame($uuid, Uuid::uuid1('ffffffffffff', 0xffff));
        $this->assertSame($uuid, Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::uuid4());
        $this->assertSame($uuid, Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::fromBytes((string) hex2bin('ffffffffffffffffffffffffffffffff')));
        $this->assertSame($uuid, Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
        $this->assertSame($uuid, Uuid::fromInteger('340282366920938463463374607431768211455'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingDegradedFeatures(): void
    {
        $numberConverter = new DegradedNumberConverter();
        $timeConverter = new DegradedTimeConverter();
        $builder = new DegradedUuidBuilder($numberConverter, $timeConverter);

        $factory = new UuidFactory();
        $factory->setNumberConverter($numberConverter);
        $factory->setUuidBuilder($builder);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1();

        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertInstanceOf(DegradedUuid::class, $uuid);
        $this->assertInstanceOf(DegradedNumberConverter::class, $uuid->getNumberConverter());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomCodec(): void
    {
        $mockUuid = Mockery::mock(UuidInterface::class);

        $codec = Mockery::mock(CodecInterface::class, [
            'encode' => 'abcd1234',
            'encodeBinary' => hex2bin('abcd1234'),
            'decode' => $mockUuid,
            'decodeBytes' => $mockUuid,
        ]);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        $this->assertSame('abcd1234', $uuid->toString());
        $this->assertSame(hex2bin('abcd1234'), $uuid->getBytes());
        $this->assertSame($mockUuid, Uuid::fromString('f00ba2'));
        $this->assertSame($mockUuid, Uuid::fromBytes((string) hex2bin('f00ba2')));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomRandomGenerator(): void
    {
        $generator = Mockery::mock(RandomGeneratorInterface::class, [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setRandomGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        $this->assertSame('01234567-abcd-4432-9cba-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomTimeGenerator(): void
    {
        $generator = Mockery::mock(TimeGeneratorInterface::class, [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1();

        $this->assertSame('01234567-abcd-1432-9cba-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingDefaultTimeGeneratorWithCustomProviders(): void
    {
        $nodeProvider = Mockery::mock(NodeProviderInterface::class, [
            'getNode' => '0123456789ab',
        ]);

        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $timeConverter
            ->shouldReceive('calculateTime')
            ->andReturnUsing(function ($seconds, $microSeconds) {
                return [
                    'low' => dechex($seconds),
                    'mid' => dechex($microSeconds),
                    'hi' => 'abcd',
                ];
            });

        $timeProvider = Mockery::mock(TimeProviderInterface::class, [
            'currentTime' => [
                'sec' => 1578522046,
                'usec' => 10000,
            ],
        ]);

        $generator = new DefaultTimeGenerator($nodeProvider, $timeConverter, $timeProvider);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1(null, 4095);

        $this->assertSame('5e1655be-2710-1bcd-8fff-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testHelperFunctions(): void
    {
        $uuid1 = Mockery::mock(UuidInterface::class, [
            'toString' => 'aVersion1Uuid',
        ]);
        $uuid3 = Mockery::mock(UuidInterface::class, [
            'toString' => 'aVersion3Uuid',
        ]);
        $uuid4 = Mockery::mock(UuidInterface::class, [
            'toString' => 'aVersion4Uuid',
        ]);
        $uuid5 = Mockery::mock(UuidInterface::class, [
            'toString' => 'aVersion5Uuid',
        ]);

        $factory = Mockery::mock(UuidFactoryInterface::class, [
            'uuid1' => $uuid1,
            'uuid3' => $uuid3,
            'uuid4' => $uuid4,
            'uuid5' => $uuid5,
        ]);

        Uuid::setFactory($factory);

        $this->assertSame('aVersion1Uuid', v1('ffffffffffff', 0xffff));
        $this->assertSame('aVersion3Uuid', v3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame('aVersion4Uuid', v4());
        $this->assertSame('aVersion5Uuid', v5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
    }
}
