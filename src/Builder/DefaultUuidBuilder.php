<?php

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\CodecInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidBuilder;

class DefaultUuidBuilder implements UuidBuilder
{

    private $converter;

    public function __construct(NumberConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function build(CodecInterface $codec, array $fields)
    {
        return new Uuid($fields, $this->converter, $codec);
    }
}
