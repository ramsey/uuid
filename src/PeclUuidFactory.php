<?php

namespace Ramsey\Uuid;

/**
 * Factory relying on PECL UUID library whenever possible, otherwise defaulting
 * to pure PHP factory.
 * @author thibaud
 *
 */
class PeclUuidFactory implements UuidFactoryInterface
{
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
    public function __construct(UuidFactoryInterface $factory)
    {
        $this->hasExt = extension_loaded('uuid');
        $this->factory = $factory;
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

        return $this->fromString(uuid_create(UUID_TYPE_TIME));
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

        return $this->fromString(uuid_create(UUID_TYPE_RANDOM));
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
