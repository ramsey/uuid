<?php

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;

class BigNumberTimeConverter implements TimeConverterInterface
{
    public function calculateTime($seconds, $microSeconds)
    {
        $uuidTime = new \Moontoast\Math\BigNumber('0');

        $sec = new \Moontoast\Math\BigNumber($seconds);
        $sec->multiply('10000000');

        $usec = new \Moontoast\Math\BigNumber($microSeconds);
        $usec->multiply('10');

        $uuidTime->add($sec)
            ->add($usec)
            ->add('122192928000000000');

        $uuidTimeHex = sprintf('%016s', $uuidTime->convertToBase(16));

        return array(
            'low' => substr($uuidTimeHex, 8),
            'mid' => substr($uuidTimeHex, 4, 4),
            'hi' => substr($uuidTimeHex, 0, 4),
        );
    }
}
