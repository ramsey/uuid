<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\RandomBytesGenerator;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

/**
 * Class RandomBytesGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\RandomBytesGenerator
 */
class RandomBytesGeneratorTest extends TestCase
{
    protected function setUp()
    {
        $this->skipIfHhvm();
        parent::setUp();
    }

    public function lengthAndHexDataProvider()
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
     */
    public function testGenerateUsesOpenSsl($length, $hex)
    {
        $bytes = hex2bin($hex);
        $openSsl = AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();
        $generator->generate($length);

        $openSsl->verifyInvokedOnce([$length]);
    }

    /**
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param int $length
     * @param string $hex
     */
    public function testGenerateReturnsRandomBytes($length, $hex)
    {
        $bytes = hex2bin($hex);
        AspectMock::func('Ramsey\Uuid\Generator', 'random_bytes', $bytes);
        $generator = new RandomBytesGenerator();
        $this->assertEquals($bytes, $generator->generate($length));
    }
}
