<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use DateTimeInterface;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Codec\TimestampLastCombCodec;
use Ramsey\Uuid\Converter\Number\BigNumberConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\DateTimeException;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Type\Time;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Validator\Validator;
use Ramsey\Uuid\Validator\ValidatorInterface;
use stdClass;

use function strlen;

class UuidTest extends TestCase
{
    protected function setUp(): void
    {
        Uuid::setFactory(new UuidFactory());
    }

    public function testFromString(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * Tests that UUID and GUID's have the same textual representation but not
     * the same binary representation.
     */
    public function testFromGuidString(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        Uuid::setFactory(new UuidFactory(new FeatureSet(true)));

        $guid = Guid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertInstanceOf(Uuid::class, $guid);

        // UUID's and GUID's share the same textual representation.
        $this->assertSame($uuid->toString(), $guid->toString());

        // But not the same binary representation.
        $this->assertNotSame($uuid->getBytes(), $guid->getBytes());
    }

    public function testFromStringWithCurlyBraces(): void
    {
        $uuid = Uuid::fromString('{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}');
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testFromStringWithInvalidUuidString(): void
    {
        $this->expectException(InvalidUuidStringException::class);
        $this->expectExceptionMessage('Invalid UUID string:');

        Uuid::fromString('ff6f8cb0-c57d-11e1-9b21');
    }

    public function testFromStringWithTrailingNewLine(): void
    {
        $this->expectException(InvalidUuidStringException::class);
        $this->expectExceptionMessage('Invalid UUID string:');

        Uuid::fromString("d0d5f586-21d1-470c-8088-55c8857728dc\n");
    }

    public function testFromStringWithUrn(): void
    {
        $uuid = Uuid::fromString('urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testGetBytes(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(16, strlen($uuid->getBytes()));
        $this->assertEquals('/2+MsMV9EeGbIQgAIAyaZg==', base64_encode($uuid->getBytes()));
    }

    public function testGetClockSeqHiAndReserved(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(155, $uuid->getClockSeqHiAndReserved());
    }

    public function testGetClockSeqHiAndReservedHex(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('9b', $uuid->getClockSeqHiAndReservedHex());
    }

    public function testGetClockSeqLow(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(33, $uuid->getClockSeqLow());
    }

    public function testGetClockSeqLowHex(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('21', $uuid->getClockSeqLowHex());
    }

    public function testGetClockSequence(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(6945, $uuid->getClockSequence());
    }

    public function testGetClockSequenceHex(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('1b21', $uuid->getClockSequenceHex());
    }

    public function testGetDateTime(): void
    {
        // Check a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals('2012-07-04T02:14:34+00:00', $uuid->getDateTime()->format('c'));

        // Check an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals('1582-10-16T16:34:04+00:00', $uuid->getDateTime()->format('c'));

        // Check a future date
        $uuid = Uuid::fromString('ff9785f6-ffff-1fff-9669-00007ffffffe');
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals('5236-03-31T21:21:00+00:00', $uuid->getDateTime()->format('c'));

        // Check the oldest date
        $uuid = Uuid::fromString('00000000-0000-1000-9669-00007ffffffe');
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals('1582-10-15T00:00:00+00:00', $uuid->getDateTime()->format('c'));
    }

    public function testGetDateTimeFromNonVersion1Uuid(): void
    {
        // Using a version 4 UUID to test
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a time-based UUID');

        $date = $uuid->getDateTime();
    }

    public function testGetFields(): void
    {
        $fields = [
            'time_low' => '4285500592',
            'time_mid' => '50557',
            'time_hi_and_version' => '4577',
            'clock_seq_hi_and_reserved' => '155',
            'clock_seq_low' => '33',
            'node' => '8796630719078',
        ];

        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame($fields, $uuid->getFields());
    }

    public function testGetFieldsHex(): void
    {
        $fields = [
            'time_low' => 'ff6f8cb0',
            'time_mid' => 'c57d',
            'time_hi_and_version' => '11e1',
            'clock_seq_hi_and_reserved' => '9b',
            'clock_seq_low' => '21',
            'node' => '0800200c9a66',
        ];

        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame($fields, $uuid->getFieldsHex());
    }

    public function testGetLeastSignificantBits(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame('11178224546741000806', $uuid->getLeastSignificantBits());
    }

    public function testGetLeastSignificantBitsHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame('9b210800200c9a66', $uuid->getLeastSignificantBitsHex());
    }

    public function testGetMostSignificantBits(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertSame('18406084892941947361', $uuid->getMostSignificantBits());
    }

    public function testGetMostSignificantBitsHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('ff6f8cb0c57d11e1', $uuid->getMostSignificantBitsHex());
    }

    public function testGetNode(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertSame('8796630719078', $uuid->getNode());
    }

    public function testGetNodeHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('0800200c9a66', $uuid->getNodeHex());
    }

    public function testGetTimeHiAndVersion(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(4577, $uuid->getTimeHiAndVersion());
    }

    public function testGetTimeHiAndVersionHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('11e1', $uuid->getTimeHiAndVersionHex());
    }

    public function testGetTimeLow(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertSame('4285500592', $uuid->getTimeLow());
    }

    public function testGetTimeLowHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('ff6f8cb0', $uuid->getTimeLowHex());
    }

    public function testGetTimeMid(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(50557, $uuid->getTimeMid());
    }

    public function testGetTimeMidHex(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('c57d', $uuid->getTimeMidHex());
    }

    public function testGetTimestamp(): void
    {
        // Check for a recent date
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertSame('135606608744910000', $uuid->getTimestamp());

        // Check for an old date
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertSame('1460440000000', $uuid->getTimestamp());
    }

    public function testGetTimestampHex(): void
    {
        // Check for a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('1e1c57dff6f8cb0', $uuid->getTimestampHex());

        // Check for an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertEquals('00001540901e600', $uuid->getTimestampHex());
    }

    public function testGetTimestampFromNonVersion1Uuid(): void
    {
        // Using a version 4 UUID to test
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a time-based UUID');

        $ts = $uuid->getTimestamp();
    }

    public function testGetTimestampHexFromNonVersion1Uuid(): void
    {
        // Using a version 4 UUID to test
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage('Not a time-based UUID');

        $ts = $uuid->getTimestampHex();
    }

    public function testGetUrn(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->getUrn());
    }

    /**
     * @dataProvider provideVariousVariantUuids
     */
    public function testGetVariantForVariousVariantUuids(string $uuid, int $variant): void
    {
        $uuid = Uuid::fromString($uuid);
        $this->assertSame($variant, $uuid->getVariant());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideVariousVariantUuids(): array
    {
        return [
            ['ff6f8cb0-c57d-11e1-0b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-1b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-2b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-3b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-4b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-5b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-6b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-7b21-0800200c9a66', Uuid::RESERVED_NCS],
            ['ff6f8cb0-c57d-11e1-8b21-0800200c9a66', Uuid::RFC_4122],
            ['ff6f8cb0-c57d-11e1-9b21-0800200c9a66', Uuid::RFC_4122],
            ['ff6f8cb0-c57d-11e1-ab21-0800200c9a66', Uuid::RFC_4122],
            ['ff6f8cb0-c57d-11e1-bb21-0800200c9a66', Uuid::RFC_4122],
            ['ff6f8cb0-c57d-11e1-cb21-0800200c9a66', Uuid::RESERVED_MICROSOFT],
            ['ff6f8cb0-c57d-11e1-db21-0800200c9a66', Uuid::RESERVED_MICROSOFT],
            ['ff6f8cb0-c57d-11e1-eb21-0800200c9a66', Uuid::RESERVED_FUTURE],
            ['ff6f8cb0-c57d-11e1-fb21-0800200c9a66', Uuid::RESERVED_FUTURE],
        ];
    }

    public function testGetVersionForVersion1(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(1, $uuid->getVersion());
    }

    public function testGetVersionForVersion2(): void
    {
        $uuid = Uuid::fromString('6fa459ea-ee8a-2ca4-894e-db77e160355e');
        $this->assertEquals(2, $uuid->getVersion());
    }

    public function testGetVersionForVersion3(): void
    {
        $uuid = Uuid::fromString('6fa459ea-ee8a-3ca4-894e-db77e160355e');
        $this->assertEquals(3, $uuid->getVersion());
    }

    public function testGetVersionForVersion4(): void
    {
        $uuid = Uuid::fromString('6fabf0bc-603a-42f2-925b-d9f779bd0032');
        $this->assertEquals(4, $uuid->getVersion());
    }

    public function testGetVersionForVersion5(): void
    {
        $uuid = Uuid::fromString('886313e1-3b8a-5372-9b90-0c9aee199e5d');
        $this->assertEquals(5, $uuid->getVersion());
    }

    public function testToString(): void
    {
        // Check with a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', (string) $uuid);

        // Check with an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertEquals('0901e600-0154-1000-9b21-0800200c9a66', $uuid->toString());
        $this->assertEquals('0901e600-0154-1000-9b21-0800200c9a66', (string) $uuid);
    }

    public function testUuid1(): void
    {
        $uuid = Uuid::uuid1();
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
    }

    public function testUuid1WithNodeAndClockSequence(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::uuid1('0800200c9a66', 0x1669);
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
        $this->assertEquals(5737, $uuid->getClockSequence());
        $this->assertSame('8796630719078', $uuid->getNode());
        $this->assertEquals('9669-0800200c9a66', substr($uuid->toString(), 19));
    }

    public function testUuid1WithHexadecimalNode(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::uuid1('7160355e');

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
        $this->assertEquals('00007160355e', $uuid->getNodeHex());
        $this->assertSame('1902130526', $uuid->getNode());
    }

    public function testUuid1WithMixedCaseHexadecimalNode(): void
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::uuid1('71B0aD5e');

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
        $this->assertEquals('000071b0ad5e', $uuid->getNodeHex());
        $this->assertEquals('1907404126', $uuid->getNode());
    }

    public function testUuid1WithOutOfBoundsNode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid node value');

        $uuid = Uuid::uuid1('9223372036854775808');
    }

    public function testUuid1WithNonHexadecimalNode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid node value');

        $uuid = Uuid::uuid1('db77e160355g');
    }

    public function testUuid1WithNon48bitNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid node value');

        $uuid = Uuid::uuid1('db77e160355ef');
    }

    public function testUuid1WithRandomNode(): void
    {
        Uuid::setFactory(new UuidFactory(new FeatureSet(false, false, false, true)));

        $uuid = Uuid::uuid1();
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertInstanceOf(DateTimeInterface::class, $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     *
     * @see http://docs.python.org/library/uuid.html
     */
    public function testUuid3WithNamespaceAsUuidObject(): void
    {
        $nsUuid = Uuid::fromString(Uuid::NAMESPACE_DNS);
        $uuid = Uuid::uuid3($nsUuid, 'python.org');
        $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(3, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     *
     * @see http://docs.python.org/library/uuid.html
     */
    public function testUuid3WithNamespaceAsUuidString(): void
    {
        $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'python.org');
        $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(3, $uuid->getVersion());
    }

    /**
     * Tests more known version-3 UUIDs
     *
     * Taken from the Python UUID tests in
     * http://hg.python.org/cpython/file/2f4c4db9aee5/Lib/test/test_uuid.py
     */
    public function testUuid3WithKnownUuids(): void
    {
        $uuids = [
            '6fa459ea-ee8a-3ca4-894e-db77e160355e' => Uuid::uuid3(Uuid::NAMESPACE_DNS, 'python.org'),
            '9fe8e8c4-aaa8-32a9-a55c-4535a88b748d' => Uuid::uuid3(Uuid::NAMESPACE_URL, 'http://python.org/'),
            'dd1a1cef-13d5-368a-ad82-eca71acd4cd1' => Uuid::uuid3(Uuid::NAMESPACE_OID, '1.3.6.1'),
            '658d3002-db6b-3040-a1d1-8ddd7d189a4d' => Uuid::uuid3(Uuid::NAMESPACE_X500, 'c=ca'),
        ];

        foreach ($uuids as $ustr => $uobj) {
            $this->assertEquals(Uuid::RFC_4122, $uobj->getVariant());
            $this->assertEquals(3, $uobj->getVersion());
            $this->assertEquals(Uuid::fromString($ustr), $uobj);
            $this->assertEquals((string) $uobj, $ustr);
        }
    }

    public function testUuid4(): void
    {
        $uuid = Uuid::uuid4();
        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(4, $uuid->getVersion());
    }

    /**
     * Tests that generated UUID's using timestamp last COMB are sequential
     */
    public function testUuid4TimestampLastComb(): void
    {
        $mock = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $mock->expects($this->any())
            ->method('generate')
            ->willReturnCallback(function ($length) {
                // Makes first fields of UUIDs equal
                return hex2bin(str_pad('', $length * 2, '0'));
            });

        $factory = new UuidFactory();
        $generator = new CombGenerator($mock, $factory->getNumberConverter());
        $codec = new TimestampLastCombCodec($factory->getUuidBuilder());
        $factory->setRandomGenerator($generator);
        $factory->setCodec($codec);

        $previous = $factory->uuid4();

        for ($i = 0; $i < 1000; $i++) {
            usleep(100);
            $uuid = $factory->uuid4();
            $this->assertGreaterThan($previous->toString(), $uuid->toString());

            $previous = $uuid;
        }
    }

    /**
     * Tests that generated UUID's using timestamp first COMB are sequential
     */
    public function testUuid4TimestampFirstComb(): void
    {
        $mock = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $mock->expects($this->any())
            ->method('generate')
            ->willReturnCallback(function ($length) {
                // Makes first fields of UUIDs equal
                return hex2bin(str_pad('', $length * 2, '0'));
            });

        $factory = new UuidFactory();
        $generator = new CombGenerator($mock, $factory->getNumberConverter());
        $codec = new TimestampFirstCombCodec($factory->getUuidBuilder());
        $factory->setRandomGenerator($generator);
        $factory->setCodec($codec);

        $previous = $factory->uuid4();

        for ($i = 0; $i < 1000; $i++) {
            usleep(100);
            $uuid = $factory->uuid4();
            $this->assertGreaterThan($previous->toString(), $uuid->toString());

            $previous = $uuid;
        }
    }

    /**
     * Test that COMB UUID's have a version 4 flag
     */
    public function testUuid4CombVersion(): void
    {
        $factory = new UuidFactory();
        $generator = new CombGenerator(
            RandomGeneratorFactory::getGenerator(),
            $factory->getNumberConverter()
        );

        $factory->setRandomGenerator($generator);

        $uuid = $factory->uuid4();

        $this->assertEquals(4, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     *
     * @see http://docs.python.org/library/uuid.html
     */
    public function testUuid5WithNamespaceAsUuidObject(): void
    {
        $nsUuid = Uuid::fromString(Uuid::NAMESPACE_DNS);
        $uuid = Uuid::uuid5($nsUuid, 'python.org');
        $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(5, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     *
     * @see http://docs.python.org/library/uuid.html
     */
    public function testUuid5WithNamespaceAsUuidString(): void
    {
        $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(5, $uuid->getVersion());
    }

    /**
     * Tests more known version-5 UUIDs
     *
     * Taken from the Python UUID tests in
     * http://hg.python.org/cpython/file/2f4c4db9aee5/Lib/test/test_uuid.py
     */
    public function testUuid5WithKnownUuids(): void
    {
        $uuids = [
            '886313e1-3b8a-5372-9b90-0c9aee199e5d' => Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org'),
            '4c565f0d-3f5a-5890-b41b-20cf47701c5e' => Uuid::uuid5(Uuid::NAMESPACE_URL, 'http://python.org/'),
            '1447fa61-5277-5fef-a9b3-fbc6e44f4af3' => Uuid::uuid5(Uuid::NAMESPACE_OID, '1.3.6.1'),
            'cc957dd1-a972-5349-98cd-874190002798' => Uuid::uuid5(Uuid::NAMESPACE_X500, 'c=ca'),
        ];

        foreach ($uuids as $ustr => $uobj) {
            $this->assertEquals(Uuid::RFC_4122, $uobj->getVariant());
            $this->assertEquals(5, $uobj->getVersion());
            $this->assertEquals(Uuid::fromString($ustr), $uobj);
            $this->assertEquals((string) $uobj, $ustr);
        }
    }

    public function testCompareTo(): void
    {
        // $uuid1 and $uuid2 are identical
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        // The next three UUIDs are used for comparing msb and lsb in
        // the compareTo() method

        // msb are less than $uuid4, lsb are greater than $uuid5
        $uuid3 = Uuid::fromString('44cca71e-d13d-11e1-a959-c8bcc8a476f4');

        // msb are greater than $uuid3, lsb are equal to those in $uuid3
        $uuid4 = Uuid::fromString('44cca71e-d13d-11e2-a959-c8bcc8a476f4');

        // msb are equal to those in $uuid3, lsb are less than in $uuid3
        $uuid5 = Uuid::fromString('44cca71e-d13d-11e1-a959-c8bcc8a476f3');

        $this->assertEquals(0, $uuid1->compareTo($uuid2));
        $this->assertEquals(0, $uuid2->compareTo($uuid1));
        $this->assertEquals(-1, $uuid3->compareTo($uuid4));
        $this->assertEquals(1, $uuid4->compareTo($uuid3));
        $this->assertEquals(-1, $uuid5->compareTo($uuid3));
        $this->assertEquals(1, $uuid3->compareTo($uuid5));
    }

    public function testCompareToReturnsZeroWhenDifferentCases(): void
    {
        $uuidString = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        // $uuid1 and $uuid2 are identical
        $uuid1 = Uuid::fromString($uuidString);
        $uuid2 = Uuid::fromString(strtoupper($uuidString));

        $this->assertEquals(0, $uuid1->compareTo($uuid2));
        $this->assertEquals(0, $uuid2->compareTo($uuid1));
    }

    public function testEqualsReturnsTrueWhenDifferentCases(): void
    {
        $uuidString = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';
        // $uuid1 and $uuid2 are identical
        $uuid1 = Uuid::fromString($uuidString);
        $uuid2 = Uuid::fromString(strtoupper($uuidString));

        $this->assertTrue($uuid1->equals($uuid2));
        $this->assertTrue($uuid2->equals($uuid1));
    }

    public function testEquals(): void
    {
        $uuid1 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $uuid2 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $uuid3 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');

        $this->assertTrue($uuid1->equals($uuid2));
        $this->assertFalse($uuid1->equals($uuid3));
        $this->assertFalse($uuid1->equals(new stdClass()));
    }

    public function testCalculateUuidTime(): void
    {
        $timeOfDay = new FixedTimeProvider(new Time(1348845514, 277885));

        $featureSet = new FeatureSet();
        $featureSet->setTimeProvider($timeOfDay);

        // For usec = 277885
        Uuid::setFactory(new UuidFactory($featureSet));
        $uuidA = Uuid::uuid1(0x00007ffffffe, 0x1669);

        $this->assertEquals('c4dbe7e2-097f-11e2-9669-00007ffffffe', (string) $uuidA);
        $this->assertEquals('c4dbe7e2', $uuidA->getTimeLowHex());
        $this->assertEquals('097f', $uuidA->getTimeMidHex());
        $this->assertEquals('11e2', $uuidA->getTimeHiAndVersionHex());

        // For usec = 0
        $timeOfDay->setUsec(0);
        $uuidB = Uuid::uuid1(0x00007ffffffe, 0x1669);

        $this->assertEquals('c4b18100-097f-11e2-9669-00007ffffffe', (string) $uuidB);
        $this->assertEquals('c4b18100', $uuidB->getTimeLowHex());
        $this->assertEquals('097f', $uuidB->getTimeMidHex());
        $this->assertEquals('11e2', $uuidB->getTimeHiAndVersionHex());

        // For usec = 999999
        $timeOfDay->setUsec(999999);
        $uuidC = Uuid::uuid1(0x00007ffffffe, 0x1669);

        $this->assertEquals('c54a1776-097f-11e2-9669-00007ffffffe', (string) $uuidC);
        $this->assertEquals('c54a1776', $uuidC->getTimeLowHex());
        $this->assertEquals('097f', $uuidC->getTimeMidHex());
        $this->assertEquals('11e2', $uuidC->getTimeHiAndVersionHex());
    }

    public function testCalculateUuidTimeUpperLowerBounds(): void
    {
        // 5235-03-31T21:20:59+00:00
        $timeOfDay = new FixedTimeProvider(new Time('103072857659', '999999'));

        $featureSet = new FeatureSet();
        $featureSet->setTimeProvider($timeOfDay);

        Uuid::setFactory(new UuidFactory($featureSet));
        $uuidA = Uuid::uuid1(0x00007ffffffe, 0x1669);

        $this->assertEquals('ff9785f6-ffff-1fff-9669-00007ffffffe', (string) $uuidA);
        $this->assertEquals('ff9785f6', $uuidA->getTimeLowHex());
        $this->assertEquals('ffff', $uuidA->getTimeMidHex());
        $this->assertEquals('1fff', $uuidA->getTimeHiAndVersionHex());

        // 1582-10-15T00:00:00+00:00
        $timeOfDay = new FixedTimeProvider(new Time('-12219292800', '0'));

        $featureSet->setTimeProvider($timeOfDay);

        Uuid::setFactory(new UuidFactory($featureSet));
        $uuidB = Uuid::uuid1(0x00007ffffffe, 0x1669);

        $this->assertEquals('00000000-0000-1000-9669-00007ffffffe', (string) $uuidB);
        $this->assertEquals('00000000', $uuidB->getTimeLowHex());
        $this->assertEquals('0000', $uuidB->getTimeMidHex());
        $this->assertEquals('1000', $uuidB->getTimeHiAndVersionHex());
    }

    /**
     * Iterates over a 3600-second period and tests to ensure that, for each
     * second in the period, the 32-bit and 64-bit versions of the UUID match
     */
    public function test32BitMatch64BitForOneHourPeriod(): void
    {
        $currentTime = strtotime('2012-12-11T00:00:00+00:00');
        $endTime = $currentTime + 3600;

        $timeOfDay = new FixedTimeProvider(new Time($currentTime, 0));

        $smallIntFeatureSet = new FeatureSet(false, true);
        $smallIntFeatureSet->setTimeProvider($timeOfDay);

        $smallIntFactory = new UuidFactory($smallIntFeatureSet);

        $featureSet = new FeatureSet();
        $featureSet->setTimeProvider($timeOfDay);

        $factory = new UuidFactory($featureSet);

        while ($currentTime <= $endTime) {
            foreach ([0, 50000, 250000, 500000, 750000, 999999] as $usec) {
                $timeOfDay->setSec($currentTime);
                $timeOfDay->setUsec($usec);

                $uuid32 = $smallIntFactory->uuid1(0x00007ffffffe, 0x1669);
                $uuid64 = $factory->uuid1(0x00007ffffffe, 0x1669);

                $this->assertTrue(
                    $uuid32->equals($uuid64),
                    'Breaks at ' . gmdate('r', $currentTime)
                        . "; 32-bit: {$uuid32->toString()}, 64-bit: {$uuid64->toString()}"
                );

                // Assert that the time matches
                $usecAdd = BigDecimal::of($usec)->dividedBy('1000000', 14, RoundingMode::HALF_UP);
                $testTime = BigDecimal::of($currentTime)->plus($usecAdd)->toScale(0, RoundingMode::HALF_UP);
                $this->assertEquals((string) $testTime, $uuid64->getDateTime()->getTimestamp());
                $this->assertEquals((string) $testTime, $uuid32->getDateTime()->getTimestamp());
            }

            $currentTime++;
        }
    }

    /**
     * This method should respond to the result of the factory
     */
    public function testIsValid(): void
    {
        $argument = uniqid('passed argument ');

        /** @var MockObject & ValidatorInterface $validator */
        $validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $validator->expects($this->once())->method('validate')->with($argument)->willReturn(true);

        /** @var UuidFactory $factory */
        $factory = Uuid::getFactory();
        $factory->setValidator($validator);

        $this->assertTrue(Uuid::isValid($argument));

        // reset the static validator
        $factory->setValidator(new Validator());
    }

    public function testUsingNilAsValidUuid(): void
    {
        $this->assertInstanceOf(Uuid::class, Uuid::uuid3(Uuid::NIL, 'randomtext'));
        $this->assertInstanceOf(Uuid::class, Uuid::uuid5(Uuid::NIL, 'randomtext'));
    }

    public function testFromBytes(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $bytes = $uuid->getBytes();

        $fromBytesUuid = Uuid::fromBytes($bytes);

        $this->assertTrue($uuid->equals($fromBytesUuid));
    }

    public function testGuidBytesMatchesUuidWithSameString(): void
    {
        $uuidFactory = new UuidFactory(new FeatureSet(false));
        $guidFactory = new UuidFactory(new FeatureSet(true));

        $uuid = $uuidFactory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $bytes = $uuid->getBytes();

        // Swap the order of the bytes for a GUID.
        $guidBytes = $bytes[3] . $bytes[2] . $bytes[1] . $bytes[0];
        $guidBytes .= $bytes[5] . $bytes[4];
        $guidBytes .= $bytes[7] . $bytes[6];
        $guidBytes .= substr($bytes, 8);

        $guid = $guidFactory->fromBytes($guidBytes);

        $this->assertSame($uuid->toString(), $guid->toString());
        $this->assertTrue($uuid->equals($guid));
    }

    public function testGuidBytesProducesSameGuidString(): void
    {
        $guidFactory = new UuidFactory(new FeatureSet(true));

        $guid = $guidFactory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $bytes = $guid->getBytes();

        $parsedGuid = $guidFactory->fromBytes($bytes);

        $this->assertSame($guid->toString(), $parsedGuid->toString());
        $this->assertTrue($guid->equals($parsedGuid));
    }

    public function testFromBytesArgumentTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Uuid::fromBytes('thisisveryshort');
    }

    public function testFromBytesArgumentTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Uuid::fromBytes('thisisabittoolong');
    }

    public function testFromInteger(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $integer = $uuid->getInteger();

        $fromIntegerUuid = Uuid::fromInteger($integer);

        $this->assertTrue($uuid->equals($fromIntegerUuid));
    }

    /**
     * This test ensures that Ramsey\Uuid passes the same test cases
     * as the Python UUID library.
     *
     * @param string[] $fields
     *
     * @dataProvider providePythonTests
     */
    public function testUuidPassesPythonTests(
        string $string,
        string $curly,
        string $hex,
        string $bytes,
        string $int,
        array $fields,
        string $urn,
        string $time,
        string $clockSeq,
        int $variant,
        ?int $version
    ): void {
        $uuids = [
            Uuid::fromString($string),
            Uuid::fromString($curly),
            Uuid::fromString($hex),
            Uuid::fromBytes(base64_decode($bytes)),
            Uuid::fromString($urn),
            Uuid::fromInteger($int),
        ];

        /** @var UuidInterface $uuid */
        foreach ($uuids as $uuid) {
            $this->assertSame($string, $uuid->toString());
            $this->assertSame($hex, $uuid->getHex());
            $this->assertSame(base64_decode($bytes), $uuid->getBytes());
            $this->assertSame($int, $uuid->getInteger());
            $this->assertSame($fields, $uuid->getFieldsHex());
            $this->assertSame($fields['time_low'], $uuid->getTimeLowHex());
            $this->assertSame($fields['time_mid'], $uuid->getTimeMidHex());
            $this->assertSame($fields['time_hi_and_version'], $uuid->getTimeHiAndVersionHex());
            $this->assertSame($fields['clock_seq_hi_and_reserved'], $uuid->getClockSeqHiAndReservedHex());
            $this->assertSame($fields['clock_seq_low'], $uuid->getClockSeqLowHex());
            $this->assertSame($fields['node'], $uuid->getNodeHex());
            $this->assertSame($urn, $uuid->getUrn());
            if ($uuid->getVersion() === 1) {
                $this->assertSame($time, $uuid->getTimestampHex());
            }
            $this->assertSame($clockSeq, $uuid->getClockSequenceHex());
            $this->assertSame($variant, $uuid->getVariant());
            $this->assertSame($version, $uuid->getVersion());
        }
    }

    /**
     * Taken from the Python UUID tests in
     * http://hg.python.org/cpython/file/2f4c4db9aee5/Lib/test/test_uuid.py
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function providePythonTests(): array
    {
        // This array is taken directly from the Python tests, more or less.
        return [
            [
                'string' => '00000000-0000-0000-0000-000000000000',
                'curly' => '{00000000-0000-0000-0000-000000000000}',
                'hex' => '00000000000000000000000000000000',
                'bytes' => 'AAAAAAAAAAAAAAAAAAAAAA==',
                'int' => '0',
                'fields' => [
                    'time_low' => '00000000',
                    'time_mid' => '0000',
                    'time_hi_and_version' => '0000',
                    'clock_seq_hi_and_reserved' => '00',
                    'clock_seq_low' => '00',
                    'node' => '000000000000',
                ],
                'urn' => 'urn:uuid:00000000-0000-0000-0000-000000000000',
                'time' => '0',
                'clock_seq' => '0000',
                'variant' => Uuid::RESERVED_NCS,
                'version' => null,
            ],
            [
                'string' => '00010203-0405-0607-0809-0a0b0c0d0e0f',
                'curly' => '{00010203-0405-0607-0809-0a0b0c0d0e0f}',
                'hex' => '000102030405060708090a0b0c0d0e0f',
                'bytes' => 'AAECAwQFBgcICQoLDA0ODw==',
                'int' => '5233100606242806050955395731361295',
                'fields' => [
                    'time_low' => '00010203',
                    'time_mid' => '0405',
                    'time_hi_and_version' => '0607',
                    'clock_seq_hi_and_reserved' => '08',
                    'clock_seq_low' => '09',
                    'node' => '0a0b0c0d0e0f',
                ],
                'urn' => 'urn:uuid:00010203-0405-0607-0809-0a0b0c0d0e0f',
                'time' => '607040500010203',
                'clock_seq' => '0809',
                'variant' => Uuid::RESERVED_NCS,
                'version' => null,
            ],
            [
                'string' => '02d9e6d5-9467-382e-8f9b-9300a64ac3cd',
                'curly' => '{02d9e6d5-9467-382e-8f9b-9300a64ac3cd}',
                'hex' => '02d9e6d59467382e8f9b9300a64ac3cd',
                'bytes' => 'Atnm1ZRnOC6Pm5MApkrDzQ==',
                'int' => '3789866285607910888100818383505376205',
                'fields' => [
                    'time_low' => '02d9e6d5',
                    'time_mid' => '9467',
                    'time_hi_and_version' => '382e',
                    'clock_seq_hi_and_reserved' => '8f',
                    'clock_seq_low' => '9b',
                    'node' => '9300a64ac3cd',
                ],
                'urn' => 'urn:uuid:02d9e6d5-9467-382e-8f9b-9300a64ac3cd',
                'time' => '82e946702d9e6d5',
                'clock_seq' => '0f9b',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_HASH_MD5,
            ],
            [
                'string' => '12345678-1234-5678-1234-567812345678',
                'curly' => '{12345678-1234-5678-1234-567812345678}',
                'hex' => '12345678123456781234567812345678',
                'bytes' => 'EjRWeBI0VngSNFZ4EjRWeA==',
                'int' => '24197857161011715162171839636988778104',
                'fields' => [
                    'time_low' => '12345678',
                    'time_mid' => '1234',
                    'time_hi_and_version' => '5678',
                    'clock_seq_hi_and_reserved' => '12',
                    'clock_seq_low' => '34',
                    'node' => '567812345678',
                ],
                'urn' => 'urn:uuid:12345678-1234-5678-1234-567812345678',
                'time' => '678123412345678',
                'clock_seq' => '1234',
                'variant' => Uuid::RESERVED_NCS,
                'version' => null,
            ],
            [
                'string' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'curly' => '{6ba7b810-9dad-11d1-80b4-00c04fd430c8}',
                'hex' => '6ba7b8109dad11d180b400c04fd430c8',
                'bytes' => 'a6e4EJ2tEdGAtADAT9QwyA==',
                'int' => '143098242404177361603877621312831893704',
                'fields' => [
                    'time_low' => '6ba7b810',
                    'time_mid' => '9dad',
                    'time_hi_and_version' => '11d1',
                    'clock_seq_hi_and_reserved' => '80',
                    'clock_seq_low' => 'b4',
                    'node' => '00c04fd430c8',
                ],
                'urn' => 'urn:uuid:6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'time' => '1d19dad6ba7b810',
                'clock_seq' => '00b4',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => '6ba7b811-9dad-11d1-80b4-00c04fd430c8',
                'curly' => '{6ba7b811-9dad-11d1-80b4-00c04fd430c8}',
                'hex' => '6ba7b8119dad11d180b400c04fd430c8',
                'bytes' => 'a6e4EZ2tEdGAtADAT9QwyA==',
                'int' => '143098242483405524118141958906375844040',
                'fields' => [
                    'time_low' => '6ba7b811',
                    'time_mid' => '9dad',
                    'time_hi_and_version' => '11d1',
                    'clock_seq_hi_and_reserved' => '80',
                    'clock_seq_low' => 'b4',
                    'node' => '00c04fd430c8',
                ],
                'urn' => 'urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8',
                'time' => '1d19dad6ba7b811',
                'clock_seq' => '00b4',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => '6ba7b812-9dad-11d1-80b4-00c04fd430c8',
                'curly' => '{6ba7b812-9dad-11d1-80b4-00c04fd430c8}',
                'hex' => '6ba7b8129dad11d180b400c04fd430c8',
                'bytes' => 'a6e4Ep2tEdGAtADAT9QwyA==',
                'int' => '143098242562633686632406296499919794376',
                'fields' => [
                    'time_low' => '6ba7b812',
                    'time_mid' => '9dad',
                    'time_hi_and_version' => '11d1',
                    'clock_seq_hi_and_reserved' => '80',
                    'clock_seq_low' => 'b4',
                    'node' => '00c04fd430c8',
                ],
                'urn' => 'urn:uuid:6ba7b812-9dad-11d1-80b4-00c04fd430c8',
                'time' => '1d19dad6ba7b812',
                'clock_seq' => '00b4',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => '6ba7b814-9dad-11d1-80b4-00c04fd430c8',
                'curly' => '{6ba7b814-9dad-11d1-80b4-00c04fd430c8}',
                'hex' => '6ba7b8149dad11d180b400c04fd430c8',
                'bytes' => 'a6e4FJ2tEdGAtADAT9QwyA==',
                'int' => '143098242721090011660934971687007695048',
                'fields' => [
                    'time_low' => '6ba7b814',
                    'time_mid' => '9dad',
                    'time_hi_and_version' => '11d1',
                    'clock_seq_hi_and_reserved' => '80',
                    'clock_seq_low' => 'b4',
                    'node' => '00c04fd430c8',
                ],
                'urn' => 'urn:uuid:6ba7b814-9dad-11d1-80b4-00c04fd430c8',
                'time' => '1d19dad6ba7b814',
                'clock_seq' => '00b4',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => '7d444840-9dc0-11d1-b245-5ffdce74fad2',
                'curly' => '{7d444840-9dc0-11d1-b245-5ffdce74fad2}',
                'hex' => '7d4448409dc011d1b2455ffdce74fad2',
                'bytes' => 'fURIQJ3AEdGyRV/9znT60g==',
                'int' => '166508041112410060672666770310773930706',
                'fields' => [
                    'time_low' => '7d444840',
                    'time_mid' => '9dc0',
                    'time_hi_and_version' => '11d1',
                    'clock_seq_hi_and_reserved' => 'b2',
                    'clock_seq_low' => '45',
                    'node' => '5ffdce74fad2',
                ],
                'urn' => 'urn:uuid:7d444840-9dc0-11d1-b245-5ffdce74fad2',
                'time' => '1d19dc07d444840',
                'clock_seq' => '3245',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => 'e902893a-9d22-3c7e-a7b8-d6e313b71d9f',
                'curly' => '{e902893a-9d22-3c7e-a7b8-d6e313b71d9f}',
                'hex' => 'e902893a9d223c7ea7b8d6e313b71d9f',
                'bytes' => '6QKJOp0iPH6nuNbjE7cdnw==',
                'int' => '309723290945582129846206211755626405279',
                'fields' => [
                    'time_low' => 'e902893a',
                    'time_mid' => '9d22',
                    'time_hi_and_version' => '3c7e',
                    'clock_seq_hi_and_reserved' => 'a7',
                    'clock_seq_low' => 'b8',
                    'node' => 'd6e313b71d9f',
                ],
                'urn' => 'urn:uuid:e902893a-9d22-3c7e-a7b8-d6e313b71d9f',
                'time' => 'c7e9d22e902893a',
                'clock_seq' => '27b8',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_HASH_MD5,
            ],
            [
                'string' => 'eb424026-6f54-4ef8-a4d0-bb658a1fc6cf',
                'curly' => '{eb424026-6f54-4ef8-a4d0-bb658a1fc6cf}',
                'hex' => 'eb4240266f544ef8a4d0bb658a1fc6cf',
                'bytes' => '60JAJm9UTvik0Ltlih/Gzw==',
                'int' => '312712571721458096795100956955942831823',
                'fields' => [
                    'time_low' => 'eb424026',
                    'time_mid' => '6f54',
                    'time_hi_and_version' => '4ef8',
                    'clock_seq_hi_and_reserved' => 'a4',
                    'clock_seq_low' => 'd0',
                    'node' => 'bb658a1fc6cf',
                ],
                'urn' => 'urn:uuid:eb424026-6f54-4ef8-a4d0-bb658a1fc6cf',
                'time' => 'ef86f54eb424026',
                'clock_seq' => '24d0',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_RANDOM,
            ],
            [
                'string' => 'f81d4fae-7dec-11d0-a765-00a0c91e6bf6',
                'curly' => '{f81d4fae-7dec-11d0-a765-00a0c91e6bf6}',
                'hex' => 'f81d4fae7dec11d0a76500a0c91e6bf6',
                'bytes' => '+B1Prn3sEdCnZQCgyR5r9g==',
                'int' => '329800735698586629295641978511506172918',
                'fields' => [
                    'time_low' => 'f81d4fae',
                    'time_mid' => '7dec',
                    'time_hi_and_version' => '11d0',
                    'clock_seq_hi_and_reserved' => 'a7',
                    'clock_seq_low' => '65',
                    'node' => '00a0c91e6bf6',
                ],
                'urn' => 'urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6',
                'time' => '1d07decf81d4fae',
                'clock_seq' => '2765',
                'variant' => Uuid::RFC_4122,
                'version' => Uuid::UUID_TYPE_TIME,
            ],
            [
                'string' => 'fffefdfc-fffe-fffe-fffe-fffefdfcfbfa',
                'curly' => '{fffefdfc-fffe-fffe-fffe-fffefdfcfbfa}',
                'hex' => 'fffefdfcfffefffefffefffefdfcfbfa',
                'bytes' => '//79/P/+//7//v/+/fz7+g==',
                'int' => '340277133821575024845345576078114880506',
                'fields' => [
                    'time_low' => 'fffefdfc',
                    'time_mid' => 'fffe',
                    'time_hi_and_version' => 'fffe',
                    'clock_seq_hi_and_reserved' => 'ff',
                    'clock_seq_low' => 'fe',
                    'node' => 'fffefdfcfbfa',
                ],
                'urn' => 'urn:uuid:fffefdfc-fffe-fffe-fffe-fffefdfcfbfa',
                'time' => 'ffefffefffefdfc',
                'clock_seq' => '3ffe',
                'variant' => Uuid::RESERVED_FUTURE,
                'version' => null,
            ],
            [
                'string' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'curly' => '{ffffffff-ffff-ffff-ffff-ffffffffffff}',
                'hex' => 'ffffffffffffffffffffffffffffffff',
                'bytes' => '/////////////////////w==',
                'int' => '340282366920938463463374607431768211455',
                'fields' => [
                    'time_low' => 'ffffffff',
                    'time_mid' => 'ffff',
                    'time_hi_and_version' => 'ffff',
                    'clock_seq_hi_and_reserved' => 'ff',
                    'clock_seq_low' => 'ff',
                    'node' => 'ffffffffffff',
                ],
                'urn' => 'urn:uuid:ffffffff-ffff-ffff-ffff-ffffffffffff',
                'time' => 'fffffffffffffff',
                'clock_seq' => '3fff',
                'variant' => Uuid::RESERVED_FUTURE,
                'version' => null,
            ],
        ];
    }

    /**
     * @covers Ramsey\Uuid\Uuid::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $uuid = Uuid::uuid1();

        $this->assertEquals('"' . $uuid->toString() . '"', json_encode($uuid));
    }

    public function testSerialize(): void
    {
        $uuid = Uuid::uuid4();
        $serialized = serialize($uuid);
        $unserializedUuid = unserialize($serialized);
        $this->assertTrue($uuid->equals($unserializedUuid));
    }

    public function testUuid3WithEmptyNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID string:');

        $uuid = Uuid::uuid3('', '');
    }

    public function testUuid3WithEmptyName(): void
    {
        $uuid = Uuid::uuid3(Uuid::NIL, '');

        $this->assertEquals('4ae71336-e44b-39bf-b9d2-752e234818a5', $uuid->toString());
    }

    public function testUuid3WithZeroName(): void
    {
        $uuid = Uuid::uuid3(Uuid::NIL, '0');

        $this->assertEquals('19826852-5007-3022-a72a-212f66e9fac3', $uuid->toString());
    }

    public function testUuid5WithEmptyNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID string:');

        $uuid = Uuid::uuid5('', '');
    }

    public function testUuid5WithEmptyName(): void
    {
        $uuid = Uuid::uuid5(Uuid::NIL, '');

        $this->assertEquals('e129f27c-5103-5c5c-844b-cdf0a15e160d', $uuid->toString());
    }

    public function testUuid5WithZeroName(): void
    {
        $uuid = Uuid::uuid5(Uuid::NIL, '0');

        $this->assertEquals('b6c54489-38a0-5f50-a60a-fd8d76219cae', $uuid->toString());
    }

    /**
     * @depends testGetVersionForVersion1
     */
    public function testUuidVersionConstantForVersion1(): void
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals($uuid->getVersion(), Uuid::UUID_TYPE_TIME);
    }

    /**
     * @depends testGetVersionForVersion2
     */
    public function testUuidVersionConstantForVersion2(): void
    {
        $uuid = Uuid::fromString('6fa459ea-ee8a-2ca4-894e-db77e160355e');
        $this->assertEquals($uuid->getVersion(), Uuid::UUID_TYPE_IDENTIFIER);
    }

    /**
     * @depends testGetVersionForVersion3
     */
    public function testUuidVersionConstantForVersion3(): void
    {
        $uuid = Uuid::fromString('6fa459ea-ee8a-3ca4-894e-db77e160355e');
        $this->assertEquals($uuid->getVersion(), Uuid::UUID_TYPE_HASH_MD5);
    }

    /**
     * @depends testGetVersionForVersion4
     */
    public function testUuidVersionConstantForVersion4(): void
    {
        $uuid = Uuid::fromString('6fabf0bc-603a-42f2-925b-d9f779bd0032');
        $this->assertEquals($uuid->getVersion(), Uuid::UUID_TYPE_RANDOM);
    }

    /**
     * @depends testGetVersionForVersion5
     */
    public function testUuidVersionConstantForVersion5(): void
    {
        $uuid = Uuid::fromString('886313e1-3b8a-5372-9b90-0c9aee199e5d');
        $this->assertEquals($uuid->getVersion(), Uuid::UUID_TYPE_HASH_SHA1);
    }

    public function testGetDateTimeThrowsExceptionWhenDateTimeCannotParseString(): void
    {
        $numberConverter = new BigNumberConverter();
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $timeConverter
            ->shouldReceive('convertTime')
            ->once()
            ->andReturn('foobar');

        $builder = new DefaultUuidBuilder($numberConverter, $timeConverter);
        $codec = new StringCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString('b1484596-25dc-11ea-978f-2e728ce88125');

        $this->expectException(DateTimeException::class);
        $this->expectExceptionMessage(
            'DateTimeImmutable::__construct(): Failed to parse time string '
            . '(@foobar) at position 0 (@): Unexpected character'
        );

        $uuid->getDateTime();
    }
}
