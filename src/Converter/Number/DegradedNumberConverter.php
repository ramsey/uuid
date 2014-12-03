<?php

namespace Rhumsaa\Uuid\Converter\Number;

use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use Rhumsaa\Uuid\Converter\NumberConverterInterface;

class DegradedNumberConverter implements NumberConverterInterface
{
    public function fromHex($hex)
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' without support for large '
            . 'integers, since integer is an unsigned '
            . '128-bit integer; Moontoast\Math\BigNumber is required.'
            . '; consider calling an hex based method instead'
        );
    }

    public function toHex($integer)
    {
        throw new UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' without support for large '
            . 'integers, since integer is an unsigned '
            . '128-bit integer; Moontoast\Math\BigNumber is required. '
        );
    }
}
