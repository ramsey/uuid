<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Math;

use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\IntegerValue;

class BrickMathCalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $int1 = new IntegerValue(5);
        $int2 = new IntegerValue(6);
        $int3 = new IntegerValue(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->add($int1, $int2, $int3);

        $this->assertSame('18', $result->toString());
    }

    public function testSubtract(): void
    {
        $int1 = new IntegerValue(5);
        $int2 = new IntegerValue(6);
        $int3 = new IntegerValue(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->subtract($int1, $int2, $int3);

        $this->assertSame('-8', $result->toString());
    }

    public function testMultiply(): void
    {
        $int1 = new IntegerValue(5);
        $int2 = new IntegerValue(6);
        $int3 = new IntegerValue(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->multiply($int1, $int2, $int3);

        $this->assertSame('210', $result->toString());
    }

    public function testDivide(): void
    {
        $int1 = new IntegerValue(1023);
        $int2 = new IntegerValue(6);
        $int3 = new IntegerValue(7);

        $calculator = new BrickMathCalculator();

        $result = $calculator->divide(RoundingMode::HALF_UP, $int1, $int2, $int3);

        $this->assertSame('24', $result->toString());
    }

    public function testFromBase(): void
    {
        $calculator = new BrickMathCalculator();

        $result = $calculator->fromBase('ffffffffffffffffffff', 16);

        $this->assertInstanceOf(IntegerValue::class, $result);
        $this->assertSame('1208925819614629174706175', $result->toString());
    }

    public function testToBase(): void
    {
        $intValue = new IntegerValue('1208925819614629174706175');
        $calculator = new BrickMathCalculator();

        $this->assertSame('ffffffffffffffffffff', $calculator->toBase($intValue, 16));
    }

    public function testToHexadecimal(): void
    {
        $intValue = new IntegerValue('1208925819614629174706175');
        $calculator = new BrickMathCalculator();

        $result = $calculator->toHexadecimal($intValue);

        $this->assertInstanceOf(Hexadecimal::class, $result);
        $this->assertSame('ffffffffffffffffffff', $result->toString());
    }
}
