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
