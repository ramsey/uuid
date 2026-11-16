<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Guid;

use JMac\Testing\Double;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Test\TestCase;

class GuidBuilderTest extends TestCase
{
    public function testBuildThrowsException(): void
    {
        $codec = Double::for(CodecInterface::class);

        $builder = new GuidBuilder(
            Double::for(NumberConverterInterface::class),
            Double::for(TimeConverterInterface::class),
        );

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage('The byte string must be 16 bytes long; received 6 bytes');

        $builder->build($codec, 'foobar');
    }
}
