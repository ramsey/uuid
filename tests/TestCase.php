<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

use function current;
use function pack;
use function unpack;

class TestCase extends PhpUnitTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public static function isLittleEndianSystem(): bool
    {
        /** @var int[] $unpacked */
        $unpacked = unpack('v', pack('S', 0x00FF));

        return current($unpacked) === 0x00FF;
    }
}
