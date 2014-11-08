<?php

namespace Rhumsaa\Uuid;

interface UuidBuilder
{
    /**
     * @return Uuid
     */
    public function build(Codec $codec, array $fields);
}
