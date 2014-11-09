<?php

namespace Rhumsaa\Uuid\Generator;

use RandomLib\Generator;
use RandomLib\Factory;
use Rhumsaa\Uuid\RandomGeneratorInterface;

class RandomLibAdapter implements RandomGeneratorInterface
{
    private $generator;

    public function __construct(Generator $generator = null)
    {
        $this->generator = $generator;

        if ($this->generator == null) {
            $factory = new Factory();

            $this->generator = $factory->getLowStrengthGenerator();
        }
    }

    public function generate($length)
    {
        return $this->generator->generate($length);
    }
}
