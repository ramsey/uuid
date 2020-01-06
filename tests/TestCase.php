<?php
namespace Ramsey\Uuid\Test;

use AspectMock\Test as AspectMock;
use Mockery;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
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

    protected function skipIfNoBrickMath()
    {
        if (!$this->hasBrickMath()) {
            $this->markTestSkipped(
                'Skipping test that requires brick/math.'
            );
        }
    }

    protected function hasBrickMath()
    {
        return class_exists('Brick\\Math\\BigInteger');
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
