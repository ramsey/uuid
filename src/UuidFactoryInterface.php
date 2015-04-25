<?php

namespace Ramsey\Uuid;

interface UuidFactoryInterface
{
    public function uuid1($node = null, $clockSeq = null);

    public function uuid3($ns, $name);

    public function uuid4();

    public function uuid5($ns, $name);

    public function fromBytes($bytes);

    public function fromString($name);

    public function fromInteger($integer);
}
