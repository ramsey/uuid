<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Number;

use AspectMock\Test as AspectMock;
use Ramsey\Uuid\Converter\Number\BigNumberConverter;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

class BigNumberConverterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testFromHexThrowsExceptionWhenMoontoastMathNotPresent(): void
    {
        $classExists = AspectMock::func(
            'Ramsey\Uuid\Converter',
            'class_exists',
            false
        );

        $converter = new BigNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('moontoast/math must be present to use this converter');

        $converter->fromHex('abcd');
        $classExists->verifyInvokedOnce(['Moontoast\Math\BigNumber']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testToHexThrowsExceptionWhenMoontoastMathNotPresent(): void
    {
        $classExists = AspectMock::func(
            'Ramsey\Uuid\Converter',
            'class_exists',
            false
        );

        $converter = new BigNumberConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('moontoast/math must be present to use this converter');

        $converter->toHex('1234');
        $classExists->verifyInvokedOnce(['Moontoast\Math\BigNumber']);
    }

    public function testFromHexThrowsExceptionWhenStringDoesNotContainOnlyHexadecimalCharacters(): void
    {
        $converter = new BigNumberConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$hex must contain only hexadecimal characters');

        $converter->fromHex('123.34');
    }

    public function testToHexThrowsExceptionWhenStringDoesNotContainOnlyDigits(): void
    {
        $converter = new BigNumberConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$number must contain only digits');

        $converter->toHex('123.34');
    }

    public function testFromHex(): void
    {
        $converter = new BigNumberConverter();

        $this->assertSame('65535', $converter->fromHex('ffff'));
    }

    public function testToHex(): void
    {
        $converter = new BigNumberConverter();

        $this->assertSame('ffff', $converter->toHex('65535'));
    }
}
