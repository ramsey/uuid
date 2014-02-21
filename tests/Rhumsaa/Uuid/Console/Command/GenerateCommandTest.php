<?php
namespace Rhumsaa\Uuid\Console\Command;

use Rhumsaa\Uuid\Console\TestCase;
use Rhumsaa\Uuid\Console\Util\TestOutput;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Console\Input\StringInput;

class GenerateCommandTest extends TestCase
{
    const FOO_NS = 'bbd8a651-6f00-11e3-8ad8-28cfe91e4895';

    protected $execute;

    protected function setUp()
    {
        parent::setUp();

        $this->execute = new \ReflectionMethod('Rhumsaa\\Uuid\\Console\\Command\\GenerateCommand', 'execute');
        $this->execute->setAccessible(true);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::configure
     */
    public function testConfigure()
    {
        $generate = new GenerateCommand();

        $this->assertEquals('generate', $generate->getName());
        $this->assertEquals('Generate a UUID', $generate->getDescription());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidDefault()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(1, Uuid::fromString($output->messages[0])->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidDefaultWithCount()
    {
        $generate = new GenerateCommand();

        //
        // Test using the "-c" option
        //

        $input1 = new StringInput(
            '-c 9',
            $generate->getDefinition()
        );

        $output1 = new TestOutput();
        $this->execute->invoke($generate, $input1, $output1);

        $this->assertCount(9, $output1->messages);

        foreach ($output1->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(1, Uuid::fromString($uuid)->getVersion());
        }

        //
        // Test using the "--count" option
        //

        $input2 = new StringInput(
            '--count=12',
            $generate->getDefinition()
        );

        $output2 = new TestOutput();
        $this->execute->invoke($generate, $input2, $output2);

        $this->assertCount(12, $output2->messages);

        foreach ($output2->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(1, Uuid::fromString($uuid)->getVersion());
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidSpecifyVersion1()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '1',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(1, Uuid::fromString($output->messages[0])->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidSpecifyVersion1WithCount()
    {
        $generate = new GenerateCommand();

        //
        // Test using the "-c" option
        //

        $input1 = new StringInput(
            '1 -c 3',
            $generate->getDefinition()
        );

        $output1 = new TestOutput();
        $this->execute->invoke($generate, $input1, $output1);

        $this->assertCount(3, $output1->messages);

        foreach ($output1->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(1, Uuid::fromString($uuid)->getVersion());
        }

        //
        // Test using the "--count" option
        //

        $input2 = new StringInput(
            '1 --count=8',
            $generate->getDefinition()
        );

        $output2 = new TestOutput();
        $this->execute->invoke($generate, $input2, $output2);

        $this->assertCount(8, $output2->messages);

        foreach ($output2->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(1, Uuid::fromString($uuid)->getVersion());
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidSpecifyVersion4()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '4',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(4, Uuid::fromString($output->messages[0])->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     */
    public function testExecuteForUuidSpecifyVersion4WithCount()
    {
        $generate = new GenerateCommand();

        //
        // Test using the "-c" option
        //

        $input1 = new StringInput(
            '4 -c 3',
            $generate->getDefinition()
        );

        $output1 = new TestOutput();
        $this->execute->invoke($generate, $input1, $output1);

        $this->assertCount(3, $output1->messages);

        foreach ($output1->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(4, Uuid::fromString($uuid)->getVersion());
        }

        //
        // Test using the "--count" option
        //

        $input2 = new StringInput(
            '4 --count=8',
            $generate->getDefinition()
        );

        $output2 = new TestOutput();
        $this->execute->invoke($generate, $input2, $output2);

        $this->assertCount(8, $output2->messages);

        foreach ($output2->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(4, Uuid::fromString($uuid)->getVersion());
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithDnsNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:DNS "python.org"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(3, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithUrlNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:URL "http://python.org/"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(3, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('9fe8e8c4-aaa8-32a9-a55c-4535a88b748d', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithOidNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:OID "1.3.6.1"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(3, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('dd1a1cef-13d5-368a-ad82-eca71acd4cd1', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithX500Ns()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:X500 "c=ca"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(3, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('658d3002-db6b-3040-a1d1-8ddd7d189a4d', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithOtherNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 bbd8a651-6f00-11e3-8ad8-28cfe91e4895 foobar',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(3, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('0707b2c0-1f0f-3b2b-9a90-371396a90a86', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage May be either a UUID in string representation or an identifier
     */
    public function testExecuteForUuidSpecifyVersion3WithInvalidNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 foo foobar',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion3WithCount()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:DNS "python.org" -c 21',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(21, $output->messages);

        foreach ($output->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(3, Uuid::fromString($uuid)->getVersion());
            $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $uuid);
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage The name argument is required for version 3 or 5 UUIDs
     */
    public function testExecuteForUuidSpecifyVersion3WithoutName()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '3 ns:DNS',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithDnsNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:DNS "python.org"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(5, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithUrlNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:URL "http://python.org/"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(5, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('4c565f0d-3f5a-5890-b41b-20cf47701c5e', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithOidNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:OID "1.3.6.1"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(5, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('1447fa61-5277-5fef-a9b3-fbc6e44f4af3', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithX500Ns()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:X500 "c=ca"',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(5, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('cc957dd1-a972-5349-98cd-874190002798', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithOtherNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 bbd8a651-6f00-11e3-8ad8-28cfe91e4895 foobar',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(1, $output->messages);
        $this->assertTrue(Uuid::isValid($output->messages[0]));
        $this->assertEquals(5, Uuid::fromString($output->messages[0])->getVersion());
        $this->assertEquals('385c280b-1d07-5d6b-932b-ca7a11d2e7e5', $output->messages[0]);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage May be either a UUID in string representation or an identifier
     */
    public function testExecuteForUuidSpecifyVersion5WithInvalidNs()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 foo foobar',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     */
    public function testExecuteForUuidSpecifyVersion5WithCount()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:DNS "python.org" -c 21',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);

        $this->assertCount(21, $output->messages);

        foreach ($output->messages as $uuid) {
            $this->assertTrue(Uuid::isValid($uuid));
            $this->assertEquals(5, Uuid::fromString($uuid)->getVersion());
            $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $uuid);
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::validateNamespace
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage The name argument is required for version 3 or 5 UUIDs
     */
    public function testExecuteForUuidSpecifyVersion5WithoutName()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '5 ns:DNS',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);
    }

    /**
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::execute
     * @covers Rhumsaa\Uuid\Console\Command\GenerateCommand::createUuid
     * @expectedException Rhumsaa\Uuid\Console\Exception
     * @expectedExceptionMessage Invalid UUID version. Supported are version "1", "3", "4", and "5".
     */
    public function testExecuteForUuidSpecifyInvalidVersion()
    {
        $generate = new GenerateCommand();

        $input = new StringInput(
            '6',
            $generate->getDefinition()
        );

        $output = new TestOutput();

        $this->execute->invoke($generate, $input, $output);
    }
}
