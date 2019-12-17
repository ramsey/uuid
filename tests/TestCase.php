<?php
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

    protected function skip64BitTest(): void
    {
        if (PHP_INT_SIZE === 4) {
            $this->markTestSkipped(
                'Skipping test that can run only on a 64-bit build of PHP.'
            );
        }
    }

    protected function skipIfNoMoontoastMath(): void
    {
        if (!$this->hasMoontoastMath()) {
            $this->markTestSkipped(
                'Skipping test that requires moontoast/math.'
            );
        }
    }

    protected function skipIfNoGmp(): void
    {
        if (!$this->hasGmp()) {
            $this->markTestSkipped(
                'Skipping test that requires GMP.'
            );
        }
    }

    protected function hasMoontoastMath(): bool
    {
        return class_exists('Moontoast\\Math\\BigNumber');
    }

    protected function hasGmp(): bool
    {
        return extension_loaded('gmp');
    }

    protected function skipIfLittleEndianHost(): void
    {
        if (self::isLittleEndianSystem()) {
            $this->markTestSkipped(
                'Skipping test targeting big-endian architectures.'
            );
        }
    }

    protected function skipIfBigEndianHost(): void
    {
        if (!self::isLittleEndianSystem()) {
            $this->markTestSkipped(
                'Skipping test targeting little-endian architectures.'
            );
        }
    }

    public static function isLittleEndianSystem(): bool
    {
        return current(unpack('v', pack('S', 0x00FF))) === 0x00FF;
    }
}
