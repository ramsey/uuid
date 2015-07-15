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

namespace Ramsey\Uuid;

use Ramsey\Uuid\Converter\NumberConverterInterface;

interface UuidInterface
{

    /**
     * @return integer
     */
    public function compareTo(UuidInterface $other);

    /**
     * @return boolean
     */
    public function equals($other);

    /**
     * @return NumberConverterInterface
     */
    public function getNumberConverter();

    /**
     * @return string
     */
    public function getHex();

    public function getFieldsHex();

    /**
     * @return string
     */
    public function getClockSeqHiAndReservedHex();

    /**
     * @return string
     */
    public function getClockSeqLowHex();

    /**
     * @return string
     */
    public function getClockSequenceHex();

    /**
     * @return \DateTime
     */
    public function getDateTime();

    /**
     * @return \Moontoast\Math\BigNumber
     */
    public function getInteger();

    /**
     * @return string
     */
    public function getLeastSignificantBitsHex();

    /**
     * @return string
     */
    public function getMostSignificantBitsHex();

    /**
     * @return string
     */
    public function getNodeHex();

    /**
     * @return string
     */
    public function getTimeHiAndVersionHex();

    /**
     * @return string
     */
    public function getTimeLowHex();

    /**
     * @return string
     */
    public function getTimeMidHex();

    /**
     * @return string
     */
    public function getTimestampHex();

    /**
     * @return string
     */
    public function getUrn();

    /**
     * @return integer
     */
    public function getVariant();

    /**
     * @return integer|null
     */
    public function getVersion();

    /**
     * @return string
     */
    public function toString();
}
