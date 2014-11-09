<?php

namespace Rhumsaa\Uuid\Console\Util\Formatter;

use Rhumsaa\Uuid\Console\Util\UuidFormatter;
use Rhumsaa\Uuid\UuidInterface;
use Rhumsaa\Uuid\Console\Util\UuidContentFormatterInterface;

class V2Formatter implements UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid)
    {
        return array();
    }
}
