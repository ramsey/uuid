<?php

namespace Ramsey\Uuid\Test\Generator;

use phpmock\phpunit\PHPMock;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\SodiumRandomGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

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

        $this->assertIsString('string', $bytes);
        $this->assertEquals(16, strlen($bytes));
    }

    public function testFactoryUsesSodiumRandomGenerator()
    {
        $this->skipIfLibsodiumExtensionNotLoaded();
        $uuidFactory = new UuidFactory();
        $uuidFactory->setRandomGenerator(new SodiumRandomGenerator());
        Uuid::setFactory($uuidFactory);

        /** @var UuidFactory $actualUuidFactory */
        $actualUuidFactory = Uuid::getFactory();
        $this->assertInstanceOf(
            SodiumRandomGenerator::class,
            $actualUuidFactory->getRandomGenerator()
        );
    }
}
