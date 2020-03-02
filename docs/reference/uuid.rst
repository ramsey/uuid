.. _reference.uuid:

====
Uuid
====

``Ramsey\Uuid`` provides static methods for the most common functionality for
generating and working with UUIDs. It also provides constants used throughout
the ramsey/uuid library.

.. php:namespace:: Ramsey\Uuid

.. php:class:: Uuid

    .. php:staticmethod:: uuid1([$node[, $clockSeq]])

        Generates a version 1, time-based UUID. See :ref:`rfc4122.version1`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV1*) A version 1 UUID

    .. php:staticmethod:: uuid2($localDomain[, $localIdentifier[, $node[, $clockSeq]]])

        Generates a version 2, DCE Security UUID. See :ref:`rfc4122.version2`.

        :param int $localDomain: The local domain to use (one of ``Uuid::DCE_DOMAIN_PERSON``, ``Uuid::DCE_DOMAIN_GROUP``, or ``Uuid::DCE_DOMAIN_ORG``)
        :param Ramsey\\Uuid\\Type\\Integer|null $localIdentifier: A local identifier for the domain (defaults to system UID or GID for *person* or *group*)
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV2*) A version 2 UUID

    .. php:staticmethod:: uuid3($ns, $name)

        Generates a version 3, name-based (MD5) UUID. See :ref:`rfc4122.version3`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV3*) A version 3 UUID

    .. php:staticmethod:: uuid4()

        Generates a version 4, random UUID. See :ref:`rfc4122.version4`.

        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV4*) A version 4 UUID

    .. php:staticmethod:: uuid5($ns, $name)

        Generates a version 5, name-based (SHA-1) UUID. See :ref:`rfc4122.version5`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV5*) A version 5 UUID

    .. php:staticmethod:: uuid6([$node[, $clockSeq]])

        Generates a version 6, ordered-time UUID. See :ref:`nonstandard.version6`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: (*Ramsey\\Uuid\\Nonstandard\\UuidV6*) A version 6 UUID

    .. php:staticmethod:: fromString($uuid)

        Creates an instance of ``UuidInterface`` from the string standard
        representation.

        :param string $uuid: The string standard representation of a UUID
        :returns: (*Ramsey\\Uuid\\UuidInterface*) An instance of ``UuidInterface``

    .. php:staticmethod:: fromBytes($bytes)

        Creates an instance of ``UuidInterface`` from a 16-byte string.

        :param string $bytes: A 16-byte binary string representation of a UUID
        :returns: (*Ramsey\\Uuid\\UuidInterface*) An instance of ``UuidInterface``

    .. php:staticmethod:: fromInteger($integer)

        Creates an instance of ``UuidInterface`` from a 128-bit string integer.

        :param string $integer: A 128-bit string integer representation of a UUID
        :returns: (*Ramsey\\Uuid\\UuidInterface*) An instance of ``UuidInterface``

    .. php:staticmethod:: fromDateTime($dateTime[, $node[, $clockSeq]])

        Creates a version 1 UUID instance from a ``DateTimeInterface`` instance.

        :param DateTimeInterface $dateTime: The date from which to create the UUID instance
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: (*Ramsey\\Uuid\\Rfc4122\\UuidV1*) A version 1 UUID

    .. php:staticmethod:: isValid($uuid)

        Validates the string standard representation of a UUID

        :param string $uuid: The string standard representation of a UUID
        :returns: True if the string UUID is valid, false otherwise

    .. php:const:: NAMESPACE_DNS

        6ba7b810-9dad-11d1-80b4-00c04fd430c8

    .. php:const:: NAMESPACE_URL

        6ba7b811-9dad-11d1-80b4-00c04fd430c8

    .. php:const:: NAMESPACE_OID

        6ba7b812-9dad-11d1-80b4-00c04fd430c8

    .. php:const:: NAMESPACE_X500

        6ba7b814-9dad-11d1-80b4-00c04fd430c8
