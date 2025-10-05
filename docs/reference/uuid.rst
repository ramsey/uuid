.. _reference.uuid:

====
Uuid
====

Ramsey\\Uuid\\Uuid provides static methods for the most common functionality for generating and working with UUIDs. It
also provides constants used throughout the ramsey/uuid library.

.. php:namespace:: Ramsey\Uuid

.. php:class:: Uuid

    .. php:const:: UUID_TYPE_TIME

        :ref:`rfc4122.version1` UUID.

    .. php:const:: UUID_TYPE_DCE_SECURITY

        :ref:`rfc4122.version2` UUID.

    .. php:const:: UUID_TYPE_HASH_MD5

        :ref:`rfc4122.version3` UUID.

    .. php:const:: UUID_TYPE_RANDOM

        :ref:`rfc4122.version4` UUID.

    .. php:const:: UUID_TYPE_HASH_SHA1

        :ref:`rfc4122.version5` UUID.

    .. php:const:: UUID_TYPE_REORDERED_TIME

        :ref:`rfc4122.version6` UUID.

    .. php:const:: UUID_TYPE_PEABODY

        *Deprecated.* Use :php:const:`Uuid::UUID_TYPE_REORDERED_TIME` instead.

    .. php:const:: UUID_TYPE_UNIX_TIME

        :ref:`rfc4122.version7` UUID.

    .. php:const:: UUID_TYPE_CUSTOM

        :ref:`rfc4122.version8` UUID.

    .. php:const:: NAMESPACE_DNS

        The name string is a fully-qualified domain name.

    .. php:const:: NAMESPACE_URL

        The name string is a URL.

    .. php:const:: NAMESPACE_OID

        The name string is an `ISO object identifier (OID)`_.

    .. php:const:: NAMESPACE_X500

        The name string is an `X.500`_ `DN`_ in `DER`_ or a text output format.

    .. php:const:: NIL

        The nil UUID is a special form of UUID that is specified to have all 128 bits set to zero.

    .. php:const:: DCE_DOMAIN_PERSON

        DCE Security principal (person) domain.

    .. php:const:: DCE_DOMAIN_GROUP

        DCE Security group domain.

    .. php:const:: DCE_DOMAIN_ORG

        DCE Security organization domain.

    .. php:const:: RESERVED_NCS

        Variant identifier: reserved, NCS backward compatibility.

    .. php:const:: RFC_4122

        An alias for :php:const:`Uuid::RFC_9562`.

    .. php:const:: RFC_9562

        Variant identifier: the UUID layout specified in `RFC 9562`_ (formerly `RFC 4122`_).

    .. php:const:: RESERVED_MICROSOFT

        Variant identifier: reserved, Microsoft Corporation backward compatibility.

    .. php:const:: RESERVED_FUTURE

        Variant identifier: reserved for future definition.

    .. php:staticmethod:: uuid1([$node[, $clockSeq]])

        Generates a version 1, Gregorian time UUID. See :ref:`rfc4122.version1`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 1 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1

    .. php:staticmethod:: uuid2($localDomain[, $localIdentifier[, $node[, $clockSeq]]])

        Generates a version 2, DCE Security UUID. See :ref:`rfc4122.version2`.

        :param int $localDomain: The local domain to use (one of :php:const:`Uuid::DCE_DOMAIN_PERSON`, :php:const:`Uuid::DCE_DOMAIN_GROUP`, or :php:const:`Uuid::DCE_DOMAIN_ORG`)
        :param Ramsey\\Uuid\\Type\\Integer|null $localIdentifier: A local identifier for the domain (defaults to system UID or GID for *person* or *group*)
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 2 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV2

    .. php:staticmethod:: uuid3($ns, $name)

        Generates a version 3, name-based (MD5) UUID. See :ref:`rfc4122.version3`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: A version 3 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV3

    .. php:staticmethod:: uuid4()

        Generates a version 4, random UUID. See :ref:`rfc4122.version4`.

        :returns: A version 4 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV4

    .. php:staticmethod:: uuid5($ns, $name)

        Generates a version 5, name-based (SHA-1) UUID. See :ref:`rfc4122.version5`.

        :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
        :param string $name: The name from which to generate an identifier
        :returns: A version 5 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV5

    .. php:staticmethod:: uuid6([$node[, $clockSeq]])

        Generates a version 6, reordered Gregorian time UUID. See :ref:`rfc4122.version6`.

        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 6 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV6

    .. php:staticmethod:: uuid7([$dateTime])

        Generates a version 7, Unix Epoch time UUID. See :ref:`rfc4122.version7`.

        :param DateTimeInterface|null $dateTime: The date from which to create the UUID instance
        :returns: A version 7 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV7

    .. php:staticmethod:: uuid8($bytes)

        Generates a version 8, implementation-specific, custom format UUID. See :ref:`rfc4122.version8`.

        :param string $bytes: A 16-byte octet string. This is an open blob of data that you may fill with 128 bits of
                              information. Be aware, however, bits 48 through 51 will be replaced with the UUID version
                              field, and bits 64 and 65 will be replaced with the UUID variant. You MUST NOT rely on
                              these bits for your application needs.
        :returns: A version 8 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV8

    .. php:staticmethod:: fromString($uuid)

        Creates an instance of UuidInterface from the string standard representation.

        :param string $uuid: The string standard representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:staticmethod:: fromBytes($bytes)

        Creates an instance of UuidInterface from a 16-byte string.

        :param string $bytes: A 16-byte binary string representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:staticmethod:: fromInteger($integer)

        Creates an instance of UuidInterface from a 128-bit string integer.

        :param string $integer: A 128-bit string integer representation of a UUID
        :returntype: Ramsey\\Uuid\\UuidInterface

    .. php:staticmethod:: fromDateTime($dateTime[, $node[, $clockSeq]])

        Creates a version 1 UUID instance from a `DateTimeInterface <https://www.php.net/datetimeinterface>`_ instance.

        :param DateTimeInterface $dateTime: The date from which to create the UUID instance
        :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
        :param int|null $clockSeq: An optional clock sequence to use
        :returns: A version 1 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1

    .. php:staticmethod:: isValid($uuid)

        Validates the string standard representation of a UUID.

        :param string $uuid: The string standard representation of a UUID
        :returntype: ``bool``

    .. php:staticmethod:: setFactory($factory)

        Sets the factory used to create UUIDs.

        :param Ramsey\\Uuid\\UuidFactoryInterface $factory: A UUID factory to use for all UUID generation
        :returntype: void

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _ISO object identifier (OID): http://www.oid-info.com
.. _X.500: https://en.wikipedia.org/wiki/X.500
.. _DN: https://en.wikipedia.org/wiki/Distinguished_Name
.. _DER: https://www.itu.int/rec/T-REC-X.690/
