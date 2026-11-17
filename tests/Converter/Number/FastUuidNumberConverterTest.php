<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Number;

use Ramsey\Uuid\Converter\Number\FastUuidNumberConverter;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Test\TestCase;
use Throwable;

use function bin2hex;
use function random_bytes;
use function random_int;
use function strtoupper;
use function substr;

/**
 * @requires extension fast_uuid
 */
class FastUuidNumberConverterTest extends TestCase
{
    private GenericNumberConverter $generic;
    private FastUuidNumberConverter $fast;

    protected function setUp(): void
    {
        $this->generic = new GenericNumberConverter(new BrickMathCalculator());
        $this->fast = new FastUuidNumberConverter($this->generic);
    }

    /**
     * @dataProvider provideHex
     */
    public function testFromHexMatchesGenericNumberConverter(string $hex): void
    {
        $this->assertSameResult(fn () => $this->generic->fromHex($hex), fn () => $this->fast->fromHex($hex));
    }

    /**
     * @return array<array{string}>
     */
    public function provideHex(): array
    {
        $values = [
            ['0'], ['00'], ['1'], ['f'], ['F'], ['ABCDEF'], ['3fff'], ['0123456789abcdef'],
            ['ffffffffffffffffffffffffffffffff'], ['00000000000000000000000000000000'],
            ['1ffffffffffffffffffffffffffffffff'], ['+ff'], ['-ff'], [' ff'], ['ff '], ['xyz'], [''], ["ff\n"],
        ];

        for ($i = 0; $i < 50; $i++) {
            $hex = substr(bin2hex(random_bytes(16)), 0, random_int(1, 32));
            $values[] = [$hex];
            $values[] = [strtoupper($hex)];
        }

        return $values;
    }

    /**
     * @dataProvider provideNumbers
     */
    public function testToHexMatchesGenericNumberConverter(string $number): void
    {
        $this->assertSameResult(fn () => $this->generic->toHex($number), fn () => $this->fast->toHex($number));
    }

    /**
     * @return array<array{string}>
     */
    public function provideNumbers(): array
    {
        $values = [
            ['0'], ['00'], ['007'], ['1'], ['16383'], ['18446744073709551616'],
            ['340282366920938463463374607431768211455'], ['340282366920938463463374607431768211456'],
            ['999999999999999999999999999999999999999'], ['1000000000000000000000000000000000000000'],
            ['+5'], ['-5'], [' 5'], ['5 '], ['1e3'], ['1.5'], [''], ['abc'],
        ];

        for ($i = 0; $i < 50; $i++) {
            $values[] = [(new GenericNumberConverter(new BrickMathCalculator()))->fromHex(
                substr(bin2hex(random_bytes(16)), 0, random_int(1, 32)),
            ),
            ];
        }

        return $values;
    }

    private function assertSameResult(callable $expected, callable $actual): void
    {
        try {
            $expectedResult = $expected();
        } catch (Throwable $exception) {
            $this->expectException($exception::class);
            $this->expectExceptionMessage($exception->getMessage());
            $actual();

            return;
        }

        $this->assertSame($expectedResult, $actual());
    }
}
