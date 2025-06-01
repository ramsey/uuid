<?php

namespace Ramsey\Uuid\Test;

use Brick\Math\BigInteger;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
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

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertIsInt($uuid->compareTo(Uuid::uuid1()));
        $this->assertNotSame(0, $uuid->compareTo(Uuid::uuid4()));
        $this->assertSame(0, $uuid->compareTo(clone $uuid));
        $this->assertFalse($uuid->equals(new stdClass()));
        $this->assertTrue($uuid->equals(clone $uuid));
        $this->assertIsString($uuid->getBytes());
        $this->assertInstanceOf('Ramsey\Uuid\Converter\NumberConverterInterface', $uuid->getNumberConverter());
        $this->assertIsString((string) $uuid->getHex());
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
        $this->assertSame(substr((string) $uuid->getHex(), 16), $uuid->getLeastSignificantBitsHex());
        $this->assertSame(substr((string) $uuid->getHex(), 0, 16), $uuid->getMostSignificantBitsHex());

        $this->assertSame(
            (string) $uuid->getHex(),
            $uuid->getTimeLowHex()
                . $uuid->getTimeMidHex()
                . $uuid->getTimeHiAndVersionHex()
                . $uuid->getClockSeqHiAndReservedHex()
                . $uuid->getClockSeqLowHex()
                . $uuid->getNodeHex()
        );

        $this->assertSame(
            (string) $uuid->getHex(),
            $uuid->getFieldsHex()['time_low']
                . $uuid->getFieldsHex()['time_mid']
                . $uuid->getFieldsHex()['time_hi_and_version']
                . $uuid->getFieldsHex()['clock_seq_hi_and_reserved']
                . $uuid->getFieldsHex()['clock_seq_low']
                . $uuid->getFieldsHex()['node']
        );

        $this->assertIsString($uuid->getUrn());
        $this->assertStringStartsWith('urn:uuid:', $uuid->getUrn());
        $this->assertSame('urn:uuid:' . (string) $uuid->getHex(), str_replace('-', '', $uuid->getUrn()));
        $this->assertSame((string) $uuid->getHex(), str_replace('-', '', $uuid->toString()));
        $this->assertSame((string) $uuid->getHex(), str_replace('-', '', (string) $uuid));

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
        $this->assertSame(1, preg_match('/^\d+$/', (string) $uuid->getInteger()));
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

        $this->assertInstanceOf('DateTimeInterface', $uuid->getDateTime());
        $this->assertSame('00000fffffff', $uuid->getNodeHex());
        $this->assertSame('3fff', $uuid->getClockSequenceHex());
        $this->assertSame('16383', (string) $uuid->getClockSequence());
    }

    public function testUuidVersion1MethodBehavior64Bit()
    {
        $uuid = Uuid::uuid1('ffffffffffff', 0xffff);

        $this->assertInstanceOf('DateTimeInterface', $uuid->getDateTime());
        $this->assertSame('ffffffffffff', $uuid->getNodeHex());
        $this->assertSame('281474976710655', (string) $uuid->getNode());
        $this->assertSame('3fff', $uuid->getClockSequenceHex());
        $this->assertSame('16383', (string) $uuid->getClockSequence());
        $this->assertSame(1, preg_match('/^\d+$/', (string) $uuid->getTimestamp()));
    }

    /**
     * @dataProvider provideIsValid
     */
    public function testIsValid($uuid, $expected)
    {
        $this->assertSame($expected, Uuid::isValid($uuid), "{$uuid} is not a valid UUID");
        $this->assertSame($expected, Uuid::isValid(strtoupper($uuid)), strtoupper($uuid) . ' is not a valid UUID');
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

        $this->assertSame(0, $uuid->compareTo($unserialized));
        $this->assertTrue($uuid->equals($unserialized));
        $this->assertSame("\"{$string}\"", json_encode($uuid));
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testSerializationWithOrderedTimeCodec($string)
    {
        $factory = new UuidFactory();
        $factory->setCodec(new OrderedTimeCodec(
            $factory->getUuidBuilder()
        ));

        $previousFactory = Uuid::getFactory();
        Uuid::setFactory($factory);
        $uuid = Uuid::fromString($string);

        $serialized = serialize($uuid);
        $unserialized = unserialize($serialized);

        Uuid::setFactory($previousFactory);

        $this->assertSame(0, $uuid->compareTo($unserialized));
        $this->assertTrue($uuid->equals($unserialized));
        $this->assertSame("\"{$string}\"", json_encode($uuid));
    }

    /**
     * @dataProvider provideFromStringInteger
     */
    public function testNumericReturnValues($string)
    {
        $leastSignificantBitsHex = substr(str_replace('-', '', $string), 16);
        $mostSignificantBitsHex = substr(str_replace('-', '', $string), 0, 16);
        $leastSignificantBits = BigInteger::fromBase($leastSignificantBitsHex, 16)->__toString();
        $mostSignificantBits = BigInteger::fromBase($mostSignificantBitsHex, 16)->__toString();

        $components = explode('-', $string);
        array_walk($components, function (&$value) {
            $value = BigInteger::fromBase($value, 16)->__toString();
        });

        if (strtolower($string) === Uuid::MAX) {
            $clockSeq = (int) $components[3];
        } else {
            $clockSeq = (int) $components[3] & 0x3fff;
        }

        $clockSeqHiAndReserved = (int) $components[3] >> 8;
        $clockSeqLow = (int) $components[3] & 0x00ff;

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
    public function testFromBytes($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromBytes($bytes);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
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
    public function testFromInteger($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromInteger($integer);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
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
    public function testFromString($string, $version, $variant, $integer)
    {
        $bytes = hex2bin(str_replace('-', '', $string));

        $uuid = Uuid::fromString($string);

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
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
        $this->assertInstanceOf('Ramsey\Uuid\UuidFactory', Uuid::getFactory());

        $factory = \Mockery::mock('Ramsey\Uuid\UuidFactory');
        Uuid::setFactory($factory);

        $this->assertSame($factory, Uuid::getFactory());
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

        $this->assertSame($uuid, Uuid::uuid1('ffffffffffff', 0xffff));
        $this->assertSame($uuid, Uuid::uuid3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::uuid4());
        $this->assertSame($uuid, Uuid::uuid5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame($uuid, Uuid::fromBytes(hex2bin('ffffffffffffffffffffffffffffffff')));
        $this->assertSame($uuid, Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
        $this->assertSame($uuid, Uuid::fromInteger('340282366920938463463374607431768211455'));
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

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertInstanceOf('Ramsey\Uuid\DegradedUuid', $uuid);
        $this->assertInstanceOf('Ramsey\Uuid\Converter\Number\DegradedNumberConverter', $uuid->getNumberConverter());
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

        $this->assertSame('abcd1234', $uuid->toString());
        $this->assertSame(hex2bin('abcd1234'), $uuid->getBytes());
        $this->assertSame($mockUuid, Uuid::fromString('f00ba2'));
        $this->assertSame($mockUuid, Uuid::fromBytes(hex2bin('f00ba2')));
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

        $this->assertSame('01234567-abcd-4432-9cba-0123456789ab', $uuid->toString());
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

        $this->assertSame('01234567-abcd-1432-9cba-0123456789ab', $uuid->toString());
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

        $this->assertSame('5e1655be-2710-1bcd-8fff-0123456789ab', $uuid->toString());
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

        $this->assertSame('aVersion1Uuid', \Ramsey\Uuid\v1('ffffffffffff', 0xffff));
        $this->assertSame('aVersion3Uuid', \Ramsey\Uuid\v3(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
        $this->assertSame('aVersion4Uuid', \Ramsey\Uuid\v4());
        $this->assertSame('aVersion5Uuid', \Ramsey\Uuid\v5(Uuid::NAMESPACE_URL, 'https://example.com/foo'));
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

        $this->assertInstanceOf('Ramsey\Uuid\UuidInterface', $uuid);
        $this->assertSame(2, $uuid->getVariant());
        $this->assertSame(4, $uuid->getVersion());
        $this->assertSame($expectedBytes, $uuid->getBytes());
        $this->assertSame($expectedHex, (string) $uuid->getHex());
    }

    /**
     * @dataProvider provideUuidConstantTests
     */
    public function testUuidConstants($constantName, $expected)
    {
        $this->assertSame($expected, constant("Ramsey\\Uuid\\Uuid::{$constantName}"));
    }

    public function provideUuidConstantTests()
    {
        return [
            ['NAMESPACE_DNS', '6ba7b810-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_URL', '6ba7b811-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_OID', '6ba7b812-9dad-11d1-80b4-00c04fd430c8'],
            ['NAMESPACE_X500', '6ba7b814-9dad-11d1-80b4-00c04fd430c8'],
            ['NIL', '00000000-0000-0000-0000-000000000000'],
            ['MAX', 'ffffffff-ffff-ffff-ffff-ffffffffffff'],
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
            ['UUID_TYPE_REORDERED_TIME', 6],
            ['UUID_TYPE_UNIX_TIME', 7],
        ];
    }
}
