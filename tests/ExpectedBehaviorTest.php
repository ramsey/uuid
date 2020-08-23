<?php

namespace Ramsey\Uuid\Test;

use Moontoast\Math\BigNumber;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
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
     * @dataProvider provideStaticCreationMethods
     */
    public function testStaticCreationMethodsAndStandardBehavior($method, $args)
    {
        $uuid = call_user_func_array(['Ramsey\Uuid\Uuid', $method], $args);

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertIsInt($uuid->compareTo(Uuid::uuid1()));
        self::assertNotSame(0, $uuid->compareTo(Uuid::uuid4()));
        self::assertSame(0, $uuid->compareTo(clone $uuid));
        self::assertFalse($uuid->equals(new stdClass()));
        self::assertTrue($uuid->equals(clone $uuid));
        self::assertIsString($uuid->getBytes());
        self::assertInstanceOf('Ramsey\Uuid\Converter\NumberConverterInterface', $uuid->getNumberConverter());
        self::assertIsString((string) $uuid->getHex());
        self::assertIsArray($uuid->getFieldsHex());
        self::assertArrayHasKey('time_low', $uuid->getFieldsHex());
        self::assertArrayHasKey('time_mid', $uuid->getFieldsHex());
        self::assertArrayHasKey('time_hi_and_version', $uuid->getFieldsHex());
        self::assertArrayHasKey('clock_seq_hi_and_reserved', $uuid->getFieldsHex());
        self::assertArrayHasKey('clock_seq_low', $uuid->getFieldsHex());
        self::assertArrayHasKey('node', $uuid->getFieldsHex());
        self::assertIsString($uuid->getTimeLowHex());
        self::assertIsString($uuid->getTimeMidHex());
        self::assertIsString($uuid->getTimeHiAndVersionHex());
        self::assertIsString($uuid->getClockSeqHiAndReservedHex());
        self::assertIsString($uuid->getClockSeqLowHex());
        self::assertIsString($uuid->getNodeHex());
        self::assertSame($uuid->getFieldsHex()['time_low'], $uuid->getTimeLowHex());
        self::assertSame($uuid->getFieldsHex()['time_mid'], $uuid->getTimeMidHex());
        self::assertSame($uuid->getFieldsHex()['time_hi_and_version'], $uuid->getTimeHiAndVersionHex());
        self::assertSame($uuid->getFieldsHex()['clock_seq_hi_and_reserved'], $uuid->getClockSeqHiAndReservedHex());
        self::assertSame($uuid->getFieldsHex()['clock_seq_low'], $uuid->getClockSeqLowHex());
        self::assertSame($uuid->getFieldsHex()['node'], $uuid->getNodeHex());
        self::assertSame(substr((string) $uuid->getHex(), 16), $uuid->getLeastSignificantBitsHex());
        self::assertSame(substr((string) $uuid->getHex(), 0, 16), $uuid->getMostSignificantBitsHex());

        self::assertSame(
            (string) $uuid->getHex(),
            $uuid->getTimeLowHex()
            . $uuid->getTimeMidHex()
            . $uuid->getTimeHiAndVersionHex()
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex()
            . $uuid->getNodeHex()
        );

        self::assertSame(
            (string) $uuid->getHex(),
            $uuid->getFieldsHex()['time_low']
            . $uuid->getFieldsHex()['time_mid']
            . $uuid->getFieldsHex()['time_hi_and_version']
            . $uuid->getFieldsHex()['clock_seq_hi_and_reserved']
            . $uuid->getFieldsHex()['clock_seq_low']
            . $uuid->getFieldsHex()['node']
        );

        self::assertIsString($uuid->getUrn());
        self::assertStringStartsWith('urn:uuid:', $uuid->getUrn());
        self::assertSame('urn:uuid:' . (string) $uuid->getHex(), str_replace('-', '', $uuid->getUrn()));
        self::assertSame((string) $uuid->getHex(), str_replace('-', '', $uuid->toString()));
        self::assertSame((string) $uuid->getHex(), str_replace('-', '', (string) $uuid));

        self::assertSame(
            $uuid->toString(),
            $uuid->getTimeLowHex() . '-'
            . $uuid->getTimeMidHex() . '-'
            . $uuid->getTimeHiAndVersionHex() . '-'
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex() . '-'
            . $uuid->getNodeHex()
        );

        self::assertSame(
            (string) $uuid,
            $uuid->getTimeLowHex() . '-'
            . $uuid->getTimeMidHex() . '-'
            . $uuid->getTimeHiAndVersionHex() . '-'
            . $uuid->getClockSeqHiAndReservedHex()
            . $uuid->getClockSeqLowHex() . '-'
            . $uuid->getNodeHex()
        );

        self::assertSame(2, $uuid->getVariant());
        self::assertSame((int) substr($method, -1), $uuid->getVersion());
        self::assertTrue(ctype_digit((string) $uuid->getInteger()));
    }

    public function provideStaticCreationMethods()
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

    public function testUuidVersion1MethodBehavior()
    {
        $uuid = Uuid::uuid1('00000fffffff', 0xffff);

        self::assertInstanceOf('DateTimeInterface', $uuid->getDateTime());
        self::assertSame('00000fffffff', $uuid->getNodeHex());
        self::assertSame('3fff', $uuid->getClockSequenceHex());
        self::assertSame('16383', (string) $uuid->getClockSequence());
    }

    public function testUuidVersion1MethodBehavior64Bit()
    {
        $uuid = Uuid::uuid1('ffffffffffff', 0xffff);

        self::assertInstanceOf('DateTimeInterface', $uuid->getDateTime());
        self::assertSame('ffffffffffff', $uuid->getNodeHex());
        self::assertSame('281474976710655', (string) $uuid->getNode());
        self::assertSame('3fff', $uuid->getClockSequenceHex());
        self::assertSame('16383', (string) $uuid->getClockSequence());
        self::assertTrue(ctype_digit((string) $uuid->getTimestamp()));
    }

    /**
     * @dataProvider provideIsValid
     */
    public function testIsValid($uuid, $expected)
    {
        self::assertSame($expected, Uuid::isValid($uuid), "{$uuid} is not a valid UUID");
        self::assertSame($expected, Uuid::isValid(strtoupper($uuid)), strtoupper($uuid) . ' is not a valid UUID');
    }

    public function provideIsValid()
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
    public function testSerialization($string)
    {
        $uuid = Uuid::fromString($string);

        $serialized = serialize($uuid);
        $unserialized = unserialize($serialized);

        self::assertSame(0, $uuid->compareTo($unserialized));
        self::assertTrue($uuid->equals($unserialized));
        self::assertSame("\"{$string}\"", json_encode($uuid));
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testNumericReturnValues($string)
    {
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

        $uuid = Uuid::fromString($string);

        self::assertSame($components[0], (string) $uuid->getTimeLow());
        self::assertSame($components[1], (string) $uuid->getTimeMid());
        self::assertSame($components[2], (string) $uuid->getTimeHiAndVersion());
        self::assertSame((string) $clockSeq, (string) $uuid->getClockSequence());
        self::assertSame((string) $clockSeqHiAndReserved, (string) $uuid->getClockSeqHiAndReserved());
        self::assertSame((string) $clockSeqLow, (string) $uuid->getClockSeqLow());
        self::assertSame($components[4], (string) $uuid->getNode());
        self::assertSame($leastSignificantBits, (string) $uuid->getLeastSignificantBits());
        self::assertSame($mostSignificantBits, (string) $uuid->getMostSignificantBits());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromBytes($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromBytes($bytes);

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertSame($string, $uuid->toString());
        self::assertSame($version, $uuid->getVersion());
        self::assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        self::assertSame($components[0], $uuid->getTimeLowHex());
        self::assertSame($components[1], $uuid->getTimeMidHex());
        self::assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        self::assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        self::assertSame($components[4], $uuid->getNodeHex());
        self::assertSame($integer, (string) $uuid->getInteger());
        self::assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromInteger($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromInteger($integer);

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertSame($string, $uuid->toString());
        self::assertSame($version, $uuid->getVersion());
        self::assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        self::assertSame($components[0], $uuid->getTimeLowHex());
        self::assertSame($components[1], $uuid->getTimeMidHex());
        self::assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        self::assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        self::assertSame($components[4], $uuid->getNodeHex());
        self::assertSame($integer, (string) $uuid->getInteger());
        self::assertSame($bytes, $uuid->getBytes());
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testFromString($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromString($string);

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertSame($string, $uuid->toString());
        self::assertSame($version, $uuid->getVersion());
        self::assertSame($variant, $uuid->getVariant());

        $components = explode('-', $string);

        self::assertSame($components[0], $uuid->getTimeLowHex());
        self::assertSame($components[1], $uuid->getTimeMidHex());
        self::assertSame($components[2], $uuid->getTimeHiAndVersionHex());
        self::assertSame($components[3], $uuid->getClockSeqHiAndReservedHex() . $uuid->getClockSeqLowHex());
        self::assertSame($components[4], $uuid->getNodeHex());
        self::assertSame($integer, (string) $uuid->getInteger());
        self::assertSame($bytes, $uuid->getBytes());
    }

    public function provideFromStringInteger()
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
    public function testGetSetFactory()
    {
        self::assertInstanceOf('Ramsey\Uuid\UuidFactory', Uuid::getFactory());

        $factory = \Mockery::mock('Ramsey\Uuid\UuidFactory');
        Uuid::setFactory($factory);

        self::assertSame($factory, Uuid::getFactory());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testFactoryProvidesFunctionality()
    {
        $uuid = \Mockery::mock('Ramsey\Uuid\UuidInterface');

        $factory = \Mockery::mock('Ramsey\Uuid\UuidFactoryInterface', [
            'uuid1' => $uuid,
            'uuid3' => $uuid,
            'uuid4' => $uuid,
            'uuid5' => $uuid,
            'fromBytes' => $uuid,
            'fromString' => $uuid,
            'fromInteger' => $uuid,
        ]);

        Uuid::setFactory($factory);

        self::assertSame($uuid, Uuid::uuid1('ffffffffffff', 0xffff));
        self::assertSame($uuid, Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        self::assertSame($uuid, Uuid::uuid4());
        self::assertSame($uuid, Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        self::assertSame($uuid, Uuid::fromBytes(hex2bin('ffffffffffffffffffffffffffffffff')));
        self::assertSame($uuid, Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
        self::assertSame($uuid, Uuid::fromInteger('340282366920938463463374607431768211455'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingDegradedFeatures()
    {
        $numberConverter = new DegradedNumberConverter();
        $builder = new DegradedUuidBuilder($numberConverter);

        $factory = new UuidFactory();
        $factory->setNumberConverter($numberConverter);
        $factory->setUuidBuilder($builder);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1();

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertInstanceOf('Ramsey\Uuid\DegradedUuid', $uuid);
        self::assertInstanceOf('Ramsey\Uuid\Converter\Number\DegradedNumberConverter', $uuid->getNumberConverter());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomCodec()
    {
        $mockUuid = \Mockery::mock('Ramsey\Uuid\UuidInterface');

        $codec = \Mockery::mock('Ramsey\Uuid\Codec\CodecInterface', [
            'encode' => 'abcd1234',
            'encodeBinary' => hex2bin('abcd1234'),
            'decode' => $mockUuid,
            'decodeBytes' => $mockUuid,
        ]);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        self::assertSame('abcd1234', $uuid->toString());
        self::assertSame(hex2bin('abcd1234'), $uuid->getBytes());
        self::assertSame($mockUuid, Uuid::fromString('f00ba2'));
        self::assertSame($mockUuid, Uuid::fromBytes(hex2bin('f00ba2')));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomRandomGenerator()
    {
        $generator = \Mockery::mock('Ramsey\Uuid\Generator\RandomGeneratorInterface', [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setRandomGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid4();

        self::assertSame('01234567-abcd-4432-9cba-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingCustomTimeGenerator()
    {
        $generator = \Mockery::mock('Ramsey\Uuid\Generator\TimeGeneratorInterface', [
            'generate' => hex2bin('01234567abcd5432dcba0123456789ab'),
        ]);

        $factory = new UuidFactory();
        $factory->setTimeGenerator($generator);

        Uuid::setFactory($factory);

        $uuid = Uuid::uuid1();

        self::assertSame('01234567-abcd-1432-9cba-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testUsingDefaultTimeGeneratorWithCustomProviders()
    {
        $nodeProvider = \Mockery::mock('Ramsey\Uuid\Provider\NodeProviderInterface', [
            'getNode' => new Hexadecimal('0123456789ab'),
        ]);

        $timeConverter = \Mockery::mock('Ramsey\Uuid\Converter\TimeConverterInterface');
        $timeConverter
            ->shouldReceive('calculateTime')
            ->andReturnUsing(function ($seconds, $microseconds) {
                return new Hexadecimal('abcd' . dechex($microseconds) . dechex($seconds));
            });

        $timeProvider = \Mockery::mock('Ramsey\Uuid\Provider\TimeProviderInterface', [
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

        self::assertSame('5e1655be-2710-1bcd-8fff-0123456789ab', $uuid->toString());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testHelperFunctions()
    {
        $uuid1 = \Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion1Uuid',
        ]);
        $uuid3 = \Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion3Uuid',
        ]);
        $uuid4 = \Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion4Uuid',
        ]);
        $uuid5 = \Mockery::mock('Ramsey\Uuid\UuidInterface', [
            'toString' => 'aVersion5Uuid',
        ]);

        $factory = \Mockery::mock('Ramsey\Uuid\UuidFactoryInterface', [
            'uuid1' => $uuid1,
            'uuid3' => $uuid3,
            'uuid4' => $uuid4,
            'uuid5' => $uuid5,
        ]);

        Uuid::setFactory($factory);

        self::assertSame('aVersion1Uuid', \Ramsey\Uuid\v1('ffffffffffff', 0xffff));
        self::assertSame('aVersion3Uuid', \Ramsey\Uuid\v3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        self::assertSame('aVersion4Uuid', \Ramsey\Uuid\v4());
        self::assertSame('aVersion5Uuid', \Ramsey\Uuid\v5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
    }

    /**
     * @link https://git.io/JvJZo Use of TimestampFirstCombCodec in laravel/framework
     */
    public function testUseOfTimestampFirstCombCodec()
    {
        $factory = new UuidFactory();

        $factory->setRandomGenerator(new CombGenerator(
            $factory->getRandomGenerator(),
            $factory->getNumberConverter()
        ));

        $factory->setCodec(new TimestampFirstCombCodec(
            $factory->getUuidBuilder()
        ));

        $uuid = $factory->uuid4();

        // Swap fields according to the rules for TimestampFirstCombCodec.
        $fields = array_values($uuid->getFieldsHex());
        $last48Bits = $fields[5];
        $fields[5] = $fields[0] . $fields[1];
        $fields[0] = substr($last48Bits, 0, 8);
        $fields[1] = substr($last48Bits, 8, 4);

        $expectedHex = implode('', $fields);
        $expectedBytes = hex2bin($expectedHex);

        self::assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        self::assertSame(2, $uuid->getVariant());
        self::assertSame(4, $uuid->getVersion());
        self::assertSame($expectedBytes, $uuid->getBytes());
        self::assertSame($expectedHex, (string) $uuid->getHex());
    }

    /**
     * @dataProvider provideUuidConstantTests
     */
    public function testUuidConstants($constantName, $expected)
    {
        self::assertSame($expected, constant("Ramsey\\Uuid\\Uuid::{$constantName}"));
    }

    public function provideUuidConstantTests()
    {
        return [
            ['NAMESPACE_DNS', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_URL', '6ba7b811-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_X500', '6ba7b814-9dad-11d1-80b4-00c04fd430c8'],
            ['NIL', '00000000-0000-0000-0000-000000000000'],
            ['RESERVED_NCS', 0],
            ['RFC_4122', 2],
            ['RESERVED_MICROSOFT', 6],
            ['RESERVED_FUTURE', 7],
            ['VALID_PATTERN', '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$'],
            ['UUID_TYPE_TIME', 1],
            ['UUID_TYPE_IDENTIFIER', 2],
            ['UUID_TYPE_HASH_MD5', 3],
            ['UUID_TYPE_RANDOM', 4],
            ['UUID_TYPE_HASH_SHA1', 5],
        ];
    }
}
