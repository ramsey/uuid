<?php

namespace Rhumsaa\Uuid;

interface UuidBuilder
{
    /**
     * @return Uuid
     */
    public function build(CodecInterface $codec, array $fields);
}
