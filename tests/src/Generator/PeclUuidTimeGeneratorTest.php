<?php
namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Test\TestCase;
use AspectMock\Test as AspectMock;

define('UUID_TYPE_TIME', 1);

class PeclUuidTimeGeneratorTest extends TestCase
{
    private $uuidString = 'b08c6fff-7dc5-e111-9b21-0800200c9a66';
    private $uuidPack = '62303863366666662d376463352d653131312d396232312d303830303230306339613636';

    protected function tearDown()
    {
        AspectMock::clean(); // remove all registered test doubles
        parent::tearDown();
    }

    /**
     * This test is just to check collaboration with the PECL UUID extension - not to check
     * the correctness of the methods defined in that extension.
     * So we are just checking that the UUID methods are called with the right parameters.
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods()
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidPack);

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
        AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidPack);
        $generator = new PeclUuidTimeGenerator;
        $uuid = $generator->generate();
        $this->assertEquals($this->uuidPack, $uuid);
    }
}
