<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test\Nonstandard;

use JMac\Testing\Double;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Nonstandard\UuidBuilder;
use Ramsey\Uuid\Test\TestCase;

class UuidBuilderTest extends TestCase
{
    public function testBuildThrowsException(): void
    {
        $codec = Double::for(CodecInterface::class);

        $builder = new UuidBuilder(
            Double::for(NumberConverterInterface::class),
            Double::for(TimeConverterInterface::class),
        );

        $this->expectException(UnableToBuildUuidException::class);
        $this->expectExceptionMessage('The byte string must be 16 bytes long; received 6 bytes');

        $builder->build($codec, 'foobar');
    }
}
