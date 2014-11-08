<?php

namespace Rhumsaa\Uuid;

interface Codec
{
    /**
     * @return string
     */
    public function encode(UuidInterface $uuid);

    /**
     * @return string
     */
    public function encodeBinary(UuidInterface $uuid);

    /**
     * @return callable
     */
    public function decode($encodedUuid);

    /**
     * @param string $bytes
     */
    public function decodeBytes($bytes);
}
