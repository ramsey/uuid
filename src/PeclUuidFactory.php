<?php

namespace Ramsey\Uuid;

use Ramsey\Uuid\Converter\NumberConverterInterface;

/**
 * Factory relying on PECL UUID library whenever possible, otherwise defaulting
 * to pure PHP factory.
 * @author thibaud
 *
 */
class PeclUuidFactory implements UuidFactoryInterface
{
    /**
     * @var CodecInterface
     */
    private $codec;

    /**
     * @var NumberConverterInterface
     */
    private $converter;

    /**
     *
     * @var UuidFactoryInterface
     */
    private $factory;

    /**
     *
     * @var boolean
     */
    private $hasExt = false;

    /**
     *
     * @param UuidFactoryInterface $factory
     */
    public function __construct(UuidFactoryInterface $factory, FeatureSet $features = null)
    {
        $this->hasExt = extension_loaded('uuid');
        $this->factory = $factory;

        $features = $features ?: new FeatureSet();

        $this->codec = $features->getCodec();
        $this->converter = $features->getNumberConverter();
    }

    /**
     * Forces factory to act as if PECL extension is not available
     */
    public function disablePecl()
    {
        $this->hasExt = false;
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::uuid1()
     */
    public function uuid1($node = null, $clockSeq = null)
    {
        if (! $this->hasExt || $node !== null || $clockSeq !== null) {
            // If either param is not null, we cannot use PECL without breaking LSP.
            return $this->factory->uuid1($node, $clockSeq);
        }

        return new LazyUuid(uuid_create(UUID_TYPE_TIME), $this->converter, $this->codec);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::uuid3()
     */
    public function uuid3($ns, $name)
    {
        return $this->factory->uuid3($ns, $name);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::uuid4()
     */
    public function uuid4()
    {
        if (! $this->hasExt) {
            return $this->factory->uuid4();
        }

        return new LazyUuid(uuid_create(UUID_TYPE_RANDOM), $this->converter, $this->codec);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::uuid5()
     */
    public function uuid5($ns, $name)
    {
        return $this->factory->uuid5($ns, $name);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::fromBytes()
     */
    public function fromBytes($bytes)
    {
        return $this->factory->fromBytes($bytes);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::fromString()
     */
    public function fromString($name)
    {
        return $this->factory->fromString($name);
    }

    /**
     * (non-PHPdoc) @see \Ramsey\Uuid\UuidFactoryInterface::fromInteger()
     */
    public function fromInteger($integer)
    {
        return $this->factory->fromInteger($integer);
    }
}
