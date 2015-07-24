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

use Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use Ramsey\Uuid\Converter\Number\BigNumberConverter;
use Ramsey\Uuid\Converter\Number\DegradedNumberConverter;
use Ramsey\Uuid\Converter\Time\BigNumberTimeConverter;
use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Ramsey\Uuid\Builder\DefaultUuidBuilder;
use Ramsey\Uuid\Codec\StringCodec;
use Ramsey\Uuid\Codec\GuidStringCodec;
use Ramsey\Uuid\Builder\DegradedUuidBuilder;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Ramsey\Uuid\Provider\TimeProviderInterface;

/**
 * Detects and exposes available features in current environment (32 or 64 bit, available dependencies...)
 *
 * @author thibaud
 *
 */
class FeatureSet
{

    private $disableBigNumber = false;

    private $disable64Bit = false;

    private $ignoreSystemNode = false;

    private $builder;

    private $codec;

    private $nodeProvider;

    private $numberConverter;

    private $randomGenerator;

    private $timeGenerator;

    private $timeConverter;

    private $timeProvider;

    public function __construct(
        $useGuids = false,
        $force32Bit = false,
        $forceNoBigNumber = false,
        $ignoreSystemNode = false
    ) {
        $this->disableBigNumber = $forceNoBigNumber;
        $this->disable64Bit = $force32Bit;
        $this->ignoreSystemNode = $ignoreSystemNode;

        $this->numberConverter = $this->buildNumberConverter();
        $this->builder = $this->buildUuidBuilder();
        $this->codec = $this->buildCodec($useGuids);
        $this->nodeProvider = $this->buildNodeProvider();
        $this->randomGenerator = $this->buildRandomGenerator();
        $this->timeConverter = $this->buildTimeConverter();
        $this->setTimeProvider(new SystemTimeProvider());
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function getCodec()
    {
        return $this->codec;
    }

    public function getNodeProvider()
    {
        return $this->nodeProvider;
    }

    public function getNumberConverter()
    {
        return $this->numberConverter;
    }

    public function getRandomGenerator()
    {
        return $this->randomGenerator;
    }

    public function getTimeGenerator()
    {
        return $this->timeGenerator;
    }

    public function getTimeConverter()
    {
        return $this->timeConverter;
    }

    public function getTimeProvider()
    {
        return $this->timeProvider;
    }

    public function setTimeProvider(TimeProviderInterface $timeProvider)
    {
        $this->timeProvider = $timeProvider;
        $this->timeGenerator = $this->buildTimeGenerator();
    }

    protected function buildCodec($useGuids = false)
    {
        if ($useGuids) {
            return new GuidStringCodec($this->builder);
        }

        return new StringCodec($this->builder);
    }

    protected function buildNodeProvider()
    {
        if ($this->ignoreSystemNode) {
            return new RandomNodeProvider();
        }

        return new FallbackNodeProvider([
            new SystemNodeProvider(),
            new RandomNodeProvider()
        ]);
    }

    protected function buildNumberConverter()
    {
        if ($this->hasBigNumber()) {
            return new BigNumberConverter();
        }

        return new DegradedNumberConverter();
    }

    protected function buildRandomGenerator()
    {
        return (new RandomGeneratorFactory())->getGenerator();
    }

    protected function buildTimeGenerator()
    {
        return (new TimeGeneratorFactory($this))->getGenerator();
    }

    protected function buildTimeConverter()
    {
        if ($this->is64BitSystem()) {
            return new PhpTimeConverter();
        } elseif ($this->hasBigNumber()) {
            return new BigNumberTimeConverter();
        }

        return new DegradedTimeConverter();
    }

    protected function buildUuidBuilder()
    {
        if ($this->is64BitSystem()) {
            return new DefaultUuidBuilder($this->numberConverter);
        }

        return new DegradedUuidBuilder($this->numberConverter);
    }

    /**
     * Returns true if the system has Moontoast\Math\BigNumber
     *
     * @return bool
     */
    protected function hasBigNumber()
    {
        return class_exists('Moontoast\Math\BigNumber') && ! $this->disableBigNumber;
    }

    /**
     * Returns true if the system is 64-bit, false otherwise
     *
     * @return bool
     */
    protected function is64BitSystem()
    {
        return PHP_INT_SIZE == 8 && ! $this->disable64Bit;
    }
}
