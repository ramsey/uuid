<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use Mockery;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Test\TestCase;
use RuntimeException;

class GuidBuilderTest extends TestCase
{
    public function testBuildThrowsException(): void
    {
        $codec = Mockery::mock(CodecInterface::class);

        $builder = Mockery::mock(GuidBuilder::class);
        $builder->shouldAllowMockingProtectedMethods();
        $builder->shouldReceive('buildFields')->andThrow(
            RuntimeException::class,
            'exception thrown'
        );
        $builder->shouldReceive('build')->passthru();

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage('exception thrown');

        $builder->build($codec, 'foobar');
    }
}
