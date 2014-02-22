<?php
namespace Rhumsaa\Uuid;

use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use Rhumsaa\Uuid\TestCase;

class UuidTypeTest extends TestCase
{
    private $platform;
    private $type;

    public static function setUpBeforeClass()
    {
        if (class_exists('Doctrine\\DBAL\\Types\\Type')) {
            Type::addType('uuid', 'Rhumsaa\Uuid\Doctrine\UuidType');
        }
    }

    protected function setUp()
    {
        $this->skipIfNoDoctrineDbal();
        $this->skip64BitTest();

        $this->platform = $this->getPlatformMock();
        $this->platform->expects($this->any())
            ->method('getGuidTypeDeclarationSQL')
            ->will($this->returnValue('DUMMYVARCHAR()'));

        $this->type = Type::getType('uuid');
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = $uuid->toString();
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testInvalidUuidConversionForDatabaseValue()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToDatabaseValue('abcdefg', $this->platform);
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->type->convertToPHPValue('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $this->platform);
        $this->assertInstanceOf('Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testInvalidUuidConversionForPHPValue()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('uuid', $this->type->getName());
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertEquals('DUMMYVARCHAR()', $this->type->getSqlDeclaration(array('length' => 36), $this->platform));
    }

    /**
     * @covers Rhumsaa\Uuid\Doctrine\UuidType::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPlatformMock()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')
            ->setMethods(array('getGuidTypeDeclarationSQL'))
            ->getMockForAbstractClass();
    }
}
