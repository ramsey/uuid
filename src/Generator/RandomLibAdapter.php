<?php

namespace Rhumsaa\Uuid;

use RandomLib\Generator;
use RandomLib\Factory;

class RandomLibAdapter implements RandomGenerator
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
