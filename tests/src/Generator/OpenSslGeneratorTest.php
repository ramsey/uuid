<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\OpenSslGenerator;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

/**
 * Class OpenSslGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\OpenSslGenerator
 */
class OpenSslGeneratorTest extends TestCase
{
    protected function setUp()
    {
        $this->skipIfHhvm();
        parent::setUp();
    }

    public function lengthAndHexDataProvider()
    {
        return [
            [6, '005340670735'],
            [10, '292be7f7e462b2b2d24a'],
            [12, 'a9e3504ed48cffefe412eb70']
        ];
    }

    /**
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateUsesOpenSsl($length, $hex)
    {
        $bytes = hex2bin($hex);
        $openSsl = AspectMock::func('Ramsey\Uuid\Generator', 'openssl_random_pseudo_bytes', $bytes);
        $generator = new OpenSslGenerator();
        $generator->generate($length);

        $openSsl->verifyInvokedOnce([$length]);
    }

    /**
     * @dataProvider lengthAndHexDataProvider
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateReturnsRandomBytes($length, $hex)
    {
        $bytes = hex2bin($hex);
        AspectMock::func('Ramsey\Uuid\Generator', 'openssl_random_pseudo_bytes', $bytes);
        $generator = new OpenSslGenerator();
        $this->assertEquals($bytes, $generator->generate($length));
    }
}
