<?php

namespace Rhumsaa\Uuid;

class UnsatisfiedNumberConverter extends BigNumberConverter
{
    public function fromHex($hex)
    {
        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' without support for large '
            . 'integers, since integer is an unsigned '
            . '128-bit integer; Moontoast\Math\BigNumber is required.'
            . '; consider calling an hex based method instead'
        );
    }

    public function toHex($integer)
    {
        throw new Exception\UnsatisfiedDependencyException(
            'Cannot call ' . __METHOD__ . ' without support for large '
            . 'integers, since integer is an unsigned '
            . '128-bit integer; Moontoast\Math\BigNumber is required. '
        );
    }
}
