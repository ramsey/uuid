<?php

namespace Ramsey\Uuid\Console\Util\Formatter;

use Ramsey\Uuid\Console\Util\UuidFormatter;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Console\Util\UuidContentFormatterInterface;

class V1Formatter implements UuidContentFormatterInterface
{
    public function getContent(UuidInterface $uuid)
    {
        return array(
            array('', 'content:', 'time:  ' . $uuid->getDateTime()->format('c')),
            array('', '', 'clock: ' . $uuid->getClockSequence() . ' (usually random)'),
            array('', '', 'node:  ' . substr(chunk_split($uuid->getNodeHex(), 2, ':'), 0, -1)),
        );
    }
}
