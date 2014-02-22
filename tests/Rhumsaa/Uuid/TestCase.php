<?php
namespace Rhumsaa\Uuid;

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

    protected function skipIfNoSymfonyConsole()
    {
        if (!$this->hasSymfonyConsole()) {
            $this->markTestSkipped(
                'Skipping test that requires symfony/console.'
            );
        }
    }

    protected function skipIfNoDoctrineDbal()
    {
        if (!$this->hasDoctrineDbal()) {
            $this->markTestSkipped(
                'Skipping test that requires doctrine/dbal.'
            );
        }
    }

    protected function hasMoontoastMath()
    {
        return class_exists('Moontoast\\Math\\BigNumber');
    }

    protected function hasSymfonyConsole()
    {
        return class_exists('Symfony\\Component\\Console\\Application');
    }

    protected function hasDoctrineDbal()
    {
        return class_exists('Doctrine\\DBAL\\Types\\Type');
    }
}
