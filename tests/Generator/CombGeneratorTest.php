<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Test\TestCase;

use function bin2hex;
use function dechex;
use function hex2bin;
use function str_pad;

use const STR_PAD_LEFT;

class CombGeneratorTest extends TestCase
{
    private int $timestampBytes = 6;

    public function testGenerateUsesRandomGeneratorWithLengthMinusTimestampBytes(): void
    {
        $length = 10;
        $expectedLength = $length - $this->timestampBytes;

        /** @var MockObject & RandomGeneratorInterface $randomGenerator */
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $randomGenerator->expects($this->once())
            ->method('generate')
            ->with($expectedLength);

        /** @var MockObject & NumberConverterInterface $converter */
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();

        $generator = new CombGenerator($randomGenerator, $converter);
        $generator->generate($length);
    }

    public function testGenerateCalculatesPaddedHexStringFromCurrentTimestamp(): void
    {
        /** @var MockObject & RandomGeneratorInterface $randomGenerator */
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();

        /** @var MockObject & NumberConverterInterface $converter */
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $converter->expects($this->once())
            ->method('toHex')
            ->with($this->isType('string'));

        $generator = new CombGenerator($randomGenerator, $converter);
        $generator->generate(10);
    }

    public function testGenerateReturnsBinaryStringCreatedFromGeneratorAndConverter(): void
    {
        $length = 20;
        $hash = dechex(1234567891);
        $timeHash = dechex(1458147405);

        /** @var MockObject & RandomGeneratorInterface $randomGenerator */
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $randomGenerator->method('generate')
            ->with($length - $this->timestampBytes)
            ->willReturn($hash);

        /** @var MockObject & NumberConverterInterface $converter */
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $converter->method('toHex')
            ->with($this->isType('string'))
            ->willReturn($timeHash);

        $time = str_pad($timeHash, 12, '0', STR_PAD_LEFT);
        $expected = hex2bin(str_pad(bin2hex($hash), $length - $this->timestampBytes, '0')) . hex2bin($time);

        $generator = new CombGenerator($randomGenerator, $converter);
        $returned = $generator->generate($length);
        $this->assertIsString($returned);
        $this->assertSame($expected, $returned);
    }

    /**
     * @return array<array{0: int}>
     */
    public function lengthLessThanSix(): array
    {
        return [[0], [1], [2], [3], [4], [5]];
    }

    /**
     * @param int<1, max> $length
     *
     * @dataProvider lengthLessThanSix
     */
    public function testGenerateWithLessThanTimestampBytesThrowsException(int $length): void
    {
        /** @var MockObject & RandomGeneratorInterface $randomGenerator */
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();

        /** @var MockObject & NumberConverterInterface $converter */
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();

        $generator = new CombGenerator($randomGenerator, $converter);

        $this->expectException(InvalidArgumentException::class);
        $generator->generate($length);
    }

    public function testGenerateWithOddNumberOverTimestampBytesCausesError(): void
    {
        /** @var MockObject & RandomGeneratorInterface $randomGenerator */
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();

        /** @var MockObject & NumberConverterInterface $converter */
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();

        $generator = new CombGenerator($randomGenerator, $converter);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Length must be an even number');

        $generator->generate(7);
    }
}
