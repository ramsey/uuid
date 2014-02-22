<?php
namespace Rhumsaa\Uuid\Console\Command;

use Rhumsaa\Uuid\Console\TestCase;
use Rhumsaa\Uuid\Console\Util\TestOutput;
use Rhumsaa\Uuid\Console\Util\BufferedOutput;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Console\Input\StringInput;

class DecodeCommandTest extends TestCase
{
    protected $execute;
    protected $decode;

    protected function setUp()
    {
        parent::setUp();

        $this->execute = new \ReflectionMethod('Rhumsaa\\Uuid\\Console\\Command\\DecodeCommand', 'execute');
        $this->execute->setAccessible(true);

        $this->decode = new DecodeCommand();
        $this->decode->setApplication(new \Rhumsaa\Uuid\Console\Application());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::configure
     */
    public function testConfigure()
    {
        $decode = new DecodeCommand();

        $this->assertEquals('decode', $decode->getName());
        $this->assertEquals('Decode a UUID and dump information about it', $decode->getDescription());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage Invalid UUID
     */
    public function testExecuteForInvalidUuid()
    {
        $input = new StringInput(
            'foobar',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForNonRFC4122Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForNonRFC4122Uuid.txt');

        $input = new StringInput(
            'ff6f8cb0-c57d-11e1-0b21-0800200c9a66',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForVersion1Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForVersion1Uuid.txt');

        $input = new StringInput(
            '2ddbf60e-7fc4-11e3-a5ac-080027cd5e4d',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForVersion2Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForVersion2Uuid.txt');

        $input = new StringInput(
            '6fa459ea-ee8a-2ca4-894e-db77e160355e',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForVersion3Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForVersion3Uuid.txt');

        $input = new StringInput(
            '6fa459ea-ee8a-3ca4-894e-db77e160355e',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForVersion4Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForVersion4Uuid.txt');

        $input = new StringInput(
            '83fc61b6-b5ef-467f-9a15-89ddee668005',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\DecodeCommand::execute
     */
    public function testExecuteForVersion5Uuid()
    {
        $expected = file_get_contents('tests/console-mocks/testExecuteForVersion5Uuid.txt');

        $input = new StringInput(
            '886313e1-3b8a-5372-9b90-0c9aee199e5d',
            $this->decode->getDefinition()
        );

        $output = new BufferedOutput();

        $this->execute->invoke($this->decode, $input, $output);

        $this->assertEquals($expected, $output->fetch());
    }
}
