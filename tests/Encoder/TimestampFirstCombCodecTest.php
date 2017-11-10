<?php
namespace Ramsey\Uuid\Test\Encoder;

use PHPUnit_Framework_MockObject_MockObject;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Test\TestCase;

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

    protected function setUp()
    {
        $this->builderMock = $this->getMockBuilder('Ramsey\Uuid\Builder\UuidBuilderInterface')->getMock();
        $this->codec = new TimestampFirstCombCodec($this->builderMock);
    }

    public function testEncoding()
    {
        $uuidMock = $this->getMockBuilder('Ramsey\Uuid\UuidInterface')->getMock();
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(array('ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66'));
        $encodedUuid = $this->codec->encode($uuidMock);

        $this->assertSame('0800200c-9a66-11e1-9b21-ff6f8cb0c57d', $encodedUuid);
    }

    public function testBinaryEncoding()
    {
        $uuidMock = $this->getMockBuilder('Ramsey\Uuid\UuidInterface')->getMock();
        $uuidMock->expects($this->any())
            ->method('getFieldsHex')
            ->willReturn(array('ff6f8cb0', 'c57d', '11e1', '9b', '21', '0800200c9a66'));
        $encodedUuid = $this->codec->encodeBinary($uuidMock);

        $this->assertSame(hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'), $encodedUuid);
    }

    public function testDecoding()
    {
        $this->builderMock->expects($this->exactly(1))
            ->method('build')
            ->with(
                $this->codec,
                array(
                    'time_low' => 'ff6f8cb0',
                    'time_mid' => 'c57d',
                    'time_hi_and_version' => '11e1',
                    'clock_seq_hi_and_reserved' => '9b',
                    'clock_seq_low' => '21',
                    'node' => '0800200c9a66'
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
                array(
                    'time_low' => 'ff6f8cb0',
                    'time_mid' => 'c57d',
                    'time_hi_and_version' => '11e1',
                    'clock_seq_hi_and_reserved' => '9b',
                    'clock_seq_low' => '21',
                    'node' => '0800200c9a66'
                )
            );
        $this->codec->decodeBytes(hex2bin('0800200c9a6611e19b21ff6f8cb0c57d'));
    }
}
