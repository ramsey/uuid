<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Encoder;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidInterface;

class TimestampFirstCombCodecTest extends TestCase
{
    /**
     * @var CodecInterface
     */
    private $codec;

    /**
     * @var MockObject & UuidBuilderInterface
     */
    private $builderMock;

    protected function setUp(): void
    {
        $this->builderMock = $this->getMockBuilder(UuidBuilderInterface::class)->getMock();
        $this->codec = new TimestampFirstCombCodec($this->builderMock);
    }

    public function testEncoding(): void
    {
        /** @var MockObject & UuidInterface $uuidMock */
        $uuidMock = $this->getMockBuilder(UuidInterface::class)->getMock();
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(['ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66']);
        $encodedUuid = $this->codec->encode($uuidMock);

        $this->assertSame('0800200c-9a66-11e1-9b21-ff6f8cb0c57d', $encodedUuid);
    }

    public function testBinaryEncoding(): void
    {
        /** @var MockObject & UuidInterface $uuidMock */
        $uuidMock = $this->getMockBuilder(UuidInterface::class)->getMock();
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(['ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66']);
        $encodedUuid = $this->codec->encodeBinary($uuidMock);

        $this->assertSame(hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'), $encodedUuid);
    }

    public function testDecoding(): void
    {
        $this->builderMock->expects($this->exactly(1))
            ->method('build')
            ->with(
                $this->codec,
                [
                    'time_low' => 'ff6f8cb0',
                    'time_mid' => 'c57d',
                    'time_hi_and_version' => '11e1',
                    'clock_seq_hi_and_reserved' => '9b',
                    'clock_seq_low' => '21',
                    'node' => '0800200c9a66',
                ]
            );
        $this->codec->decode('0800200c-9a66-11e1-9b21-ff6f8cb0c57d');
    }

    public function testBinaryDecoding(): void
    {
        $this->builderMock->expects($this->exactly(1))
            ->method('build')
            ->with(
                $this->codec,
                [
                    'time_low' => 'ff6f8cb0',
                    'time_mid' => 'c57d',
                    'time_hi_and_version' => '11e1',
                    'clock_seq_hi_and_reserved' => '9b',
                    'clock_seq_low' => '21',
                    'node' => '0800200c9a66',
                ]
            );
        $this->codec->decodeBytes((string) hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'));
    }
}
