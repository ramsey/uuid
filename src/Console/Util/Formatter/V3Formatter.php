<?php

namespace Rhumsaa\Uuid\Console\Util\Formatter;

use Rhumsaa\Uuid\Console\Util\UuidFormatter;
use Rhumsaa\Uuid\UuidInterface;
use Rhumsaa\Uuid\Console\Util\UuidContentFormatterInterface;

class V3Formatter implements UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid)
    {
        return array(
            array('', 'content:', substr(chunk_split($uuid->getHex(), 2, ':'), 0, -1)),
            array('', '', '(not decipherable: MD5 message digest only)'),
        );
    }
}
