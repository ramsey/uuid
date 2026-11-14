<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\MockObject\Stub;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
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
    private TimeProviderInterface & MockInterface $timeProvider;
    private NodeProviderInterface & Stub $nodeProvider;
    private TimeConverterInterface & Stub $timeConverter;

    /**
     * @var non-empty-string
     */
    private string $nodeId = '122f80ca9e06';

    /**
     * @var int[]
     */
    private array $currentTime;

    private Hexadecimal $calculatedTime;

    /**
     * @var int<0, 16383>
     */
    private int $clockSeq = 4066;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nodeProvider = $this->createStub(NodeProviderInterface::class);
        $this->timeConverter = $this->createStub(TimeConverterInterface::class);
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
        $nodeProvider = $this->createMock(NodeProviderInterface::class);
        $timeConverter = $this->createMock(TimeConverterInterface::class);
        $nodeProvider->expects($this->once())
            ->method('getNode')
            ->willReturn(new Hexadecimal('122f80ca9e06'));
        $timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $nodeProvider,
            $timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate(null, $this->clockSeq);
    }

    public function testGenerateUsesTimeProvidersCurrentTime(): void
    {
        $timeConverter = $this->createMock(TimeConverterInterface::class);
        $timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
    }

    public function testGenerateCalculatesTimeWithConverter(): void
    {
        $timeConverter = $this->createMock(TimeConverterInterface::class);
        $timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq);
    }

    public function testGenerateDoesNotApplyVersionAndVariant(): void
    {
        $expectedBytes = hex2bin('83cb98e098e003cb0fe2122f80ca9e06');

        $timeConverter = $this->createMock(TimeConverterInterface::class);
        $timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);

        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $timeConverter,
            $this->timeProvider
        );

        $this->assertSame($expectedBytes, $defaultTimeGenerator->generate($this->nodeId, $this->clockSeq));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateUsesRandomSequenceWhenClockSeqNull(): void
    {
        $timeConverter = $this->createMock(TimeConverterInterface::class);
        PHPMockery::mock('Ramsey\Uuid\Generator', 'random_int')
            ->once()
            ->with(0, 0x3fff)
            ->andReturn(9622);
        $timeConverter->expects($this->once())
            ->method('calculateTime')
            ->with($this->currentTime['sec'], $this->currentTime['usec'])
            ->willReturn($this->calculatedTime);
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $timeConverter,
            $this->timeProvider
        );
        $defaultTimeGenerator->generate($this->nodeId);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
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
        $timeConverter = Mockery::mock(TimeConverterInterface::class, [
            'calculateTime' => new Hexadecimal('10000000000000004'),
        ]);
        $timeGenerator = new DefaultTimeGenerator(
            $featureSet->getNodeProvider(),
            $timeConverter,
            $timeProvider
        );

        $this->expectException(TimeSourceException::class);
        $this->expectExceptionMessage(
            'The generated time of \'10000000000000004\' is larger than expected'
        );

        $timeGenerator->generate();
    }

    public function testGenerateThrowsExceptionForNodeWithTrailingNewline(): void
    {
        $defaultTimeGenerator = new DefaultTimeGenerator(
            $this->nodeProvider,
            $this->timeConverter,
            $this->timeProvider,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid node value');

        $defaultTimeGenerator->generate("0123456789a\n");
    }
}
