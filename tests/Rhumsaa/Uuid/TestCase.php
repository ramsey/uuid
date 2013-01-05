<?php
namespace Rhumsaa\Uuid;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * If the system is 32-bit, this will mark a test as skipped
     */
    protected function skip64BitTest()
    {
        if (PHP_INT_SIZE == 4) {
            $this->markTestSkipped(
                'Skipping test that can run only on a 64-bit build of PHP.'
            );
        }
    }
}
