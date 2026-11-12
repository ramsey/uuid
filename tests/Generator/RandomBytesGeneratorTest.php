<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\MocksFunctions;
use Ramsey\Uuid\Test\TestCase;

use function hex2bin;

class RandomBytesGeneratorTest extends TestCase
{
    use MocksFunctions;

    /**
     * @return array<array{0: positive-int, 1: non-empty-string}>
     */
    public static function lengthAndHexDataProvider(): array
    {
        return [
            [6, '4f17dd046fb8'],
            [10, '4d25f6fe5327cb04267a'],
            [12, '1ea89f83bd49cacfdf119e24'],
        ];
    }

    /**
     * @param positive-int $length
     * @param non-empty-string $hex
     *
     * @throws Exception
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('lengthAndHexDataProvider')]
    public function testGenerateReturnsRandomBytes(int $length, string $hex): void
    {
        $bytes = hex2bin($hex);

        $this->expectFunctionCall(
            'Ramsey\Uuid\Generator',
            'random_bytes',
            [$length],
            $bytes,
        );

        $generator = new RandomBytesGenerator();

        $this->assertSame($bytes, $generator->generate($length));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testGenerateThrowsExceptionWhenExceptionThrownByRandomBytes(): void
    {
        $this->expectFunctionCall(
            'Ramsey\Uuid\Generator',
            'random_bytes',
            [16],
            new Exception('Could not gather sufficient random data'),
        );

        $generator = new RandomBytesGenerator();

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $generator->generate(16);
    }
}
