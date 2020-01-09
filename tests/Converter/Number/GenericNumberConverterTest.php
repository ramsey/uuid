<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;

class GenericNumberConverterTest extends TestCase
{
    public function testFromHex(): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericNumberConverter($calculator);

        $this->assertSame('65535', $converter->fromHex('ffff'));
    }

    public function testToHex(): void
    {
        $calculator = new BrickMathCalculator();
        $converter = new GenericNumberConverter($calculator);

        $this->assertSame('ffff', $converter->toHex('65535'));
    }
}
