<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use AspectMock\Test as AspectMock;
use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\BinaryUtils;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Time;

class DefaultTimeGeneratorTest extends TestCase
{
    /**
     * @var TimeProviderInterface & MockInterface
     */
    private $timeProvider;

    /**
     * @var NodeProviderInterface & MockObject
     */
    private $nodeProvider;

    /**
     * @var TimeConverterInterface & MockObject
     */
    private $timeConverter;

    /**
     * @var string
     */
    private $nodeId = '122f80ca9e06';

    /**
     * @var int[]
     */
    private $currentTime;

    /**
     * @var string[]
     */
    private $calculatedTime;

    /**
     * @var int
     */
    private $clockSeq = 4066;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nodeProvider = $this->getMockBuilder(NodeProviderInterface::class)->getMock();
        $this->timeConverter = $this->getMockBuilder(TimeConverterInterface::class)->getMock();
        $this->currentTime = ['sec' => 1458733431, 'usec' => 877449];
        $this->calculatedTime = ['low' => '83cb98e0', 'mid' => '98e0', 'hi' => '03cb'];

        $time = new Time($this->currentTime['sec'], $this->currentTime['usec']);
        $this->timeProvider = Mockery::mock(TimeProviderInterface::class, [
            'getTime' => $time,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->timeProvider, $this->nodeProvider, $this->timeConverter);
        Mockery::close();
        AspectMock::clean();
    }

    public function testGenerateUsesNodeProviderWhenNodeIsNull(): void
    {
        $this->nodeProvider->expects($this->once())
            ->method('getNode')
            ->willReturn('122f80ca9e06');
        $this->timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate(null, $this->clockSeq);
    }

    public function testGenerateUsesTimeProvidersCurrentTime(): void
    {
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

    public function testGenerateCalculatesTimeWithConverter(): void
    {
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
    public function testGenerateAppliesVersionAndVariant(): void
    {
        $expectedBytes = hex2bin('83cb98e098e003cb8fe2122f80ca9e06');

        $this->timeConverter->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:' . BinaryUtils::class);
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
    public function testGenerateReturnsBinaryStringInUuidFormat(): void
    {
        $this->timeConverter->method('calculateTime')->willReturn($this->calculatedTime);
        $binaryUtils = Mockery::mock('alias:' . BinaryUtils::class);
        $binaryUtils->shouldReceive('applyVersion')->andReturn(971);
        $binaryUtils->shouldReceive('applyVariant')->andReturn(143);

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $result = $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);

        // Given we use values:
        // $low = '83cb98e0';
        // $mid = '98e0';
        // $timeHi = 971;
        // $clockSeqHi = 143;
        // $clockSeq = 4066;
        // $node = '122f80ca9e06';
        //
        // $values = [
        //     $low,
        //     $mid,
        //     sprintf('%04x', $timeHi),
        //     sprintf('%02x', $clockSeqHi),
        //     sprintf('%02x', $clockSeq & 0xff),
        //     $node
        // ];
        //
        // then:
        // $hex = vsprintf('%08s%04s%04s%02s%02s%012s', $values);

        $hex = '83cb98e098e003cb8fe2122f80ca9e06';
        $binary = hex2bin($hex);
        $this->assertEquals($binary, $result);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateUsesRandomSequenceWhenClockSeqNull(): void
    {
        $randomInt = AspectMock::func('Ramsey\Uuid\Generator', 'random_int', 9622);
        $this->timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId);
        $randomInt->verifyInvokedOnce([0, 0x3fff]);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateThrowsExceptionWhenExceptionThrownByRandomint(): void
    {
        AspectMock::func('Ramsey\Uuid\Generator', 'random_int', function (): void {
            throw new Exception('Could not gather sufficient random data');
        });

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $defaultTimeGenerator->generate($this->nodeId);
    }
}
