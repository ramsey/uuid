<?php
namespace Rhumsaa\Uuid;

use Doctrine\DBAL\Types\Type;
use Doctrine\Tests\DBAL\Mocks\MockPlatform;
use PHPUnit_Framework_TestCase;

class UuidTypeTest extends PHPUnit_Framework_TestCase
{
    private $platform;
    private $type;

    public static function setUpBeforeClass()
    {
        Type::addType('uuid', 'Rhumsaa\Uuid\Doctrine\UuidType');
    }

    protected function setUp()
    {
        // Skip these tests if run on a 32-bit build of PHP
        if (PHP_INT_SIZE == 4) {
            $this->markTestSkipped(
                'Running tests on a 32-bit build of PHP; 64-bit build required.'
            );
        }

        $this->platform = new MockPlatform();
        $this->type = Type::getType('uuid');
    }

    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = $uuid->toString();
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->type->convertToPHPValue('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $this->platform);
        $this->assertInstanceOf('Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testInvalidUuidConversion()
    {
        $this->setExpectedException('Doctrine\DBAL\Types\ConversionException');
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    public function testNullConversion()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }
}
