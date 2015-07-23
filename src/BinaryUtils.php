<?php

namespace Ramsey\Uuid;

class BinaryUtils
{
    /**
     * @param $clockSeqHi
     * @return int
     */
    public static function applyVariant($clockSeqHi)
    {
        // Set the variant to RFC 4122
        $clockSeqHi = $clockSeqHi & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        return $clockSeqHi;
    }

    /**
     * @param string $timeHi
     * @param integer $version
     * @return int|string
     */
    public static function applyVersion($timeHi, $version)
    {
        $timeHi = hexdec($timeHi) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= $version << 12;

        return $timeHi;
    }


}
