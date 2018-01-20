<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\BinaryUtils;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Mockery;
use AspectMock\Test as AspectMock;

class DefaultTimeGeneratorTest extends TestCase
{
    /** @var  TimeProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $timeProvider;
    /** @var  NodeProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $nodeProvider;
    /** @var  TimeConverterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $timeConverter;
    /** @var string */
    private $nodeId = '122f80ca9e06';
    /** @var int[] */
    private $currentTime;
    /** @var string[] */
    private $calculatedTime;
    /** @var int */
    private $clockSeq = 4066;

    protected function setUp()
    {
        parent::setUp();
        $this->timeProvider = $this->getMockBuilder(TimeProviderInterface::class)->getMock();
        $this->nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $this->timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();
        $this->currentTime = ["sec" => 1458733431, "usec" => 877449];
        $this->calculatedTime = ["low" => "83cb98e0", "mid" => "98e0", "hi" => "03cb"];
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->timeProvider = null;
        $this->nodeProvider = null;
        $this->timeConverter = null;
        Mockery::close();
        if (!self::isHhvm()) {
            AspectMock::clean();
        }
    }

    public function testGenerateUsesNodeProviderWhenNodeIsNull()
    {
        $this->nodeProvider->expects($this->once())
            ->method('getNode')
            ->willReturn('122f80ca9e06');
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate(null, $this->clockSeq);
    }

    public function testGenerateUsesTimeProvidersCurrentTime()
    {
        $this->timeProvider->expects($this->once())
            ->method('currentTime')
            ->willReturn($this->currentTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
    }

    public function testGenerateCalculatesTimeWithConverter()
    {
        $this->timeProvider->method('currentTime')
            ->willReturn($this->currentTime);
        $this->timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateAppliesVersionAndVariant()
    {
        $expectedBytes = hex2bin('83cb98e098e003cb8fe2122f80ca9e06');

        $this->timeProvider->method('currentTime')
            ->willReturn($this->currentTime);
        $this->timeConverter->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:'.BinaryUtils::class);
        $binaryUtils->shouldReceive('applyVersion')
            ->with($this->calculatedTime['hi'], 1)
            ->andReturn(971);
        $clockSeqShifted = 15;
        $binaryUtils->shouldReceive('applyVariant')
            ->with($clockSeqShifted)
            ->andReturn(143);

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );

        $this->assertSame($expectedBytes, $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateReturnsBinaryStringInUuidFormat()
    {
        $this->timeProvider->method('currentTime')->willReturn($this->currentTime);
        $this->timeConverter->method('calculateTime')->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:'.BinaryUtils::class);
        $binaryUtils->shouldReceive('applyVersion')->andReturn(971);
        $binaryUtils->shouldReceive('applyVariant')->andReturn(143);

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $result = $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
        /**
         * // Given we use values:
         * $low = '83cb98e0';
         * $mid = '98e0';
         * $timeHi = 971;
         * $clockSeqHi = 143;
         * $clockSeq = 4066;
         * $node = '122f80ca9e06';
         *
         * $values = [$low, $mid,
         * sprintf('%04x', $timeHi), sprintf('%02x', $clockSeqHi),
         * sprintf('%02x', $clockSeq & 0xff), $node];
         *
         * // then:
         * $hex = vsprintf('%08s%04s%04s%02s%02s%012s', $values);
         */
        $hex = '83cb98e098e003cb8fe2122f80ca9e06';
        $binary = hex2bin($hex);
        $this->assertEquals($binary, $result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateUsesRandomSequenceWhenClockSeqNull()
    {
        $expectedBytes = hex2bin('0000000000001000a596122f80ca9e06');

        $this->skipIfHhvm();
        $mt_rand = AspectMock::func('Ramsey\Uuid\Generator', 'random_int', 9622);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );

        $this->assertSame($expectedBytes, $defaultTimeGenerator->generate($this->nodeId));
        $mt_rand->verifyInvokedOnce([0, 0x3fff]);
    }
}
