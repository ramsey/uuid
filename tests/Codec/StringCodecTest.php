<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Codec;

use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

use function hex2bin;
use function implode;
use function pack;

class StringCodecTest extends TestCase
{
    /**
     * @var UuidBuilderInterface & MockObject
     */
    private $builder;

    /**
     * @var UuidInterface & MockObject
     */
    private $uuid;

    /**
     * @var Fields
     */
    private $fields;

    /**
     * @var string
     */
    private $uuidString = '12345678-1234-4bcd-abef-1234abcd4321';

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->uuid = $this->getMockBuilder(UuidInterface::class)->getMock();
        $this->fields = new Fields((string) hex2bin('1234567812344bcdabef1234abcd4321'));
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
        $codec = new StringCodec($this->builder);
        $codec->encode($this->uuid);
    }

    public function testEncodeReturnsFormattedString(): void
    {
        $this->uuid->method('getFields')
            ->willReturn($this->fields);
        $codec = new StringCodec($this->builder);
        $result = $codec->encode($this->uuid);
        $this->assertSame($this->uuidString, $result);
    }

    public function testEncodeBinaryReturnsBinaryString(): void
    {
        $expected = hex2bin('123456781234abcdabef1234abcd4321');

        $fields = Mockery::mock(FieldsInterface::class, [
            'getBytes' => hex2bin('123456781234abcdabef1234abcd4321'),
        ]);

        $this->uuid->method('getFields')->willReturn($fields);

        $codec = new StringCodec($this->builder);
        $result = $codec->encodeBinary($this->uuid);
        $this->assertSame($expected, $result);
    }

    public function testDecodeUsesBuilderOnFields(): void
    {
        $fields = [
            'time_low' => $this->fields->getTimeLow()->toString(),
            'time_mid' => $this->fields->getTimeMid()->toString(),
            'time_hi_and_version' => $this->fields->getTimeHiAndVersion()->toString(),
            'clock_seq_hi_and_reserved' => $this->fields->getClockSeqHiAndReserved()->toString(),
            'clock_seq_low' => $this->fields->getClockSeqLow()->toString(),
            'node' => $this->fields->getNode()->toString(),
        ];

        $bytes = hex2bin(implode('', $fields));

        $string = 'uuid:12345678-1234-4bcd-abef-1234abcd4321';
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf(StringCodec::class), $bytes);
        $codec = new StringCodec($this->builder);
        $codec->decode($string);
    }

    public function testDecodeThrowsExceptionOnInvalidUuid(): void
    {
        $string = 'invalid-uuid';
        $codec = new StringCodec($this->builder);

        $this->expectException(InvalidArgumentException::class);
        $codec->decode($string);
    }

    public function testDecodeReturnsUuidFromBuilder(): void
    {
        $string = 'uuid:12345678-1234-abcd-abef-1234abcd4321';
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $codec = new StringCodec($this->builder);
        $result = $codec->decode($string);
        $this->assertSame($this->uuid, $result);
    }

    public function testDecodeBytesThrowsExceptionWhenBytesStringNotSixteenCharacters(): void
    {
        $string = '61';
        $bytes = pack('H*', $string);
        $codec = new StringCodec($this->builder);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$bytes string should contain 16 characters.');
        $codec->decodeBytes($bytes);
    }

    public function testDecodeBytesReturnsUuid(): void
    {
        $string = '123456781234abcdabef1234abcd4321';
        $bytes = pack('H*', $string);
        $codec = new StringCodec($this->builder);
        $this->builder->method('build')
            ->willReturn($this->uuid);
        $result = $codec->decodeBytes($bytes);
        $this->assertSame($this->uuid, $result);
    }

    /**
     * @dataProvider provideAcceptedStrings
     */
    public function testDecodeAcceptsStandardFormsOfUuidString(string $string): void
    {
        $this->builder->expects($this->once())
            ->method('build')
            ->with($this->anything(), hex2bin('123456781234abcdabef1234abcd4321'))
            ->willReturn($this->uuid);

        $codec = new StringCodec($this->builder);

        $this->assertSame($this->uuid, $codec->decode($string));
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideAcceptedStrings(): array
    {
        return [
            'hyphens' => ['12345678-1234-abcd-abef-1234abcd4321'],
            'no hyphens' => ['123456781234abcdabef1234abcd4321'],
            'uppercase' => ['12345678-1234-ABCD-ABEF-1234ABCD4321'],
            'urn:uuid: prefix' => ['urn:uuid:12345678-1234-abcd-abef-1234abcd4321'],
            'uppercase prefix' => ['URN:UUID:12345678-1234-abcd-abef-1234abcd4321'],
            'uuid: prefix' => ['uuid:12345678-1234-abcd-abef-1234abcd4321'],
            'urn: prefix' => ['urn:12345678-1234-abcd-abef-1234abcd4321'],
            'braces' => ['{12345678-1234-abcd-abef-1234abcd4321}'],
            'braces without hyphens' => ['{123456781234abcdabef1234abcd4321}'],
            'prefix and braces' => ['urn:uuid:{12345678-1234-abcd-abef-1234abcd4321}'],
        ];
    }

    /**
     * @dataProvider provideRefusedStrings
     */
    public function testDecodeThrowsExceptionForNonstandardFormsOfUuidString(string $string): void
    {
        $this->builder->expects($this->never())->method('build');

        $codec = new StringCodec($this->builder);

        $this->expectException(InvalidUuidStringException::class);
        $this->expectExceptionMessage('Invalid UUID string: ' . $string);

        $codec->decode($string);
    }

    /**
     * @return array<string, array{string}>
     */
    public function provideRefusedStrings(): array
    {
        return [
            'empty' => [''],
            'hyphens in other positions' => ['1-2-3-4-5-6-7-8-1234abcdabef1234abcd4321'],
            'some hyphens missing' => ['12345678-1234abcd-abef-1234abcd4321'],
            'extra hyphen' => ['12345678-1234-abcd-abef-1234abcd4321-'],
            'repeated prefix' => ['urn:uuid:urn:uuid:12345678-1234-abcd-abef-1234abcd4321'],
            'prefixes in reverse order' => ['uuid:urn:12345678-1234-abcd-abef-1234abcd4321'],
            'prefix after the UUID' => ['12345678-1234-abcd-abef-1234abcd4321uuid:'],
            'prefix inside braces' => ['{urn:uuid:12345678-1234-abcd-abef-1234abcd4321}'],
            'two pairs of braces' => ['{{12345678-1234-abcd-abef-1234abcd4321}}'],
            'opening brace only' => ['{12345678-1234-abcd-abef-1234abcd4321'],
            'closing brace only' => ['12345678-1234-abcd-abef-1234abcd4321}'],
            'brace inside the UUID' => ['12345678-{1234}-abcd-abef-1234abcd4321'],
            'trailing newline' => ["12345678-1234-abcd-abef-1234abcd4321\n"],
            'too few digits' => ['12345678-1234-abcd-abef-1234abcd432'],
            'too many digits' => ['12345678-1234-abcd-abef-1234abcd43210'],
            'not hexadecimal' => ['12345678-1234-abcd-abef-1234abcd432g'],
        ];
    }
}
