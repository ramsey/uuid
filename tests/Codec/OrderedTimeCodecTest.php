<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Codec;

use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\Fields as NonstandardFields;
use Ramsey\Uuid\Nonstandard\UuidBuilder;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

use function hex2bin;
use function pack;
use function serialize;
use function str_replace;
use function unserialize;

class OrderedTimeCodecTest extends TestCase
{
    private UuidBuilderInterface & MockObject $builder;
    private UuidInterface & MockObject $uuid;
    private Fields $fields;

    /**
     * @var non-empty-string
     */
    private string $uuidString = '58e0a7d7-eebc-11d8-9669-0800200c9a66';

    /**
     * @var non-empty-string
     */
    private string $optimizedHex = '11d8eebc58e0a7d796690800200c9a66';

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->uuid = $this->getMockBuilder(UuidInterface::class)->getMock();

        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin('58e0a7d7eebc11d896690800200c9a66');

        $this->fields = new Fields($bytes);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->builder, $this->uuid, $this->fields);
    }

    public function testEncodeUsesFieldsArray(): void
    {
        $this->uuid->expects($this->once())
            ->method('getFields')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString(): void
    {
        $this->uuid->method('getFields')
            ->willReturn($this->fields);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertSame($this->uuidString, $result);
    }

    public function testEncodeBinary(): void
    {
        $expected = hex2bin($this->optimizedHex);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new Rfc4122UuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString($this->uuidString);

        $this->assertSame($expected, $codec->encodeBinary($uuid));
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters(): void
    {
        $string = '61';
        $codec = new OrderedTimeCodec($this->builder);

        /** @var non-empty-string $bytes */
        $bytes = pack('H*', $string);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeReturnsUuidFromBuilder(): void
    {
        $string = 'uuid:58e0a7d7-eebc-11d8-9669-0800200c9a66';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new OrderedTimeCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertSame($this->uuid, $result);
    }

    public function testDecodeBytesRearrangesFields(): void
    {
        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin($this->optimizedHex);

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new Rfc4122UuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $expectedUuid = $factory->fromString($this->uuidString);
        $uuidReturned = $codec->decodeBytes($bytes);

        $this->assertTrue($uuidReturned->equals($expectedUuid));
    }

    public function testEncodeBinaryThrowsExceptionForNonRfc4122Uuid(): void
    {
        $nonRfc4122Uuid = '58e0a7d7-eebc-11d8-d669-0800200c9a66';

        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin(str_replace('-', '', $nonRfc4122Uuid));

        $fields = new NonstandardFields($bytes);
        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new Rfc4122UuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $uuid = Mockery::mock(UuidInterface::class, [
            'getVariant' => 0,
            'toString' => $nonRfc4122Uuid,
            'getFields' => $fields,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected RFC 4122 version 1 (time-based) UUID'
        );

        $codec->encodeBinary($uuid);
    }

    public function testEncodeBinaryThrowsExceptionForNonTimeBasedUuid(): void
    {
        $nonTimeBasedUuid = '58e0a7d7-eebc-41d8-9669-0800200c9a66';

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new Rfc4122UuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $uuid = $factory->fromString($nonTimeBasedUuid);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected RFC 4122 version 1 (time-based) UUID'
        );

        $codec->encodeBinary($uuid);
    }

    public function testDecodeBytesThrowsExceptionsForNonRfc4122Uuid(): void
    {
        $nonRfc4122OptimizedHex = '11d8eebc58e0a7d716690800200c9a66';

        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin($nonRfc4122OptimizedHex);

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $builder = new UuidBuilder($numberConverter, $timeConverter);

        $codec = new OrderedTimeCodec($builder);

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage(
            'Attempting to decode a non-time-based UUID using OrderedTimeCodec'
        );

        $codec->decodeBytes($bytes);
    }

    public function testDecodeBytesThrowsExceptionsForNonTimeBasedUuid(): void
    {
        $nonTimeBasedOptimizedHex = '41d8eebc58e0a7d796690800200c9a66';

        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin($nonTimeBasedOptimizedHex);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);
        $builder = new Rfc4122UuidBuilder($numberConverter, $timeConverter);
        $codec = new OrderedTimeCodec($builder);

        $factory = new UuidFactory();
        $factory->setCodec($codec);

        $this->expectException(UnsupportedOperationException::class);
        $this->expectExceptionMessage(
            'Attempting to decode a non-time-based UUID using OrderedTimeCodec'
        );

        $codec->decodeBytes($bytes);
    }

    public function testSerializationDoesNotUseOrderedTimeCodec(): void
    {
        $expected = '9ec692cc-67c8-11eb-ae93-0242ac130002';

        $codec = new OrderedTimeCodec(
            (new UuidFactory())->getUuidBuilder()
        );
        $decoded = $codec->decode($expected);
        $serialized = serialize($decoded);

        /** @var \Ramsey\Uuid\Rfc4122\UuidInterface $unserializedUuid */
        $unserializedUuid = unserialize($serialized);

        /** @var \Ramsey\Uuid\Rfc4122\UuidInterface $expectedUuid */
        $expectedUuid = Uuid::fromString($expected);

        $this->assertSame($expectedUuid->getFields()->getVersion(), $unserializedUuid->getFields()->getVersion());
        $this->assertTrue($expectedUuid->equals($unserializedUuid));
    }
}
