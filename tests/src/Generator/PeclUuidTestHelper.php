<?php

namespace Ramsey\Uuid\Generator;

define('UUID_TYPE_TIME', 1);

final class PeclUuidHelper
{
    const EXAMPLE_UUID = 'f81d4fae-7dec-11d0-a765-00a0c91e6bf6';
}

function uuid_create($type)
{
    if ($type === UUID_TYPE_TIME) {
        return PeclUuidHelper::EXAMPLE_UUID;
    }
}

function uuid_parse($uuid)
{
    return PeclUuidHelper::EXAMPLE_UUID;
}
