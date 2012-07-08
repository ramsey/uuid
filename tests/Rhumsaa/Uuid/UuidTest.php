<?php
namespace Rhumsaa\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Rhumsaa\Uuid\Uuid::fromString
     * @covers Rhumsaa\Uuid\Uuid::__construct
     */
    public function testFromString()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::fromString
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid UUID string:
     */
    public function testFromStringWithInvalidUuidString()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21');
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getClockSeqHiAndReserved
     */
    public function testGetClockSeqHiAndReserved()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(155, $uuid->getClockSeqHiAndReserved());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getClockSeqLow
     */
    public function testGetClockSeqLow()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(33, $uuid->getClockSeqLow());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getClockSequence
     */
    public function testGetClockSequence()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(6945, $uuid->getClockSequence());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getDateTime
     */
    public function testGetDateTime()
    {
        // Check a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals('Wed, 04 Jul 2012 02:14:34 +0000', $uuid->getDateTime()->format('r'));

        // Check an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals('Sun, 16 Oct 1582 16:34:04 +0000', $uuid->getDateTime()->format('r'));
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getDateTime
     * @expectedException Rhumsaa\Uuid\UnsupportedOperationException
     * @expectedExceptionMessage Not a time-based UUID
     */
    public function testGetDateTimeFromNonVersion1Uuid()
    {
        // Using a version 4 UUID to test
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');
        $date = $uuid->getDateTime();
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getLeastSignificantBits
     */
    public function testGetLeastSignificantBits()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(-7268519526968550810, $uuid->getLeastSignificantBits());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getMostSignificantBits
     */
    public function testGetMostSignificantBits()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(-40659180767604255, $uuid->getMostSignificantBits());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getNode
     */
    public function testGetNode()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(8796630719078, $uuid->getNode());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getTimeHiAndVersion
     */
    public function testGetTimeHiAndVersion()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(4577, $uuid->getTimeHiAndVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getTimeLow
     */
    public function testGetTimeLow()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(4285500592, $uuid->getTimeLow());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getTimeMid
     */
    public function testGetTimeMid()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(50557, $uuid->getTimeMid());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getTimestamp
     */
    public function testGetTimestamp()
    {
        // Check for a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(135606608744910000, $uuid->getTimestamp());

        // Check for an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertEquals(1460440000000, $uuid->getTimestamp());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getTimestamp
     * @expectedException Rhumsaa\Uuid\UnsupportedOperationException
     * @expectedExceptionMessage Not a time-based UUID
     */
    public function testGetTimestampFromNonVersion1Uuid()
    {
        // Using a version 4 UUID to test
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');
        $ts = $uuid->getTimestamp();
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getUrn
     */
    public function testGetUrn()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->getUrn());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVariant
     */
    public function testGetVariantForReservedNcs()
    {
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-0b21-0800200c9a66');
        $this->assertEquals(0, $uuid1->getVariant());

        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-1b21-0800200c9a66');
        $this->assertEquals(0, $uuid2->getVariant());

        $uuid3 = Uuid::fromString('ff6f8cb0-c57d-11e1-2b21-0800200c9a66');
        $this->assertEquals(0, $uuid3->getVariant());

        $uuid4 = Uuid::fromString('ff6f8cb0-c57d-11e1-3b21-0800200c9a66');
        $this->assertEquals(0, $uuid4->getVariant());

        $uuid5 = Uuid::fromString('ff6f8cb0-c57d-11e1-4b21-0800200c9a66');
        $this->assertEquals(0, $uuid5->getVariant());

        $uuid6 = Uuid::fromString('ff6f8cb0-c57d-11e1-5b21-0800200c9a66');
        $this->assertEquals(0, $uuid6->getVariant());

        $uuid7 = Uuid::fromString('ff6f8cb0-c57d-11e1-6b21-0800200c9a66');
        $this->assertEquals(0, $uuid7->getVariant());

        $uuid8 = Uuid::fromString('ff6f8cb0-c57d-11e1-7b21-0800200c9a66');
        $this->assertEquals(0, $uuid8->getVariant());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVariant
     */
    public function testGetVariantForRfc4122()
    {
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-8b21-0800200c9a66');
        $this->assertEquals(2, $uuid1->getVariant());

        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(2, $uuid2->getVariant());

        $uuid3 = Uuid::fromString('ff6f8cb0-c57d-11e1-ab21-0800200c9a66');
        $this->assertEquals(2, $uuid3->getVariant());

        $uuid4 = Uuid::fromString('ff6f8cb0-c57d-11e1-bb21-0800200c9a66');
        $this->assertEquals(2, $uuid4->getVariant());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVariant
     */
    public function testGetVariantForReservedMicrosoft()
    {
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-cb21-0800200c9a66');
        $this->assertEquals(6, $uuid1->getVariant());

        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-db21-0800200c9a66');
        $this->assertEquals(6, $uuid2->getVariant());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVariant
     */
    public function testGetVariantForReservedFuture()
    {
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-eb21-0800200c9a66');
        $this->assertEquals(7, $uuid1->getVariant());

        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-fb21-0800200c9a66');
        $this->assertEquals(7, $uuid2->getVariant());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVersion
     */
    public function testGetVersionForVersion1()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(1, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVersion
     */
    public function testGetVersionForVersion2()
    {
        $this->markTestIncomplete('This test is not implemented yet');
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVersion
     */
    public function testGetVersionForVersion3()
    {
        $uuid = Uuid::fromString('6fa459ea-ee8a-3ca4-894e-db77e160355e');
        $this->assertEquals(3, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVersion
     */
    public function testGetVersionForVersion4()
    {
        $uuid = Uuid::fromString('6fabf0bc-603a-42f2-925b-d9f779bd0032');
        $this->assertEquals(4, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getVersion
     */
    public function testGetVersionForVersion5()
    {
        $uuid = Uuid::fromString('886313e1-3b8a-5372-9b90-0c9aee199e5d');
        $this->assertEquals(5, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::toString
     * @covers Rhumsaa\Uuid\Uuid::__toString
     * @covers Rhumsaa\Uuid\Uuid::digits
     */
    public function testToString()
    {
        // Check with a recent date
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', sprintf('%s', $uuid));

        // Check with an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertEquals('0901e600-0154-1000-9b21-0800200c9a66', $uuid->toString());
        $this->assertEquals('0901e600-0154-1000-9b21-0800200c9a66', sprintf('%s', $uuid));
    }

    /**
     * covers Rhumsaa\Uuid\Uuid::uuid1
     */
    public function testUuid1()
    {
        $uuid = Uuid::uuid1();
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
    }

    /**
     * covers Rhumsaa\Uuid\Uuid::uuid1
     */
    public function testUuid1WithNodeAndClockSequence()
    {
        $uuid = Uuid::uuid1(0x0800200c9a66, 0x1669);
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
        $this->assertEquals(5737, $uuid->getClockSequence());
        $this->assertEquals(8796630719078, $uuid->getNode());
        $this->assertEquals('9669-0800200c9a66', substr($uuid->toString(), 19));
    }
}

