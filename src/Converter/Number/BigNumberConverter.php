<?php

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\NumberConverterInterface;

class BigNumberConverter implements NumberConverterInterface
{
    /**
     * @param string $hex
     */
    public function fromHex($hex)
    {
        $number = \Moontoast\Math\BigNumber::baseConvert($hex, 16, 10);

        return new \Moontoast\Math\BigNumber($number);
    }

    public function toHex($integer)
    {
        if (!$integer instanceof \Moontoast\Math\BigNumber) {
            $integer = new \Moontoast\Math\BigNumber($integer);
        }

        return \Moontoast\Math\BigNumber::baseConvert($integer, 10, 16);
    }
}
