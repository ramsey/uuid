<?php

namespace Rhumsaa\Uuid\Builder;

use Rhumsaa\Uuid\UuidBuilder;
use Rhumsaa\Uuid\CodecInterface;
use Rhumsaa\Uuid\DegradedUuid;
use Rhumsaa\Uuid\Converter\NumberConverterInterface;

class DegradedUuidBuilder implements UuidBuilder
{

    private $converter;

    public function __construct(NumberConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function build(CodecInterface $codec, array $fields)
    {
        return new DegradedUuid($fields, $this->converter, $codec);
    }
}
