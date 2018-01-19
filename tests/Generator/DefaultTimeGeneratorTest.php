<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Mockery;
use AspectMock\Test as AspectMock;

class DefaultTimeGeneratorTest extends TestCase
{
    /** @var  TimeProviderInterface */
    private $timeProvider;
    /** @var  NodeProviderInterface */
    private $nodeProvider;
    /** @var  TimeConverterInterface */
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
        $this->timeProvider = $this->getMockBuilder('Ramsey\Uuid\Provider\TimeProviderInterface')->getMock();
        $this->nodeProvider = $this->getMockBuilder('Ramsey\Uuid\Provider\NodeProviderInterface')->getMock();
        $this->timeConverter = $this->getMockBuilder('Ramsey\Uuid\Converter\TimeConverterInterface')->getMock();
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
        $this->timeProvider->method('currentTime')
            ->willReturn($this->currentTime);
        $this->timeConverter->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:Ramsey\Uuid\BinaryUtils');
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
        $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateReturnsBinaryStringInUuidFormat()
    {
        $this->timeProvider->method('currentTime')->willReturn($this->currentTime);
        $this->timeConverter->method('calculateTime')->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:Ramsey\Uuid\BinaryUtils');
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
        $this->skipIfHhvm();
        $mt_rand = AspectMock::func('Ramsey\Uuid\Generator', 'random_int', 9622);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId);
        $mt_rand->verifyInvokedOnce([0, 0x3fff]);
    }
}
