<?php

namespace Rhumsaa\Uuid;

class PeclUuidTest extends \PHPUnit_Framework_TestCase
{
    private $mockFactory;

    protected function setUp()
    {
        $this->mockFactory = $this->getMock('Rhumsaa\Uuid\UuidFactoryInterface');

        Uuid::setFactory(new PeclUuidFactory($this->mockFactory));
    }

    public function getUuid1Params()
    {
        return [
            [ true, null ],
            [ null, true ],
            [ true, true ]
        ];
    }

    /**
     * @dataProvider getUuid1Params
     */
    public function testUuid1WithParametersIsDelegated($node, $clockSeq)
    {
        $this->mockFactory->expects($this->once())
            ->method('uuid1')
            ->with($node, $clockSeq);

        Uuid::uuid1($node, $clockSeq);
    }

    public function testUuid1WithoutParametersIsNotDelegated()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('PECL Uuid extension not available in HHVM');
        }

        $this->mockFactory->expects($this->never())
            ->method('uuid1');

        Uuid::uuid1();
    }

    public function testUuid1WithoutExtensionIsDelegated()
    {
        $factory = new PeclUuidFactory($this->mockFactory);
        $factory->disablePecl();

        Uuid::setFactory($factory);

        $this->mockFactory->expects($this->once())
            ->method('uuid1');

        Uuid::uuid1();
    }

    public function testUuid1Version()
    {
        Uuid::setFactory(new PeclUuidFactory(new UuidFactory()));

        $uuid = Uuid::uuid1();

        $this->assertEquals(1, $uuid->getVersion());
    }

    public function testUuid3IsDelegated()
    {
        $this->mockFactory->expects($this->once())
            ->method('uuid3');

        Uuid::uuid3(Uuid::NAMESPACE_DNS, str_replace('\\', '.', __NAMESPACE__));
    }

    public function testUuid4WithoutExtensionIsDelegated()
    {
        $factory = new PeclUuidFactory($this->mockFactory);
        $factory->disablePecl();

        Uuid::setFactory($factory);

        $this->mockFactory->expects($this->once())
            ->method('uuid4');

        Uuid::uuid4();
    }

    public function testUuid4WithParametersIsNeverDelegated()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('PECL Uuid extension not available in HHVM');
        }

        $this->mockFactory->expects($this->never())
            ->method('uuid4');

        Uuid::uuid4();
    }

    public function testUuid4Version()
    {
        Uuid::setFactory(new PeclUuidFactory(new UuidFactory()));

        $uuid = Uuid::uuid4();

        $this->assertEquals(4, $uuid->getVersion());
    }

    public function testUuid5IsDelegated()
    {
        $this->mockFactory->expects($this->once())
            ->method('uuid5');

        Uuid::uuid5(Uuid::NAMESPACE_DNS, str_replace('\\', '.', __NAMESPACE__));
    }
}