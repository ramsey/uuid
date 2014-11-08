<?php

namespace Rhumsaa\Uuid;

interface UuidBuilder
{
    public function build(Codec $codec, array $fields);
}
