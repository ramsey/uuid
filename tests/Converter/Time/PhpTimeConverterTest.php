<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Time;

use Mockery;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

class PhpTimeConverterTest extends TestCase
{
    public function testCalculateTimeReturnsArrayOfTimeSegments(): void
    {
        $this->skip64BitTest();

        $seconds = 5;
        $microSeconds = 3;
        $calculatedTime = ($seconds * 10000000) + ($microSeconds * 10) + 0x01b21dd213814000;

        $expectedArray = [
            'low' => sprintf('%08x', $calculatedTime & 0xffffffff),
            'mid' => sprintf('%04x', ($calculatedTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($calculatedTime >> 48) & 0x0fff),
        ];

        $converter = new PhpTimeConverter();
        $returned = $converter->calculateTime((string) $seconds, (string) $microSeconds);

        $this->assertSame($expectedArray, $returned);
    }

    public function testConvertTime(): void
    {
        $this->skip64BitTest();

        $converter = new PhpTimeConverter();
        $returned = $converter->convertTime('135606608744910000');

        $this->assertSame('1341368074', $returned);
    }

    public function testCalculateTimeThrowsExceptionWhenNot64BitPhp(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();
        $converter->shouldAllowMockingProtectedMethods();
        $converter->shouldReceive('getPhpIntSize')->once()->andReturn(4);

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('The PHP build must be 64-bit to use this converter');

        $converter->calculateTime('1234', '5678');
    }

    public function testConvertTimeThrowsExceptionWhenNot64BitPhp(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();
        $converter->shouldAllowMockingProtectedMethods();
        $converter->shouldReceive('getPhpIntSize')->once()->andReturn(4);

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('The PHP build must be 64-bit to use this converter');

        $converter->convertTime('1234');
    }

    public function testCalculateTimeThrowsExceptionWhenSecondsIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();
        $converter->shouldAllowMockingProtectedMethods();
        $converter->shouldReceive('getPhpIntSize')->once()->andReturn(8);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$seconds must contain only digits');

        $converter->calculateTime('12.34', '5678');
    }

    public function testCalculateTimeThrowsExceptionWhenMicroSecondsIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();
        $converter->shouldAllowMockingProtectedMethods();
        $converter->shouldReceive('getPhpIntSize')->once()->andReturn(8);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$microSeconds must contain only digits');

        $converter->calculateTime('1234', '56.78');
    }

    public function testConvertTimeThrowsExceptionWhenTimestampIsNotOnlyDigits(): void
    {
        /** @var Mockery\MockInterface & PhpTimeConverter $converter */
        $converter = Mockery::mock(PhpTimeConverter::class)->makePartial();
        $converter->shouldAllowMockingProtectedMethods();
        $converter->shouldReceive('getPhpIntSize')->once()->andReturn(8);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$timestamp must contain only digits');

        $converter->convertTime('1234.56');
    }
}
