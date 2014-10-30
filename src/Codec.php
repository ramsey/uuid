<?php

namespace Rhumsaa\Uuid;

interface Codec
{
    public function encode(UuidInterface $uuid);

    public function encodeBinary(UuidInterface $uuid);

    public function decode(BigNumberConverter $converter, $encodedUuid);

    public function decodeBytes(BigNumberConverter $converter, $bytes);
}
