<?php

namespace Ramsey\Uuid\Test\Generator;

use AspectMock\Test as AspectMock;
use Exception;
use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\TestCase;

class RandomBytesGeneratorTest extends TestCase
{
    /**
     * @return array[]
     */
    public function lengthAndHexDataProvider(): array
    {
        return [
            [6, '4f17dd046fb8'],
            [10, '4d25f6fe5327cb04267a'],
            [12, '1ea89f83bd49cacfdf119e24']
        ];
    }

    /**
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param int $length
     * @param string $hex
     * @throws Exception
     */
    public function testGenerateUsesOpenSsl($length, $hex): void
    {
        $bytes = hex2bin($hex);
        $openSsl = AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();

        $this->assertSame($bytes, $generator->generate($length));
        $openSsl->verifyInvokedOnce([$length]);
    }

    /**
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param int $length
     * @param string $hex
     * @throws Exception
     */
    public function testGenerateReturnsRandomBytes($length, $hex): void
    {
        $bytes = hex2bin($hex);
        AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();
        $this->assertEquals($bytes, $generator->generate($length));
    }
}
