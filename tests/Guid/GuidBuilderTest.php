<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use JMac\Testing\Double;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Test\TestCase;
use RuntimeException;

class GuidBuilderTest extends TestCase
{
    public function testBuildThrowsException(): void
    {
        $codec = Double::for(CodecInterface::class);

        $builder = Double::for(GuidBuilder::class)->passthru();
        $builder->expects('buildFields')->throws(new RuntimeException('exception thrown'));

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage('exception thrown');

        $builder->build($codec, 'foobar');
    }
}
