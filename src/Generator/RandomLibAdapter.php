<?php
/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Generator;

use RandomLib\Generator;
use RandomLib\Factory;

/**
 * RandomLibAdapter provides functionality to generate strings of random
 * binary data using the paragonie/random-lib library
 *
 * @link https://packagist.org/packages/paragonie/random-lib
 */
class RandomLibAdapter implements RandomGeneratorInterface
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * Constructs a `RandomLibAdapter` using a `RandomLib\Generator`
     *
     * By default, if no `Generator` is passed in, this creates a high-strength
     * generator to use when generating random binary data.
     *
     * @param Generator $generator An paragonie/random-lib `Generator`
     */
    public function __construct(Generator $generator = null)
    {
        if ($generator === null) {
            $factory = new Factory();

            $generator = $factory->getHighStrengthGenerator();
        }

        $this->generator = $generator;
    }

    /**
     * Generates a string of random binary data of the specified length
     *
     * @param int $length The number of bytes of random binary data to generate
     * @return string|null A binary string
     */
    public function generate(int $length): ?string
    {
        return $this->generator->generate($length);
    }
}
