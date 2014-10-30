<?php

namespace Rhumsaa\Uuid\Codec;

use InvalidArgumentException;
use Rhumsaa\Uuid\Codec;
use Rhumsaa\Uuid\UuidInterface;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\BigNumberConverter;
use Rhumsaa\Uuid\UuidFactory;

class GuidStringCodec implements Codec
{

    private $factory;

    public function __construct(UuidFactory $factory)
    {
        $this->factory = $factory;
    }

    public function encode(UuidInterface $uuid)
    {
        $fields = array_values($uuid->getFieldsHex());

        // Swap byte-order on the first three fields
        $hex = unpack('H*', pack('V', hexdec($fields[0])));
        $fields[0] = $hex[1];
        $hex = unpack('H*', pack('v', hexdec($fields[1])));
        $fields[1] = $hex[1];
        $hex = unpack('H*', pack('v', hexdec($fields[2])));
        $fields[2] = $hex[1];

        return vsprintf(
            '%08s-%04s-%04s-%02s%02s-%012s',
            $fields
        );
    }

    public function encodeBinary(UuidInterface $uuid)
    {
        $reversed = $this->_decode($uuid->getConverter(), $this->encode($uuid), false);

        return (new StringCodec())->encodeBinary($reversed);
    }

    public function decode(BigNumberConverter $converter, $encodedUuid)
    {
        return $this->_decode($converter, $encodedUuid, true);
    }

    public function decodeBytes(BigNumberConverter $converter, $bytes)
    {
        if (strlen($bytes) !== 16) {
            throw new InvalidArgumentException('$bytes string should contain 16 characters.');
        }

        $hexUuid = unpack('H*', $bytes);

        return $this->_decode($converter, $hexUuid[1], false);
    }

    private function _decode(BigNumberConverter $converter, $hex, $swap)
    {
        $nameParsed = str_replace(array(
            'urn:',
            'uuid:',
            '{',
            '}',
            '-'
        ), '', $hex);

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

        if ($swap) {
            $hex = unpack('H*', pack('V', hexdec($components[0])));
            $components[0] = $hex[1];
            $hex = unpack('H*', pack('v', hexdec($components[1])));
            $components[1] = $hex[1];
            $hex = unpack('H*', pack('v', hexdec($components[2])));
            $components[2] = $hex[1];
        }

        $nameParsed = implode('-', $components);

        if (! Uuid::isValid($nameParsed)) {
            throw new InvalidArgumentException('Invalid UUID string: ' . $hex);
        }

        $fields = array(
            'time_low' => sprintf('%08s', $components[0]),
            'time_mid' => sprintf('%04s', $components[1]),
            'time_hi_and_version' => sprintf('%04s', $components[2]),
            'clock_seq_hi_and_reserved' => sprintf('%02s', substr($components[3], 0, 2)),
            'clock_seq_low' => sprintf('%02s', substr($components[3], 2)),
            'node' => sprintf('%012s', $components[4])
        );

        return $this->factory->uuid($fields, $this);
    }
}
