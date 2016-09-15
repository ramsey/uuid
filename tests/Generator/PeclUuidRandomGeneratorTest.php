<?php
namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidRandomGenerator;
use AspectMock\Test as AspectMock;

/**
 * Class PeclUuidRandomGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\PeclUuidRandomGenerator
 */
class PeclUuidRandomGeneratorTest extends PeclUuidTestCase
{
    private $length = 10; //Doesn't matter, it isn't used

    /**
     * This test is just to check collaboration with the PECL UUID extension - not to check
     * the correctness of the methods defined in that extension.
     * So we are just checking that the UUID methods are called with the right parameters.
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods()
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);

        $generator = new PeclUuidRandomGenerator();
        $generator->generate($this->length);

        $create->verifyInvoked(UUID_TYPE_RANDOM);
        $parse->verifyInvoked($this->uuidString);
    }

    /**
     * This test is for the return type of the generate method
     * It ensures that the generate method returns whatever value uuid_parse returns.
     */
    public function testGenerateReturnsUuidString()
    {
        AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);
        $generator = new PeclUuidRandomGenerator;
        $uuid = $generator->generate($this->length);
        $this->assertEquals($this->uuidBinary, $uuid);
    }
}
