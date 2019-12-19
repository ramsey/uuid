<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use AspectMock\Test as AspectMock;
use Exception;
use Ramsey\Uuid\Exception\RandomSourceException;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\TestCase;

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
     * @throws Exception
     *
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateUsesOpenSsl(int $length, string $hex): void
    {
        $bytes = hex2bin($hex);
        $openSsl = AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();

        $this->assertSame($bytes, $generator->generate($length));
        $openSsl->verifyInvokedOnce([$length]);
    }

    /**
     * @throws Exception
     *
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateReturnsRandomBytes(int $length, string $hex): void
    {
        $bytes = hex2bin($hex);
        AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();
        $this->assertEquals($bytes, $generator->generate($length));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateThrowsExceptionWhenExceptionThrownByRandombytes(): void
    {
        AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', function (): void {
            throw new Exception('Could not gather sufficient random data');
        });

        $generator = new RandomBytesGenerator();

        $this->expectException(RandomSourceException::class);
        $this->expectExceptionMessage('Could not gather sufficient random data');

        $generator->generate(16);
    }
}
