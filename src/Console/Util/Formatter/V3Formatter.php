<?php

namespace Ramsey\Uuid\Console\Util\Formatter;

use Ramsey\Uuid\Console\Util\UuidFormatter;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Console\Util\UuidContentFormatterInterface;

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
