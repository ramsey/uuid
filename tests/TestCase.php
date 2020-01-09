<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use AspectMock\Test as AspectMock;
use Mockery;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        AspectMock::clean();
        Mockery::close();
    }

    public static function isLittleEndianSystem(): bool
    {
        return current(unpack('v', pack('S', 0x00FF))) === 0x00FF;
    }
}
