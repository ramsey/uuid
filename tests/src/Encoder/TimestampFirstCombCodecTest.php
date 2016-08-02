<?php
namespace Ramsey\Uuid\Test\Encoder;

use PHPUnit_Framework_MockObject_MockObject;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\UuidFields;
use Ramsey\Uuid\UuidInterface;

class TimestampFirstCombCodecTest extends TestCase
{
    /**
     * @var CodecInterface
     */
    private $codec;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $builderMock;

    public function setUp()
    {
        $this->builderMock = $this->createMock(UuidBuilderInterface::class);
        $this->codec = new TimestampFirstCombCodec($this->builderMock);
    }

    public function testEncoding()
    {
        $uuidMock = $this->createMock(UuidInterface::class);
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(new UuidFields('ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66'));
        $encodedUuid = $this->codec->encode($uuidMock);

        $this->assertSame('0800200c-9a66-11e1-9b21-ff6f8cb0c57d', $encodedUuid);
    }

    public function testBinaryEncoding()
    {
        $uuidMock = $this->createMock(UuidInterface::class);
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(new UuidFields('ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66'));
        $encodedUuid = $this->codec->encodeBinary($uuidMock);

        $this->assertSame(hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'), $encodedUuid);
    }

    public function testDecoding()
    {
        $this->builderMock->expects($this->exactly(1))
            ->method('build')
            ->with(
                $this->codec,
                new UuidFields(
                    'ff6f8cb0',
                    'c57d',
                    '11e1',
                    '9b',
                    '21',
                    '0800200c9a66'
                )
            );
        $this->codec->decode('0800200c-9a66-11e1-9b21-ff6f8cb0c57d');
    }

    public function testBinaryDecoding()
    {
        $this->builderMock->expects($this->exactly(1))
            ->method('build')
            ->with(
                $this->codec,
                new UuidFields(
                    'ff6f8cb0',
                    'c57d',
                    '11e1',
                    '9b',
                    '21',
                    '0800200c9a66'
                )
            );
        $this->codec->decodeBytes(hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'));
    }
}
