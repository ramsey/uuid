<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\SodiumRandomGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

class SodiumRandomGeneratorTest extends TestCase
{
    public function setUp()
    {
        if (!extension_loaded('libsodium')) {
            $this->markTestSkipped(
                'The libsodium extension is not available.'
            );
        }
    }

    public function testGenerateReturnsBytes()
    {
        $generator = new SodiumRandomGenerator();

        $bytes = $generator->generate(16);

        $this->assertInternalType('string', $bytes);
        $this->assertEquals(16, strlen($bytes));
    }

    public function testFactoryUsesSodiumRandomGenerator()
    {
        $uuidFactory = new UuidFactory();
        $uuidFactory->setRandomGenerator(new SodiumRandomGenerator());
        Uuid::setFactory($uuidFactory);

        $uuid = Uuid::uuid4();

        $this->assertInstanceOf(
            SodiumRandomGenerator::class,
            $uuid->getFactory()->getRandomGenerator()
        );
    }
}
