<?php

namespace Ramsey\Uuid\Test;

use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\UuidFactory;

class UuidFactoryTest extends TestCase
{
    public function testParsesUuidCorrectly()
    {
        $factory = new UuidFactory();

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    public function testParsesGuidCorrectly()
    {
        $factory = new UuidFactory(new FeatureSet(true));

        $uuid = $factory->fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     */
    public function testIsValidGoodVersion1()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidGoodVersion2()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57d-21e1-9b21-0800200c9a66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidGoodVersion3()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57d-31e1-9b21-0800200c9a66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidGoodVersion4()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57d-41e1-9b21-0800200c9a66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidGoodVersion5()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57d-51e1-9b21-0800200c9a66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidGoodUpperCase()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('FF6F8CB0-C57D-11E1-9B21-0800200C9A66');
        $this->assertTrue($valid);
    }

    /**
     */
    public function testIsValidBadHex()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('zf6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertFalse($valid);
    }

    /**
     */
    public function testIsValidTooShort1()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('3f6f8cb0-c57d-11e1-9b21-0800200c9a6');
        $this->assertFalse($valid);
    }

    /**
     */
    public function testIsValidTooShort2()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('af6f8cb-c57d-11e1-9b21-0800200c9a66');
        $this->assertFalse($valid);
    }

    /**
     */
    public function testIsValidNoDashes()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('af6f8cb0c57d11e19b210800200c9a66');
        $this->assertFalse($valid);
    }

    /**
     */
    public function testIsValidTooLong()
    {
        $factory = new UuidFactory();
        $valid = $factory->isValid('ff6f8cb0-c57da-51e1-9b21-0800200c9a66');
        $this->assertFalse($valid);
    }
}
