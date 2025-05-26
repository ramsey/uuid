<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Generator;

use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Ramsey\Uuid\Generator\PeclUuidRandomGenerator;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Uuid;

class PeclUuidRandomGeneratorTest extends TestCase
{
    #[RequiresPhpExtension('uuid')]
    public function testGenerateCreatesUuidUsingPeclUuidMethods(): void
    {
        $generator = new PeclUuidRandomGenerator();
        $bytes = $generator->generate(10);
        $uuid = Uuid::fromBytes($bytes);

        /** @var Fields $fields */
        $fields = $uuid->getFields();

        $this->assertSame(16, strlen($bytes));
        $this->assertSame(Version::Random, $fields->getVersion());
    }
}
