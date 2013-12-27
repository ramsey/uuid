<?php
namespace Rhumsaa\Uuid\Console;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Rhumsaa\Uuid\Console\Application::__construct
     */
    public function testConstructor()
    {
        $app = new Application();

        // Reset the error handler, since the constructor sets it
        restore_error_handler();

        $this->assertInstanceOf('Rhumsaa\\Uuid\\Console\\Application', $app);
        $this->assertEquals('uuid', $app->getName());
        $this->assertEquals(\Rhumsaa\Uuid\Uuid::VERSION, $app->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Application::getDefaultCommands
     */
    public function testGetDefaultCommands()
    {
        $app = new Application();

        // Reset the error handler, since the constructor sets it
        restore_error_handler();

        $method = new \ReflectionMethod('Rhumsaa\\Uuid\\Console\\Application', 'getDefaultCommands');
        $method->setAccessible(true);

        $this->assertInternalType('array', $method->invoke($app));
    }
}
