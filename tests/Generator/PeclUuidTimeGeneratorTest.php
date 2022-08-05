<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

class PeclUuidTimeGeneratorTest extends TestCase
{
    /**
     * @requires extension uuid
     */
    public function testGenerateCreatesUuidUsingPeclUuidMethods(): void
    {
        $generator = new PeclUuidTimeGenerator();
        $bytes = $generator->generate();
        $uuid = Uuid::fromBytes($bytes);

        /** @var Fields $fields */
        $fields = $uuid->getFields();

        $this->assertSame(16, strlen($bytes));
        $this->assertSame(Uuid::UUID_TYPE_TIME, $fields->getVersion());
    }
}
