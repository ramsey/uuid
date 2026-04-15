<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Builder;

use Mockery;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Exception\BuilderNotFoundException;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV2;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\Test\TestCase;

class FallbackBuilderTest extends TestCase
{
    public function testBuildThrowsExceptionAfterAllConfiguredBuildersHaveErrored(): void
    {
        $codec = Mockery::mock(CodecInterface::class);
        $bytes = 'foobar';

        $builder1 = Mockery::mock(UuidBuilderInterface::class);
        $builder1
            ->shouldReceive('build')
            ->once()
            ->with($codec, $bytes)
            ->andThrow(UnableToBuildUuidException::class);

        $builder2 = Mockery::mock(UuidBuilderInterface::class);
        $builder2
            ->shouldReceive('build')
            ->once()
            ->with($codec, $bytes)
            ->andThrow(UnableToBuildUuidException::class);

        $builder3 = Mockery::mock(UuidBuilderInterface::class);
        $builder3
            ->shouldReceive('build')
            ->once()
            ->with($codec, $bytes)
            ->andThrow(UnableToBuildUuidException::class);

        $fallbackBuilder = new FallbackBuilder([$builder1, $builder2, $builder3]);

        $this->expectException(BuilderNotFoundException::class);
        $this->expectExceptionMessage(
            'Could not find a suitable builder for the provided codec and fields'
        );

        $fallbackBuilder->build($codec, $bytes);
    }

    /**
     * @dataProvider provideBytes
     */
    public function testSerializationOfBuilderCollection(string $bytes): void
    {
        $calculator = new BrickMathCalculator();
        $genericNumberConverter = new GenericNumberConverter($calculator);
        $genericTimeConverter = new GenericTimeConverter($calculator);
        $phpTimeConverter = new PhpTimeConverter($calculator, $genericTimeConverter);

        // Use the GenericTimeConverter.
        $guidBuilder = new GuidBuilder($genericNumberConverter, $genericTimeConverter);
        $rfc4122Builder = new Rfc4122UuidBuilder($genericNumberConverter, $genericTimeConverter);
        $nonstandardBuilder = new NonstandardUuidBuilder($genericNumberConverter, $genericTimeConverter);

        // Use the PhpTimeConverter.
        $guidBuilder2 = new GuidBuilder($genericNumberConverter, $phpTimeConverter);
        $rfc4122Builder2 = new Rfc4122UuidBuilder($genericNumberConverter, $phpTimeConverter);
        $nonstandardBuilder2 = new NonstandardUuidBuilder($genericNumberConverter, $phpTimeConverter);

        /** @var list<UuidBuilderInterface> $unserializedBuilderCollection */
        $unserializedBuilderCollection = unserialize(serialize([
            $guidBuilder,
            $guidBuilder2,
            $rfc4122Builder,
            $rfc4122Builder2,
            $nonstandardBuilder,
            $nonstandardBuilder2,
        ]));

        foreach ($unserializedBuilderCollection as $builder) {
            $codec = new StringCodec($builder);

            $this->assertInstanceOf(UuidBuilderInterface::class, $builder);

            try {
                $uuid = $builder->build($codec, $bytes);

                if (($uuid instanceof UuidV1) || ($uuid instanceof UuidV2) || ($uuid instanceof UuidV6)) {
                    $this->assertNotEmpty($uuid->getDateTime()->format('r'));
                }
            } catch (UnableToBuildUuidException $exception) {
                switch ($exception->getMessage()) {
                    case 'The byte string received does not contain a valid version':
                    case 'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) variant':
                    case 'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) '
                        . 'or Microsoft Corporation variants':
                        // This is expected; ignoring.
                        break;
                    default:
                        throw $exception;
                }
            }
        }
    }

    /**
     * @return array<array{bytes: string}>
     */
    public function provideBytes(): array
    {
        return [
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1110b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1111b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1112b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1113b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1114b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1115b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1116b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e1117b210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e111eb210800200c9a66'),
            ],
            [
                // GUID bytes
                'bytes' => (string) hex2bin('b08c6fff7dc5e111fb210800200c9a66'),
            ],
            [
                // Version 1 bytes
                'bytes' => (string) hex2bin('ff6f8cb0c57d11e19b210800200c9a66'),
            ],
            [
                // Version 2 bytes
                'bytes' => (string) hex2bin('000001f55cde21ea84000242ac130003'),
            ],
            [
                // Version 3 bytes
                'bytes' => (string) hex2bin('ff6f8cb0c57d31e1bb210800200c9a66'),
            ],
            [
                // Version 4 bytes
                'bytes' => (string) hex2bin('ff6f8cb0c57d41e1ab210800200c9a66'),
            ],
            [
                // Version 5 bytes
                'bytes' => (string) hex2bin('ff6f8cb0c57d51e18b210800200c9a66'),
            ],
            [
                // Version 6 bytes
                'bytes' => (string) hex2bin('ff6f8cb0c57d61e18b210800200c9a66'),
            ],
            [
                // NIL bytes
                'bytes' => (string) hex2bin('00000000000000000000000000000000'),
            ],
        ];
    }
}
