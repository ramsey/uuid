<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use phpmock\mockery\PHPMockery;

use const UUID_TYPE_TIME;

class PeclUuidTimeGeneratorTest extends PeclUuidTestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods(): void
    {
        PHPMockery::mock('Ramsey\Uuid\Generator', 'uuid_create')
            ->once()
            ->with(UUID_TYPE_TIME)
            ->andReturn($this->uuidString);

        PHPMockery::mock('Ramsey\Uuid\Generator', 'uuid_parse')
            ->once()
            ->with($this->uuidString)
            ->andReturn($this->uuidBinary);

        $generator = new PeclUuidTimeGenerator();
        $uuid = $generator->generate();

        $this->assertSame($this->uuidBinary, $uuid);
    }
}
