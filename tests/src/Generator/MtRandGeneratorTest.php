<?php

namespace Ramsey\Uuid\Test\Generator;

use Ramsey\Uuid\Generator\MtRandGenerator;
use Ramsey\Uuid\Test\TestCase;

/**
 * Class MtRandGeneratorTest
 * @package Ramsey\Uuid\Test\Generator
 * @covers Ramsey\Uuid\Generator\MtRandGenerator
 */
class MtRandGeneratorTest extends TestCase
{
    public function lengthDataProvider()
    {
        return [
            [0],
            [1],
            [2],
            [16],
            [1000]
        ];
    }

    /**
     * @dataProvider lengthDataProvider
     */
    public function testGenerateReturnsStringOfGivenLength($length)
    {
        $generator = new MtRandGenerator();
        $returned = $generator->generate($length);
        $this->assertEquals($length, strlen($returned));
    }
}
