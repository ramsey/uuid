<?php

namespace Ramsey\Uuid;


use MyCLabs\Enum\Enum;
use UnexpectedValueException;

class UuidVersionEnum extends Enum
{
    /**
     * Version 1 (time-based) UUID object constant identifier
     */
    const UUID_TYPE_TIME = 1;

    /**
     * Version 2 (identifier-based) UUID object constant identifier
     */
    const UUID_TYPE_IDENTIFIER = 2;

    /**
     * Version 3 (name-based and hashed with MD5) UUID object constant identifier
     */
    const UUID_TYPE_HASH_MD5 = 3;

    /**
     * Version 4 (random) UUID object constant identifier
     */
    const UUID_TYPE_RANDOM = 4;

    /**
     * Version 5 (name-based and hashed with SHA1) UUID object constant identifier
     */
    const UUID_TYPE_HASH_SHA1 = 5;

    /**
     * @return UuidVersionEnum
     * @throws UnexpectedValueException
     */
    public static function UUID_TYPE_TIME()
    {
        return new self(self::UUID_TYPE_TIME());
    }

    /**
     * @return UuidVersionEnum
     * @throws UnexpectedValueException
     */
    public static function UUID_TYPE_IDENTIFIER()
    {
        return new self(self::UUID_TYPE_IDENTIFIER());
    }

    /**
     * @return UuidVersionEnum
     * @throws UnexpectedValueException
     */
    public static function UUID_TYPE_HASH_MD5()
    {
        return new self(self::UUID_TYPE_HASH_MD5());
    }

    /**
     * @return UuidVersionEnum
     * @throws UnexpectedValueException
     */
    public static function UUID_TYPE_RANDOM()
    {
        return new self(self::UUID_TYPE_RANDOM());
    }

    /**
     * @return UuidVersionEnum
     * @throws UnexpectedValueException
     */
    public static function UUID_TYPE_HASH_SHA1()
    {
        return new self(self::UUID_TYPE_HASH_SHA1());
    }
}
