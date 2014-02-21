<?php
namespace Rhumsaa\Uuid\Console;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Console\Application') || !class_exists('Moontoast\Math\BigNumber')) {
            $this->markTestSkipped(
                'symfony/console and moontoast/math are required to run these tests'
            );
        }
    }
}
