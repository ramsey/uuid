<?php

namespace Ramsey\Uuid\Console\Util;

use Ramsey\Uuid\UuidInterface;

interface UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid);
}
