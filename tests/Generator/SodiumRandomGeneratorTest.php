<?php

namespace Ramsey\Uuid\Test\Generator;

use phpmock\phpunit\PHPMock;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\SodiumRandomGenerator;

class SodiumRandomGeneratorTest extends TestCase
{
    use PHPMock;

    protected function skipIfLibsodiumExtensionNotLoaded()
    {
        if (!extension_loaded('libsodium')) {
            $this->markTestSkipped(
                'The libsodium extension is not available.'
            );
        }
    }

    public function testGenerateReturnsBytes()
    {
        $this->skipIfLibsodiumExtensionNotLoaded();
        $generator = new SodiumRandomGenerator();

        $bytes = $generator->generate(16);

        $this->assertInternalType('string', $bytes);
        $this->assertEquals(16, strlen($bytes));
    }

    public function testFactoryUsesSodiumRandomGenerator()
    {
        $this->skipIfLibsodiumExtensionNotLoaded();
        $uuidFactory = new \Ramsey\Uuid\UuidFactory();
        $uuidFactory->setRandomGenerator(new SodiumRandomGenerator());
        \Ramsey\Uuid\Uuid::setFactory($uuidFactory);

        $uuid = \Ramsey\Uuid\Uuid::uuid4();

        $this->assertInstanceOf(
            'Ramsey\Uuid\Generator\SodiumRandomGenerator',
            $uuid->getFactory()->getRandomGenerator()
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateUsesSodiumLibrary()
    {
        $randomBytesFunc = $this->getFunctionMock('Sodium', 'randombytes_buf');
        $randomBytesFunc->expects($this->once())
            ->with(10);
        $generator = new SodiumRandomGenerator();
        $generator->generate(10);
    }
}
