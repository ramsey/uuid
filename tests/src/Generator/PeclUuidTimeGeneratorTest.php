<?php
namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Generator\PeclUuidHelper;

// @codingStandardsIgnoreStart
require_once 'PeclUuidTestHelper.php';
// @codingStandardsIgnoreEnd

class PeclUuidTimeGeneratorTest extends TestCase
{
    public function testGenerateCreatesUuid()
    {
        $generator = new PeclUuidTimeGenerator;
        $uuid      = $generator->generate();
        $this->assertEquals(PeclUuidHelper::EXAMPLE_UUID, $uuid);
    }
}
