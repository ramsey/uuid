<?php

namespace Ramsey\Uuid\Console\Util;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Console\Util\Formatter\V1Formatter;
use Ramsey\Uuid\Console\Util\Formatter\V2Formatter;
use Ramsey\Uuid\Console\Util\Formatter\V3Formatter;
use Ramsey\Uuid\Console\Util\Formatter\V4Formatter;
use Ramsey\Uuid\Console\Util\Formatter\V5Formatter;
use Symfony\Component\Console\Helper\TableHelper;

class UuidFormatter
{

    private static $versionMap = [
        1 => '1 (time and node based)',
        2 => '2 (DCE security based)',
        3 => '3 (name based, MD5)',
        4 => '4 (random data based)',
        5 => '5 (name based, SHA-1)'
    ];

    private static $variantMap = [
        Uuid::RESERVED_NCS => 'Reserved',
        Uuid::RFC_4122 => 'RFC 4122',
        Uuid::RESERVED_MICROSOFT => 'Reserved for Microsoft use.',
        Uuid::RESERVED_FUTURE => 'Reserved for future use.'
    ];

    private static $formatters;

    public function __construct()
    {
        if (self::$formatters == null) {
            self::$formatters = [
                1 => new V1Formatter(),
                2 => new V2Formatter(),
                3 => new V3Formatter(),
                4 => new V4Formatter(),
                5 => new V5Formatter()
            ];
        }
    }

    public function write(TableHelper $table, UuidInterface $uuid)
    {
        $table->addRows(array(
            array('encode:', 'STR:', (string) $uuid),
            array('',        'INT:', (string) $uuid->getInteger()),
        ));

        if ($uuid->getVariant() == Uuid::RFC_4122) {
            $table->addRows(array(
                array('decode:', 'variant:',$this->getFormattedVariant($uuid)),
                array('',        'version:', $this->getFormattedVersion($uuid)),
            ));

            $table->addRows($this->getContent($uuid));
        } else {
            $table->addRows(array(
                array('decode:', 'variant:', 'Not an RFC 4122 UUID'),
            ));
        }
    }

    public function getFormattedVersion(UuidInterface $uuid)
    {
        return self::$versionMap[$uuid->getVersion()];
    }

    public function getFormattedVariant(UuidInterface $uuid)
    {
        return self::$variantMap[$uuid->getVariant()];
    }

    /**
     * Returns content as an array of rows, each row being an array containing column values.
     */
    public function getContent(UuidInterface $uuid)
    {
        $formatter = self::$formatters[$uuid->getVersion()];

        return $formatter->getContent($uuid);
    }
}
