<?php

namespace Ramsey\Uuid;

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
}
