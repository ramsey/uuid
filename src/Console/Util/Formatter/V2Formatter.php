<?php

namespace Ramsey\Uuid\Console\Util\Formatter;

use Ramsey\Uuid\Console\Util\UuidFormatter;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Console\Util\UuidContentFormatterInterface;

class V2Formatter implements UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid)
    {
        return array();
    }
}
