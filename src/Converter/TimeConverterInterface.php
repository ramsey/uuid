<?php

namespace Ramsey\Uuid\Converter;

interface TimeConverterInterface
{

    /**
     * Calculates low, mid, high time array.
     *
     * @param string $seconds
     * @param string $microSeconds
     * @return string[] An array guaranteed to contain 'low', 'mid', and 'high' keys.
     */
    public function calculateTime($seconds, $microSeconds);
}
