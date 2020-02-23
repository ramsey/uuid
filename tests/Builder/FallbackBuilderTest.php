<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Builder;

use Mockery;
use Ramsey\Uuid\Builder\BuilderCollection;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Exception\BuilderNotFoundException;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
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

        $fallbackBuilder = new FallbackBuilder(new BuilderCollection([$builder1, $builder2, $builder3]));

        $this->expectException(BuilderNotFoundException::class);
        $this->expectExceptionMessage(
            'Could not find a suitable builder for the provided codec and fields'
        );

        $fallbackBuilder->build($codec, $bytes);
    }
}
