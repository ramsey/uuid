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
    private $length = 10;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods()
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);

        $generator = new PeclUuidRandomGenerator();
        $uuid = $generator->generate($this->length);

        $this->assertEquals($this->uuidBinary, $uuid);
        $create->verifyInvoked([UUID_TYPE_RANDOM]);
        $parse->verifyInvoked([$this->uuidString]);
    }

    /**
     * This test is for the return type of the generate method
     * It ensures that the generate method returns whatever value uuid_parse returns.
     */
    public function testGenerateReturnsUuidString()
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);
        $generator = new PeclUuidRandomGenerator;
        $uuid = $generator->generate($this->length);

        $this->assertEquals($this->uuidBinary, $uuid);
        $create->verifyInvoked([UUID_TYPE_RANDOM]);
        $parse->verifyInvoked([$this->uuidString]);
    }
}
