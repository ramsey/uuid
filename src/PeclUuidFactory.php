<?php

namespace Rhumsaa\Uuid;

class PeclUuidFactory implements UuidFactoryInterface
{

    private $factory;

    public function __construct(UuidFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::uuid1()
     */
    public function uuid1($node = null, $clockSeq = null)
    {
        $uuid = uuid_create(UUID_TYPE_TIME);

        return $this->fromString($uuid);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::uuid3()
     */
    public function uuid3($ns, $name)
    {
        return $this->factory->uuid3($ns, $name);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::uuid4()
     */
    public function uuid4()
    {
        $uuid = uuid_create(UUID_TYPE_RANDOM);

        return $this->fromString($uuid);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::uuid5()
     */
    public function uuid5($ns, $name)
    {
        return $this->factory->uuid5($ns, $name);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::fromBytes()
     */
    public function fromBytes($bytes)
    {
        return $this->factory->fromBytes($bytes);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::fromString()
     */
    public function fromString($name)
    {
        return $this->factory->fromString($name);
    }

    /*
     * (non-PHPdoc) @see \Rhumsaa\Uuid\UuidFactoryInterface::fromInteger()
     */
    public function fromInteger($integer)
    {
        return $this->factory->fromInteger($integer);
    }
}