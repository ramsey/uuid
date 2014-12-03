<?php

namespace Rhumsaa\Uuid\Converter\Time;

use Rhumsaa\Uuid\Converter\TimeConverterInterface;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class DegradedTimeConverter implements TimeConverterInterface
{
    public function calculateTime($seconds, $microSeconds)
    {
        throw new UnsatisfiedDependencyException(
            'When calling ' . __METHOD__ . ' on a 32-bit system, '
            . 'Moontoast\Math\BigNumber must be present in order '
            . 'to generate version 1 UUIDs'
        );
    }
}
