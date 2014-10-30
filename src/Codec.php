<?php

namespace Rhumsaa\Uuid;

interface Codec
{
    public function encode(UuidInterface $plainUuid);

    public function decode($encodedUuid);

    public function decodeBytes($bytes);
}
