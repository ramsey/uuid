<?php

namespace Ramsey\Uuid\Test\Validator;

use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\Validator;

/**
 * @coversDefaultClass Ramsey\Uuid\Validator\Validator
 */
class ValidatorTest extends TestCase
{
    private $validator = null;

    public function setUp()
    {
        // Disable calls to the constructor, but do not override any methods
        $this->validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodVersion1()
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-11e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodVersion2()
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-21e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodVersion3()
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-31e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodVersion4()
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-41e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodVersion5()
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-51e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateGoodUpperCase()
    {
        $this->assertTrue($this->validator->validate('FF6F8CB0-C57D-11E1-9B21-0800200C9A66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateBadHex()
    {
        $this->assertFalse($this->validator->validate('zf6f8cb0-c57d-11e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateTooShort1()
    {
        $this->assertFalse($this->validator->validate('3f6f8cb0-c57d-11e1-9b21-0800200c9a6'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateTooShort2()
    {
        $this->assertFalse($this->validator->validate('af6f8cb-c57d-11e1-9b21-0800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateNoDashes()
    {
        $this->assertFalse($this->validator->validate('af6f8cb0c57d11e19b210800200c9a66'));
    }

    /**
     * @covers ::validate
     */
    public function testValidateTooLong()
    {
        $this->assertFalse($this->validator->validate('ff6f8cb0-c57da-51e1-9b21-0800200c9a66'));
    }
}
