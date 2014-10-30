<?php

namespace Rhumsaa\Uuid;

class BigNumberConverter
{
    public function fromHex($hex)
    {
        $number = \Moontoast\Math\BigNumber::baseConvert($hex, 16, 10);

        return new \Moontoast\Math\BigNumber($number);
    }
}
