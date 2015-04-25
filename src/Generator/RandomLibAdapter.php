<?php

namespace Ramsey\Uuid\Generator;

use RandomLib\Generator;
use RandomLib\Factory;
use Ramsey\Uuid\RandomGeneratorInterface;

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
