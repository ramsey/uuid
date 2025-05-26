<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

class PeclUuidTimeGeneratorTest extends TestCase
{
    #[RequiresPhpExtension('uuid')]
    public function testGenerateCreatesUuidUsingPeclUuidMethods(): void
    {
        $generator = new PeclUuidTimeGenerator();
        $bytes = $generator->generate();
        $uuid = Uuid::fromBytes($bytes);

        /** @var Fields $fields */
        $fields = $uuid->getFields();

        $this->assertSame(16, strlen($bytes));
        $this->assertSame(Version::Time, $fields->getVersion());
    }
}
