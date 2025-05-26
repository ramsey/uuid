<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Rfc4122;

use Mockery;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidV6 as NonstandardUuidV6;
use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\MaxUuid;
use Ramsey\Uuid\Rfc4122\NilUuid;
use Ramsey\Uuid\Rfc4122\UuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Rfc4122\UuidV3;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Rfc4122\UuidV5;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\Rfc4122\UuidV7;
use Ramsey\Uuid\Rfc4122\UuidV8;
use Ramsey\Uuid\Test\TestCase;

use function hex2bin;
use function str_replace;

class UuidBuilderTest extends TestCase
{
    /**
     * @param non-empty-string $uuid
     * @param class-string $expectedClass
     *
     * @dataProvider provideBuildTestValues
     */
    public function testBuild(string $uuid, string $expectedClass, ?int $expectedVersion): void
    {
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
     * @return array<array{uuid: non-empty-string, expectedClass: class-string, expectedVersion: int | null}>
     */
    public function provideBuildTestValues(): array
    {
        return [
            [
                'uuid' => '00000000-0000-0000-0000-000000000000',
                'expectedClass' => NilUuid::class,
                'expectedVersion' => null,
            ],
            [
                'uuid' => 'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'expectedClass' => MaxUuid::class,
                'expectedVersion' => null,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66',
                'expectedClass' => UuidV1::class,
                'expectedVersion' => 1,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-21e1-9b21-0800200c9a66',
                'expectedClass' => UuidV2::class,
                'expectedVersion' => 2,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-31e1-9b21-0800200c9a66',
                'expectedClass' => UuidV3::class,
                'expectedVersion' => 3,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-41e1-9b21-0800200c9a66',
                'expectedClass' => UuidV4::class,
                'expectedVersion' => 4,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-51e1-9b21-0800200c9a66',
                'expectedClass' => UuidV5::class,
                'expectedVersion' => 5,
            ],
            [
                'uuid' => 'ff6f8cb0-c57d-61e1-9b21-0800200c9a66',
                'expectedClass' => UuidV6::class,
                'expectedVersion' => 6,
            ],

            // The same UUIDv6 will also be of the expected class type
            // \Ramsey\Uuid\Nonstandard\UuidV6.
            [
                'uuid' => 'ff6f8cb0-c57d-61e1-9b21-0800200c9a66',
                'expectedClass' => NonstandardUuidV6::class,
                'expectedVersion' => 6,
            ],

            [
                'uuid' => 'ff6f8cb0-c57d-71e1-9b21-0800200c9a66',
                'expectedClass' => UuidV7::class,
                'expectedVersion' => 7,
            ],

            [
                'uuid' => 'ff6f8cb0-c57d-81e1-9b21-0800200c9a66',
                'expectedClass' => UuidV8::class,
                'expectedVersion' => 8,
            ],
        ];
    }

    public function testBuildThrowsUnableToBuildException(): void
    {
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

    public function testBuildThrowsUnableToBuildExceptionForIncorrectVersionFields(): void
    {
        $fields = Mockery::mock(FieldsInterface::class, [
            'isNil' => false,
            'isMax' => false,
            'getVersion' => 255,
        ]);

        $builder = Mockery::mock(UuidBuilder::class);
        $builder->shouldAllowMockingProtectedMethods();
        $builder->shouldReceive('buildFields')->andReturn($fields);
        $builder->shouldReceive('build')->passthru();

        $codec = Mockery::mock(StringCodec::class);

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage(
            'The UUID version in the given fields is not supported by this UUID builder'
        );

        $builder->build($codec, 'foobar');
    }
}
