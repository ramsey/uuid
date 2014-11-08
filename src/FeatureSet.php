<?php

namespace Rhumsaa\Uuid;

use Rhumsaa\Uuid\Node\FallbackNodeProvider;
use Rhumsaa\Uuid\Node\RandomNodeProvider;
use Rhumsaa\Uuid\Node\SystemNodeProvider;
use Rhumsaa\Uuid\Time\BigNumberTimeConverter;
use Rhumsaa\Uuid\Time\DegradedTimeConverter;
use Rhumsaa\Uuid\Time\PhpTimeConverter;
use Rhumsaa\Uuid\Time\SystemTimeProvider;
use Rhumsaa\Uuid\Builder\DefaultUuidBuilder;
use Rhumsaa\Uuid\Codec\StringCodec;
use Rhumsaa\Uuid\Codec\GuidStringCodec;
use Rhumsaa\Uuid\Builder\DegradedUuidBuilder;

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

    private $timeConverter;

    private $timeProvider;

    public function __construct($useGuids = false, $force32Bit = false, $forceNoBigNumber = false, $ignoreSystemNode = false)
    {
        $this->disableBigNumber = $forceNoBigNumber;
        $this->disable64Bit = $force32Bit;
        $this->ignoreSystemNode = $ignoreSystemNode;

        $this->numberConverter = $this->buildNumberConverter();
        $this->builder = $this->buildUuidBuilder();
        $this->codec = $this->buildCodec($useGuids);
        $this->nodeProvider = $this->buildNodeProvider();
        $this->randomGenerator = $this->buildRandomGenerator();
        $this->timeConverter = $this->buildTimeConverter();
        $this->timeProvider = new SystemTimeProvider();
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

    public function getTimeConverter()
    {
        return $this->timeConverter;
    }

    public function getTimeProvider()
    {
        return $this->timeProvider;
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

    protected function buildTimeConverter()
    {
        if ($this->is64BitSystem()) {
            return new PhpTimeConverter();
        }
        elseif ($this->hasBigNumber()) {
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
