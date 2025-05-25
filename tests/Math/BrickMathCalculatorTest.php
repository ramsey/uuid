<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Math;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Integer as IntegerObject;

class BrickMathCalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $int1 = new IntegerObject(5);
        $int2 = new IntegerObject(6);
        $int3 = new IntegerObject(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->add($int1, $int2, $int3);

        $this->assertSame('18', $result->toString());
    }

    public function testSubtract(): void
    {
        $int1 = new IntegerObject(5);
        $int2 = new IntegerObject(6);
        $int3 = new IntegerObject(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->subtract($int1, $int2, $int3);

        $this->assertSame('-8', $result->toString());
    }

    public function testMultiply(): void
    {
        $int1 = new IntegerObject(5);
        $int2 = new IntegerObject(6);
        $int3 = new IntegerObject(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->multiply($int1, $int2, $int3);

        $this->assertSame('210', $result->toString());
    }

    public function testDivide(): void
    {
        $int1 = new IntegerObject(1023);
        $int2 = new IntegerObject(6);
        $int3 = new IntegerObject(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->divide(RoundingMode::HALF_UP, 0, $int1, $int2, $int3);

        $this->assertSame('24', $result->toString());
    }

    public function testFromBase(): void
    {
        $calculator = new BrickMathCalculator();

        $result = $calculator->fromBase('ffffffffffffffffffff', 16);

        $this->assertSame('1208925819614629174706175', $result->toString());
    }

    public function testToBase(): void
    {
        $intValue = new IntegerObject('1208925819614629174706175');
        $calculator = new BrickMathCalculator();

        $this->assertSame('ffffffffffffffffffff', $calculator->toBase($intValue, 16));
    }

    public function testToHexadecimal(): void
    {
        $intValue = new IntegerObject('1208925819614629174706175');
        $calculator = new BrickMathCalculator();

        $result = $calculator->toHexadecimal($intValue);

        $this->assertSame('ffffffffffffffffffff', $result->toString());
    }

    public function testFromBaseThrowsException(): void
    {
        $calculator = new BrickMathCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"o" is not a valid character in base 16');

        $calculator->fromBase('foobar', 16);
    }

    public function testToBaseThrowsException(): void
    {
        $calculator = new BrickMathCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Base 1024 is out of range [2, 36]');

        $calculator->toBase(new IntegerObject(42), 1024);
    }
}
