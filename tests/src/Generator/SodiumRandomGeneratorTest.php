<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\SodiumRandomGenerator;

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
        $uuidFactory = new \Ramsey\Uuid\UuidFactory();
        $uuidFactory->setRandomGenerator(new SodiumRandomGenerator());
        \Ramsey\Uuid\Uuid::setFactory($uuidFactory);

        $uuid = \Ramsey\Uuid\Uuid::uuid4();

        $this->assertInstanceOf(
            SodiumRandomGenerator::class,
            $uuid->getFactory()->getRandomGenerator()
        );
    }
}
