<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidV6;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\UuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Rfc4122\UuidV3;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Rfc4122\UuidV5;
use Ramsey\Uuid\Rfc4122\Version;
use Ramsey\Uuid\Test\TestCase;

use function hex2bin;
use function str_replace;

class UuidBuilderTest extends TestCase
{
    /**
     * @param class-string $expectedClass
     *
     * @dataProvider provideBuildTestValues
     */
    public function testBuild(string $uuid, string $expectedClass, Version $expectedVersion): void
    {
        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin(str_replace('-', '', $uuid));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $builder = new UuidBuilder($numberConverter, $timeConverter);
        $codec = new StringCodec($builder);

        $result = $builder->build($codec, $bytes);

        /** @var Fields $fields */
        $fields = $result->getFields();

        $this->assertInstanceOf($expectedClass, $result);
        $this->assertSame($expectedVersion, $fields->getVersion());
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function provideBuildTestValues(): array
    {
        return [
            [
                'uuid' => 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66',
                'expectedClass' => UuidV1::class,
                'expectedVersion' => Version::Time,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-21e1-9b21-0800200c9a66',
                'expectedClass' => UuidV2::class,
                'expectedVersion' => Version::DceSecurity,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-31e1-9b21-0800200c9a66',
                'expectedClass' => UuidV3::class,
                'expectedVersion' => Version::HashMd5,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-41e1-9b21-0800200c9a66',
                'expectedClass' => UuidV4::class,
                'expectedVersion' => Version::Random,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-51e1-9b21-0800200c9a66',
                'expectedClass' => UuidV5::class,
                'expectedVersion' => Version::HashSha1,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-61e1-9b21-0800200c9a66',
                'expectedClass' => UuidV6::class,
                'expectedVersion' => Version::ReorderedTime,
            ],
        ];
    }

    public function testBuildThrowsUnableToBuildException(): void
    {
        /** @var non-empty-string $bytes */
        $bytes = (string) hex2bin(str_replace('-', '', 'ff6f8cb0-c57d-51e1-9b21-0800200c9a'));

        $calculator = new BrickMathCalculator();
        $numberConverter = new GenericNumberConverter($calculator);
        $timeConverter = new GenericTimeConverter($calculator);
        $builder = new UuidBuilder($numberConverter, $timeConverter);
        $codec = new StringCodec($builder);

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage(
            'The byte string must be 16 bytes long; received 15 bytes'
        );

        $builder->build($codec, $bytes);
    }
}
