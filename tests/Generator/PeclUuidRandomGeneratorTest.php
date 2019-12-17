<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use AspectMock\Test as AspectMock;
use Ramsey\Uuid\Generator\PeclUuidRandomGenerator;

class PeclUuidRandomGeneratorTest extends PeclUuidTestCase
{
    /**
     * @var int
     */
    private $length = 10;

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods(): void
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
    public function testGenerateReturnsUuidString(): void
    {
        $create = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_create', $this->uuidString);
        $parse = AspectMock::func('Ramsey\Uuid\Generator', 'uuid_parse', $this->uuidBinary);
        $generator = new PeclUuidRandomGenerator();
        $uuid = $generator->generate($this->length);

        $this->assertEquals($this->uuidBinary, $uuid);
        $create->verifyInvoked([UUID_TYPE_RANDOM]);
        $parse->verifyInvoked([$this->uuidString]);
    }
}
