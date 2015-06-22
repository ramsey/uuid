<?php
namespace Ramsey\Uuid;

class TestCase extends \PHPUnit_Framework_TestCase
{
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

    protected function hasMoontoastMath()
    {
        return class_exists('Moontoast\\Math\\BigNumber');
    }
}
