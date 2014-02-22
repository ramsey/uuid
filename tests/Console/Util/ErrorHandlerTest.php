<?php
namespace Rhumsaa\Uuid\Console\Util;

use Rhumsaa\Uuid\Console\TestCase;

class ErrorHandlerTest extends TestCase
{
    /**
     * @covers Rhumsaa\Uuid\Console\Util\ErrorHandler::register
     */
    public function testRegister()
    {
        $expected = array (
            'Rhumsaa\\Uuid\Console\\Util\\ErrorHandler',
            'handle',
        );

        $originalHandler = set_error_handler(function () {});

        ErrorHandler::register();
        $testHandler = set_error_handler(function () {});

        // Set handler back to original
        set_error_handler($originalHandler);

        $this->assertEquals($expected, $testHandler);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Util\ErrorHandler::handle
     * @expectedException ErrorException
     * @expectedExceptionMessage Test exception
     */
    public function testHandle()
    {
        error_reporting(E_ALL);
        ErrorHandler::handle(1, 'Test exception', __FILE__, __LINE__);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Util\ErrorHandler::handle
     */
    public function testHandleNoException()
    {
        error_reporting(0);

        $this->assertEmpty(ErrorHandler::handle(1, 'Test exception', __FILE__, __LINE__));
    }
}
