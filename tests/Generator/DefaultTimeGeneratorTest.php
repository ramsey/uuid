<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\BinaryUtils;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Exception\TimeSourceException;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Generator\DefaultTimeGenerator;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\Time\FixedTimeProvider;
use Ramsey\Uuid\Provider\TimeProviderInterface;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;
use phpmock\mockery\PHPMockery;

use function hex2bin;

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
     * @var Hexadecimal
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
        $this->calculatedTime = new Hexadecimal('03cb98e083cb98e0');

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
    }

    public function testGenerateUsesNodeProviderWhenNodeIsNull(): void
    {
        $this->nodeProvider->expects($this->once())
            ->method('getNode')
            ->willReturn(new Hexadecimal('122f80ca9e06'));
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
    public function testGenerateDoesNotApplyVersionAndVariant(): void
    {
        $expectedBytes = hex2bin('83cb98e098e003cb0fe2122f80ca9e06');

        $this->timeConverter->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);

        $binaryUtils = Mockery::mock('alias:' . BinaryUtils::class);
        $binaryUtils->shouldNotReceive('applyVersion');
        $binaryUtils->shouldNotReceive('applyVariant');

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
    public function testGenerateUsesRandomSequenceWhenClockSeqNull(): void
    {
        PHPMockery::mock('Ramsey\Uuid\Generator', 'random_int')
            ->once()
            ->with(0, 0x3fff)
            ->andReturn(9622);
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
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateThrowsExceptionWhenExceptionThrownByRandomint(): void
    {
        PHPMockery::mock('Ramsey\Uuid\Generator', 'random_int')
            ->once()
            ->andThrow(new Exception('Could not gather sufficient random data'));

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider
        );

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $defaultTimeGenerator->generate($this->nodeId);
    }

    public function testDefaultTimeGeneratorThrowsExceptionForLargeGeneratedValue(): void
    {
        $timeProvider = new FixedTimeProvider(new Time('1832455114570', '955162'));
        $featureSet = new FeatureSet();
        $timeGenerator = new DefaultTimeGenerator(
            $featureSet->getNodeProvider(),
            $featureSet->getTimeConverter(),
            $timeProvider
        );

        $this->expectException(TimeSourceException::class);
        $this->expectExceptionMessage(
            'The generated time of \'10000000000000004\' is larger than expected'
        );

        $timeGenerator->generate();
    }
}
