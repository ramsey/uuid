.. _reference.uuidfactoryinterface:

====================
UuidFactoryInterface
====================

.. php:namespace:: Ramsey\Uuid

.. php:interface:: UuidFactoryInterface

    Represents a UUID factory.

    .. php:method:: getValidator()

        :returntype: Ramsey\\Uuid\\Validator\\ValidatorInterface

    .. php:method:: uuid1([$node[, $clockSeq]])

        Generates a version 1, Gregorian time UUID. See :ref:`rfc4122.version1`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 1 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1

    .. php:method:: uuid2($localDomain[, $localIdentifier[, $node[, $clockSeq]]])

        Generates a version 2, DCE Security UUID. See :ref:`rfc4122.version2`.

        :param int $localDomain: The local domain to use (one of :php:const:`Uuid::DCE_DOMAIN_PERSON`, :php:const:`Uuid::DCE_DOMAIN_GROUP`, or :php:const:`Uuid::DCE_DOMAIN_ORG`)
        :param Ramsey\\Uuid\\Type\\Integer|null $localIdentifier: A local identifier for the domain (defaults to system UID or GID for *person* or *group*)
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 2 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV2

    .. php:method:: uuid3($ns, $name)

        Generates a version 3, name-based (MD5) UUID. See :ref:`rfc4122.version3`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: A version 3 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV3

    .. php:method:: uuid4()

        Generates a version 4, random UUID. See :ref:`rfc4122.version4`.

        :returns: A version 4 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV4

    .. php:method:: uuid5($ns, $name)

        Generates a version 5, name-based (SHA-1) UUID. See :ref:`rfc4122.version5`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: A version 5 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV5

    .. php:method:: uuid6([$node[, $clockSeq]])

        Generates a version 6, reordered Gregorian time UUID. See :ref:`rfc4122.version6`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 6 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV6

    .. php:method:: fromString($uuid)

        Creates an instance of UuidInterface from the string standard representation.

        :param string $uuid: The string standard representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:method:: fromBytes($bytes)

        Creates an instance of UuidInterface from a 16-byte string.

        :param string $bytes: A 16-byte binary string representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:method:: fromInteger($integer)

        Creates an instance of UuidInterface from a 128-bit string integer.

        :param string $integer: A 128-bit string integer representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:method:: fromDateTime($dateTime[, $node[, $clockSeq]])

        Creates a version 1 UUID instance from a `DateTimeInterface <https://www.php.net/datetimeinterface>`_ instance.

        :param DateTimeInterface $dateTime: The date from which to create the UUID instance
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 1 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1
