<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Exception;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\TestCase;
use phpmock\mockery\PHPMockery;

use function hex2bin;

class RandomBytesGeneratorTest extends TestCase
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function lengthAndHexDataProvider(): array
    {
        return [
            [6, '4f17dd046fb8'],
            [10, '4d25f6fe5327cb04267a'],
            [12, '1ea89f83bd49cacfdf119e24'],
        ];
    }

    /**
     * @param int<1, max> $length
     *
     * @throws Exception
     *
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateReturnsRandomBytes(int $length, string $hex): void
    {
        $bytes = hex2bin($hex);

        PHPMockery::mock('Ramsey\Uuid\Generator', 'random_bytes')
            ->once()
            ->with($length)
            ->andReturn($bytes);

        $generator = new RandomBytesGenerator();

        $this->assertSame($bytes, $generator->generate($length));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateThrowsExceptionWhenExceptionThrownByRandombytes(): void
    {
        PHPMockery::mock('Ramsey\Uuid\Generator', 'random_bytes')
            ->once()
            ->with(16)
            ->andThrow(new Exception('Could not gather sufficient random data'));

        $generator = new RandomBytesGenerator();

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $generator->generate(16);
    }
}
