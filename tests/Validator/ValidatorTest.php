<?php

namespace Ramsey\Uuid\Test\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Test\TestCase;
use Ramsey\Uuid\Validator\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @var MockObject & Validator
     */
    private $validator = null;

    public function setUp(): void
    {
        // Disable calls to the constructor, but do not override any methods
        $this->validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
    }

    public function testValidateGoodVersion1(): void
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-11e1-9b21-0800200c9a66'));
    }

    public function testValidateGoodVersion2(): void
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-21e1-9b21-0800200c9a66'));
    }

    public function testValidateGoodVersion3(): void
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-31e1-9b21-0800200c9a66'));
    }

    public function testValidateGoodVersion4(): void
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-41e1-9b21-0800200c9a66'));
    }

    public function testValidateGoodVersion5(): void
    {
        $this->assertTrue($this->validator->validate('ff6f8cb0-c57d-51e1-9b21-0800200c9a66'));
    }

    public function testValidateGoodUpperCase(): void
    {
        $this->assertTrue($this->validator->validate('FF6F8CB0-C57D-11E1-9B21-0800200C9A66'));
    }

    public function testValidateBadHex(): void
    {
        $this->assertFalse($this->validator->validate('zf6f8cb0-c57d-11e1-9b21-0800200c9a66'));
    }

    public function testValidateTooShort1(): void
    {
        $this->assertFalse($this->validator->validate('3f6f8cb0-c57d-11e1-9b21-0800200c9a6'));
    }

    public function testValidateTooShort2(): void
    {
        $this->assertFalse($this->validator->validate('af6f8cb-c57d-11e1-9b21-0800200c9a66'));
    }

    public function testValidateNoDashes(): void
    {
        $this->assertFalse($this->validator->validate('af6f8cb0c57d11e19b210800200c9a66'));
    }

    public function testValidateTooLong(): void
    {
        $this->assertFalse($this->validator->validate('ff6f8cb0-c57da-51e1-9b21-0800200c9a66'));
    }
}
