<?php

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\UuidBuilder;
use Ramsey\Uuid\CodecInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Converter\NumberConverterInterface;

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
