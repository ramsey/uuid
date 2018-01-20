<?php
namespace Ramsey\Uuid\Test;

use AspectMock\Test as AspectMock;
use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        AspectMock::clean();
        Mockery::close();
    }

    protected function skip64BitTest()
    {
        if (PHP_INT_SIZE == 4) {
            $this->markTestSkipped(
                'Skipping test that can run only on a 64-bit build of PHP.'
            );
        }
    }

    protected function skipIfNoMoontoastMath()
    {
        if (!$this->hasMoontoastMath()) {
            $this->markTestSkipped(
                'Skipping test that requires moontoast/math.'
            );
        }
    }

    protected function skipIfNoGmp()
    {
        if (!$this->hasGmp()) {
            $this->markTestSkipped(
                'Skipping test that requires GMP.'
            );
        }
    }

    protected function hasMoontoastMath()
    {
        return class_exists('Moontoast\\Math\\BigNumber');
    }

    protected function hasGmp()
    {
        return extension_loaded('gmp');
    }

    protected function skipIfLittleEndianHost()
    {
        if (self::isLittleEndianSystem()) {
            $this->markTestSkipped(
                'Skipping test targeting big-endian architectures.'
            );
        }
    }

    protected function skipIfBigEndianHost()
    {
        if (!self::isLittleEndianSystem()) {
            $this->markTestSkipped(
                'Skipping test targeting little-endian architectures.'
            );
        }
    }

    public static function isLittleEndianSystem()
    {
        return current(unpack('v', pack('S', 0x00FF))) === 0x00FF;
    }
}
