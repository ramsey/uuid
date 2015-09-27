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

use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * CombGenerator provides functionality to generate COMB (combined GUID/timestamp)
 * sequential UUIDs
 *
 * @link https://en.wikipedia.org/wiki/Globally_unique_identifier#Sequential_algorithms
 */
class CombGenerator implements RandomGeneratorInterface
{
    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;

    /**
     * @var NumberConverterInterface
     */
    private $converter;

    /**
     * @var integer
     */
    private $timestampBytes;

    /**
     * Constructs a `CombGenerator` using a random-number generator and a number converter
     *
     * @param RandomGeneratorInterface $generator Random-number generator for the non-time part.
     * @param NumberConverterInterface $numberConverter Instance of number converter.
     */
    public function __construct(RandomGeneratorInterface $generator, NumberConverterInterface $numberConverter)
    {
        $this->converter = $numberConverter;
        $this->randomGenerator = $generator;
        $this->timestampBytes = 6;
    }

    /**
     * Generates a string of binary data of the specified length
     *
     * @param integer $length The number of bytes of random binary data to generate
     * @return string A binary string
     */
    public function generate($length)
    {
        if ($length < $this->timestampBytes || $length < 0) {
            throw new \InvalidArgumentException('Length must be a positive integer.');
        }

        $hash = '';

        if ($this->timestampBytes > 0 && $length > $this->timestampBytes) {
            $hash = $this->randomGenerator->generate($length - $this->timestampBytes);
        }

        $lsbTime = str_pad($this->converter->toHex($this->timestamp()), $this->timestampBytes * 2, '0', STR_PAD_LEFT);

        if ($this->timestampBytes > 0 && strlen($lsbTime) > $this->timestampBytes * 2) {
            $lsbTime = substr($lsbTime, 0 - ($this->timestampBytes * 2));
        }

        return hex2bin(str_pad(bin2hex($hash), $length - $this->timestampBytes, '0')) . hex2bin($lsbTime);
    }

    /**
     * Returns current timestamp as integer, precise to 0.00001 seconds
     *
     * @return integer
     */
    private function timestamp()
    {
        $time = explode(' ', microtime(false));

        return $time[1] . substr($time[0], 2, 5);
    }
}
