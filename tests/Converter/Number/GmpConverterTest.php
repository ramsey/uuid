<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Converter\Number;

use AspectMock\Test as AspectMock;
use InvalidArgumentException;
use Ramsey\Uuid\Converter\Number\GmpConverter;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Test\TestCase;

class GmpConverterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testFromHexThrowsExceptionWhenGmpExtensionNotPresent(): void
    {
        $extensionLoaded = AspectMock::func(
            'Ramsey\Uuid\Converter',
            'extension_loaded',
            false
        );

        $converter = new GmpConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('ext-gmp must be present to use this converter');

        $converter->fromHex('abcd');
        $extensionLoaded->verifyInvokedOnce(['gmp']);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testToHexThrowsExceptionWhenGmpExtensionNotPresent(): void
    {
        $extensionLoaded = AspectMock::func(
            'Ramsey\Uuid\Converter',
            'extension_loaded',
            false
        );

        $converter = new GmpConverter();

        $this->expectException(UnsatisfiedDependencyException::class);
        $this->expectExceptionMessage('ext-gmp must be present to use this converter');

        $converter->toHex('1234');
        $extensionLoaded->verifyInvokedOnce(['gmp']);
    }

    public function testFromHexThrowsExceptionWhenStringDoesNotContainOnlyHexadecimalCharacters(): void
    {
        $converter = new GmpConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$hex must contain only hexadecimal characters');

        $converter->fromHex('123.34');
    }

    public function testToHexThrowsExceptionWhenStringDoesNotContainOnlyDigits(): void
    {
        $converter = new GmpConverter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$number must contain only digits');

        $converter->toHex('123.34');
    }

    public function testFromHex(): void
    {
        $converter = new GmpConverter();

        $this->assertSame('65535', $converter->fromHex('ffff'));
    }

    public function testToHex(): void
    {
        $converter = new GmpConverter();

        $this->assertSame('ffff', $converter->toHex('65535'));
    }
}
