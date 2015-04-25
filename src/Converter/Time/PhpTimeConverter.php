<?php

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;

class PhpTimeConverter implements TimeConverterInterface
{
    public function calculateTime($seconds, $microSeconds)
    {
        // 0x01b21dd213814000 is the number of 100-ns intervals between the
        // UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
        $uuidTime = ($seconds * 10000000) + ($microSeconds * 10) + 0x01b21dd213814000;

        return array(
            'low' => sprintf('%08x', $uuidTime & 0xffffffff),
            'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
            'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
        );
    }
}
