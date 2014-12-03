<?php

namespace Rhumsaa\Uuid\Console\Util;

use Rhumsaa\Uuid\UuidInterface;

interface UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid);
}
