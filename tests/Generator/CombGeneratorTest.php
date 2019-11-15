<?php

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\Error\Error as PHPUnitError;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class CombGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\CombGenerator
 */
class CombGeneratorTest extends TestCase
{
    private $timestampBytes = 6;

    public function testGenerateUsesRandomGeneratorWithLengthMinusTimestampBytes()
    {
        $length = 10;
        $expectedLength = ($length - $this->timestampBytes);
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $randomGenerator->expects($this->once())
            ->method('generate')
            ->with($expectedLength);
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $generator = new CombGenerator($randomGenerator, $converter);
        $generator->generate($length);
    }

    public function testGenerateCalculatesPaddedHexStringFromCurrentTimestamp()
    {
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $converter->expects($this->once())
            ->method('toHex')
            ->with($this->isType('string'));
        $generator = new CombGenerator($randomGenerator, $converter);
        $generator->generate(10);
    }

    public function testGenerateReturnsBinaryStringCreatedFromGeneratorAndConverter()
    {
        $length = 20;
        $hash = \dechex(1234567891);
        $timeHash = \dechex(1458147405);

        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $randomGenerator->method('generate')
            ->with(($length - $this->timestampBytes))
            ->willReturn($hash);

        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $converter->method('toHex')
            ->with($this->isType('string'))
            ->willReturn($timeHash);

        $time = \str_pad($timeHash, 12, '0', STR_PAD_LEFT);
        $expected = \hex2bin(\str_pad(\bin2hex($hash), $length - $this->timestampBytes, '0')) . \hex2bin($time);

        $generator = new CombGenerator($randomGenerator, $converter);
        $returned = $generator->generate($length);
        $this->assertInternalType('string', $returned);
        $this->assertEquals($expected, $returned);
    }

    public function lengthLessThanSix()
    {
        return [[0], [1], [2], [3], [4], [5]];
    }

    /**
     * @dataProvider lengthLessThanSix
     */
    public function testGenerateWithLessThanTimestampBytesThrowsException($length)
    {
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $generator = new CombGenerator($randomGenerator, $converter);

        $this->expectException(\InvalidArgumentException::class);
        $generator->generate($length);
    }

    /**
     * PHP Unit converts the error to an exception so we can test it.
     */
    public function testGenerateWithOddNumberOverTimestampBytesCausesError()
    {
        $randomGenerator = $this->getMockBuilder(RandomGeneratorInterface::class)->getMock();
        $converter = $this->getMockBuilder(NumberConverterInterface::class)->getMock();
        $generator = new CombGenerator($randomGenerator, $converter);

        $this->expectException(PHPUnitError::class);
        $generator->generate(7);
    }
}
