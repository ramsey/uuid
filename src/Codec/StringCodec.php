<?php

namespace Rhumsaa\Uuid\Codec;

use InvalidArgumentException;
use Rhumsaa\Uuid\Codec;
use Rhumsaa\Uuid\UuidInterface;
use Rhumsaa\Uuid\Uuid;

class StringCodec implements Codec
{

    public function encode(UuidInterface $uuid)
    {
        $fields = array_values($uuid->getFieldsHex());

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $fields
        );
    }

    public function decode($encodedUuid)
    {
        $nameParsed = str_replace(array(
            'urn:',
            'uuid:',
            '{',
            '}',
            '-'
        ), '', $encodedUuid);

        // We have stripped out the dashes and are breaking up the string using
        // substr(). In this way, we can accept a full hex value that doesn't
        // contain dashes.
        $components = array(
            substr($nameParsed, 0, 8),
            substr($nameParsed, 8, 4),
            substr($nameParsed, 12, 4),
            substr($nameParsed, 16, 4),
            substr($nameParsed, 20)
        );

        $nameParsed = implode('-', $components);

        if (! Uuid::isValid($nameParsed)) {
            throw new InvalidArgumentException('Invalid UUID string: ' . $name);
        }

        $fields = array(
            'time_low' => sprintf('%08s', $components[0]),
            'time_mid' => sprintf('%04s', $components[1]),
            'time_hi_and_version' => sprintf('%04s', $components[2]),
            'clock_seq_hi_and_reserved' => sprintf('%02s', substr($components[3], 0, 2)),
            'clock_seq_low' => sprintf('%02s', substr($components[3], 2)),
            'node' => sprintf('%012s', $components[4])
        );

        return new Uuid($fields, $this);
    }

    public function decodeBytes($bytes)
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }

        $hexUuid = unpack('H*', $bytes);

        return $this->decode($hexUuid);
    }
}
