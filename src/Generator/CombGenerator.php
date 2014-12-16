<?php

namespace Rhumsaa\Uuid\Generator;

use Rhumsaa\Uuid\RandomGeneratorInterface;

/**
 * Generator to be used for COMB sequential UUID's.
 *
 * Implementation is pretty naive in that in
 *
 * @author thibaud
 *
 */
class CombGenerator implements RandomGeneratorInterface
{

    private $randomGenerator;

    public function __construct(RandomGeneratorInterface $generator)
    {
        $this->randomGenerator = $generator;
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\RandomGeneratorInterface::generate()
     */
    public function generate($length)
    {
        if ($length < 6) {
            // Obviously, we cant COMB with less than 6 bytes
            throw new \InvalidArgumentException();
        }

        $hash = $this->randomGenerator->generate($length - 6);
        $lsbTime = hexdec(substr(dechex(round(microtime(true) * 10000, 0)), -12));

        return $hash . $lsbTime;
    }
}