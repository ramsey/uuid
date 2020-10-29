<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidRandomGenerator;
use phpmock\mockery\PHPMockery;

use const UUID_TYPE_RANDOM;

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
        PHPMockery::mock('Ramsey\Uuid\Generator', 'uuid_create')
            ->once()
            ->with(UUID_TYPE_RANDOM)
            ->andReturn($this->uuidString);

        PHPMockery::mock('Ramsey\Uuid\Generator', 'uuid_parse')
            ->once()
            ->with($this->uuidString)
            ->andReturn($this->uuidBinary);

        $generator = new PeclUuidRandomGenerator();
        $uuid = $generator->generate($this->length);

        $this->assertSame($this->uuidBinary, $uuid);
    }
}
