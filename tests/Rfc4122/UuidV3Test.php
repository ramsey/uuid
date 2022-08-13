<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV3;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;

class UuidV3Test extends TestCase
{
    /**
     * @dataProvider provideTestVersions
     */
    public function testConstructorThrowsExceptionWhenFieldsAreNotValidForType(Version $version): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'getVersion' => $version,
        ]);

        $numberConverter = Mockery::mock(NumberConverterInterface::class);
        $codec = Mockery::mock(CodecInterface::class);
        $timeConverter = Mockery::mock(TimeConverterInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Fields used to create a UuidV3 must represent a '
            . 'version 3 (name-based, MD5-hashed) UUID'
        );

        new UuidV3($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideTestVersions(): array
    {
        return [
            ['version' => Version::Time],
            ['version' => Version::DceSecurity],
            ['version' => Version::Random],
            ['version' => Version::HashSha1],
            ['version' => Version::ReorderedTime],
            ['version' => Version::UnixTime],
            ['version' => Version::Custom],
        ];
    }
}
