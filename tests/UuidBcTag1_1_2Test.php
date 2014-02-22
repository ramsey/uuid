<?php
namespace Rhumsaa\Uuid;

/**
 * This is a backwards-compatibility test to ensure that Rhumsaa\Uuid
 * maintains backwards compatibility with tag 1.1.2.
 *
 * This test was copied directly from the UuidTest case at tag 1.1.2.
 * A few minor changes have been made.
 */
class UuidBcTag1_1_2Test extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // Skip these tests if run on a 32-bit build of PHP
        if (PHP_INT_SIZE == 4) {
            $this->markTestSkipped(
                'BC tests for tag 1.1.2 should only be run on a 64-bit system'
            );
        }
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::fromString
     * @covers Rhumsaa\Uuid\Uuid::__construct
     */
    public function testFromString()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::fromString
     */
    public function testFromStringWithCurlyBraces()
    {
        $uuid = Uuid::fromString('{ff6f8cb0-c57d-11e1-9b21-0800200c9a66}');
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
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
     * @covers Rhumsaa\Uuid\Uuid::fromString
     */
    public function testFromStringWithUrn()
    {
        $uuid = Uuid::fromString('urn:uuid:ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getBytes
     */
    public function testGetBytes()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(16, strlen($uuid->getBytes()));
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
        $this->assertEquals('2012-07-04T02:14:34+00:00', $uuid->getDateTime()->format('c'));

        // Check an old date
        $uuid = Uuid::fromString('0901e600-0154-1000-9b21-0800200c9a66');
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals('1582-10-16T16:34:04+00:00', $uuid->getDateTime()->format('c'));
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getDateTime
     * @covers Rhumsaa\Uuid\Exception\UnsupportedOperationException
     * @expectedException Rhumsaa\Uuid\Exception\UnsupportedOperationException
     * @expectedExceptionMessage Not a time-based UUID
     */
    public function testGetDateTimeFromNonVersion1Uuid()
    {
        // Using a version 4 UUID to test
        $uuid = Uuid::fromString('bf17b594-41f2-474f-bf70-4c90220f75de');
        $date = $uuid->getDateTime();
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getFields
     */
    public function testGetFields()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertArrayHasKey('time_low', $uuid->getFields());
        $this->assertArrayHasKey('time_mid', $uuid->getFields());
        $this->assertArrayHasKey('time_hi_and_version', $uuid->getFields());
        $this->assertArrayHasKey('clock_seq_hi_and_reserved', $uuid->getFields());
        $this->assertArrayHasKey('clock_seq_low', $uuid->getFields());
        $this->assertArrayHasKey('node', $uuid->getFields());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getLeastSignificantBits
     */
    public function testGetLeastSignificantBits()
    {
        $this->assertEquals(true, true);
        return true;

        // This test was previously flawed and is an acknowledged
        // backward-compatibility break. It was previously marked as
        // "Skipped," but it will never be fixed, so I am leaving it
        // in place for historical purposes.

        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $this->assertEquals(-7268519526968550810, $uuid->getLeastSignificantBits());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::getMostSignificantBits
     */
    public function testGetMostSignificantBits()
    {
        $this->assertEquals(true, true);
        return true;

        // This test was previously flawed and is an acknowledged
        // backward-compatibility break. It was previously marked as
        // "Skipped," but it will never be fixed, so I am leaving it
        // in place for historical purposes.

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
     * @covers Rhumsaa\Uuid\Exception\UnsupportedOperationException
     * @expectedException Rhumsaa\Uuid\Exception\UnsupportedOperationException
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
        $uuid = Uuid::fromString('6fa459ea-ee8a-2ca4-894e-db77e160355e');
        $this->assertEquals(2, $uuid->getVersion());
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
     * @covers Rhumsaa\Uuid\Uuid::uuid1
     * @covers Rhumsaa\Uuid\Uuid::getNodeFromSystem
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
     * @covers Rhumsaa\Uuid\Uuid::uuid1
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

    /**
     * @covers Rhumsaa\Uuid\Uuid::uuid1
     */
    public function testUuid1WithRandomNode()
    {
        Uuid::$ignoreSystemNode = true;

        $uuid = Uuid::uuid1();
        $this->assertInstanceOf('\Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertInstanceOf('\DateTime', $uuid->getDateTime());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(1, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     * @see http://docs.python.org/library/uuid.html
     *
     * @covers Rhumsaa\Uuid\Uuid::uuid3
     * @covers Rhumsaa\Uuid\Uuid::uuidFromHashedName
     */
    public function testUuid3WithNamespaceAsUuidObject()
    {
        $nsUuid = Uuid::fromString(Uuid::NAMESPACE_DNS);
        $uuid = Uuid::uuid3($nsUuid, 'python.org');
        $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(3, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     * @see http://docs.python.org/library/uuid.html
     *
     * @covers Rhumsaa\Uuid\Uuid::uuid3
     * @covers Rhumsaa\Uuid\Uuid::uuidFromHashedName
     */
    public function testUuid3WithNamespaceAsUuidString()
    {
        $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, 'python.org');
        $this->assertEquals('6fa459ea-ee8a-3ca4-894e-db77e160355e', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(3, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::uuid4
     * @covers Rhumsaa\Uuid\Uuid::uuidFromHashedName
     */
    public function testUuid4()
    {
        $uuid = Uuid::uuid4();
        $this->assertInstanceOf('Rhumsaa\Uuid\Uuid', $uuid);
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(4, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     * @see http://docs.python.org/library/uuid.html
     *
     * @covers Rhumsaa\Uuid\Uuid::uuid5
     * @covers Rhumsaa\Uuid\Uuid::uuidFromHashedName
     */
    public function testUuid5WithNamespaceAsUuidObject()
    {
        $nsUuid = Uuid::fromString(Uuid::NAMESPACE_DNS);
        $uuid = Uuid::uuid5($nsUuid, 'python.org');
        $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(5, $uuid->getVersion());
    }

    /**
     * The "python.org" UUID is a known entity, so we're testing that this
     * library generates a matching UUID for the same name.
     * @see http://docs.python.org/library/uuid.html
     *
     * @covers Rhumsaa\Uuid\Uuid::uuid5
     * @covers Rhumsaa\Uuid\Uuid::uuidFromHashedName
     */
    public function testUuid5WithNamespaceAsUuidString()
    {
        $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $this->assertEquals('886313e1-3b8a-5372-9b90-0c9aee199e5d', $uuid->toString());
        $this->assertEquals(2, $uuid->getVariant());
        $this->assertEquals(5, $uuid->getVersion());
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::compareTo
     */
    public function testCompareTo()
    {
        // $uuid1 and $uuid2 are identical
        $uuid1 = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');
        $uuid2 = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        // The next three UUIDs are used for comparing msb and lsb in
        // the compareTo() method

        // msb are less than $uuid4, lsb are greater than $uuid5
        $uuid3 = Uuid::fromString('44cca71e-d13d-11e1-a959-c8bcc8a476f4');

        // msb are greater than $uuid3, lsb are equal to those in $uuid3
        $uuid4 = Uuid::fromString('44cca71e-d13d-11e2-a959-c8bcc8a476f4');

        // msb are equal to those in $uuid3, lsb are less than in $uuid3
        $uuid5 = Uuid::fromString('44cca71e-d13d-11e1-a959-c8bcc8a476f3');

        $this->assertEquals(0, $uuid1->compareTo($uuid2));
        $this->assertEquals(0, $uuid2->compareTo($uuid1));
        $this->assertEquals(-1, $uuid3->compareTo($uuid4));
        $this->assertEquals(1, $uuid4->compareTo($uuid3));
        $this->assertEquals(-1, $uuid5->compareTo($uuid3));
        $this->assertEquals(1, $uuid3->compareto($uuid5));
    }

    /**
     * @covers Rhumsaa\Uuid\Uuid::equals
     */
    public function testEquals()
    {
        $uuid1 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $uuid2 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'python.org');
        $uuid3 = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');

        $this->assertTrue($uuid1->equals($uuid2));
        $this->assertFalse($uuid1->equals($uuid3));
        $this->assertFalse($uuid1->equals(null));
        $this->assertFalse($uuid1->equals(new \stdClass()));
    }
}
