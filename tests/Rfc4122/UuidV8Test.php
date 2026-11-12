<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use JMac\Testing\Double;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV8;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;

class UuidV8Test extends TestCase
{
    #[DataProvider('provideTestVersions')]
    public function testConstructorThrowsExceptionWhenFieldsAreNotValidForType(Version $version): void
    {
        $fields = Double::for(FieldsInterface::class);
        $fields->allows('getVersion')->returns($version);

        $numberConverter = Double::for(NumberConverterInterface::class);
        $codec = Double::for(CodecInterface::class);
        $timeConverter = Double::for(TimeConverterInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fields used to create a UuidV8 must represent a version 8 (custom format) UUID');

        new UuidV8($fields, $numberConverter, $codec, $timeConverter);
    }

    /**
     * @return array<array{version: Version}>
     */
    public static function provideTestVersions(): array
    {
        return [
            ['version' => Version::Time],
            ['version' => Version::DceSecurity],
            ['version' => Version::HashMd5],
            ['version' => Version::Random],
            ['version' => Version::HashSha1],
            ['version' => Version::ReorderedTime],
            ['version' => Version::UnixTime],
        ];
    }
}
