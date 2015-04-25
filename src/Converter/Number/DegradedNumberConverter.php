<?php

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Ramsey\Uuid\Converter\NumberConverterInterface;

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
