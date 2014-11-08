<?php

namespace Rhumsaa\Uuid;

interface Codec
{
    public function encode(UuidInterface $uuid);

    public function encodeBinary(UuidInterface $uuid);

    public function decode($encodedUuid);

    public function decodeBytes($bytes);
}
