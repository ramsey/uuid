<?php
namespace Rhumsaa\Uuid;

/**
 * This class tests the environment to ensure the library functions as designed
 * on 32-bit and 64-bit environments.
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Rhumsaa\Uuid\Uuid::__construct
     */
    public function testConstructorException()
    {
        if (PHP_INT_SIZE == 4) {

            $this->setExpectedException(
                'OverflowException',
                'Attempting to create a UUID on a 32-bit build of PHP. This library requires a 64-bit build of PHP.'
            );

            $uuid = Uuid::uuid1();

        } else {

            $this->markTestSkipped('This test is only applicable on a 32-bit system.');

        }
    }
}
