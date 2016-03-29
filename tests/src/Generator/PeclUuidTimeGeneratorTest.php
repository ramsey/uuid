<?php
namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use AspectMock\Test as AspectMock;

/**
 * Class PeclUuidTimeGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\PeclUuidTimeGenerator
 */
class PeclUuidTimeGeneratorTest extends PeclUuidTestCase
{

    /**
     * This test is just to check collaboration with the PECL UUID extension - not to check
     * the correctness of the methods defined in that extension.
     * So we are just checking that the UUID methods are called with the right parameters.
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods()
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);

        $generator = new PeclUuidTimeGenerator;
        $generator->generate();

        $create->verifyInvoked(UUID_TYPE_TIME);
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
        $generator = new PeclUuidTimeGenerator;
        $uuid = $generator->generate();
        $this->assertEquals($this->uuidBinary, $uuid);
    }
}
