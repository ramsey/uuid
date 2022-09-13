.. _reference.helper:

================
Helper Functions
================

ramsey/uuid additionally provides the following helper functions, which return
only the string standard representation of a UUID.

.. php:function:: Ramsey\\Uuid\\v1([$node[, $clockSeq]])

    Generates a string standard representation of a version 1, Gregorian time UUID.

    :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
    :param int|null $clockSeq: An optional clock sequence to use
    :returns: A string standard representation of a version 1 UUID
    :returntype: string

.. php:function:: Ramsey\\Uuid\\v2($localDomain[, $localIdentifier[, $node[, $clockSeq]]])

    Generates a string standard representation of a version 2, DCE Security UUID.

    :param int $localDomain: The local domain to use (one of ``Uuid::DCE_DOMAIN_PERSON``, ``Uuid::DCE_DOMAIN_GROUP``, or ``Uuid::DCE_DOMAIN_ORG``)
    :param Ramsey\\Uuid\\Type\\Integer|null $localIdentifier: A local identifier for the domain (defaults to system UID or GID for *person* or *group*)
    :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
    :param int|null $clockSeq: An optional clock sequence to use
    :returns: A string standard representation of a version 2 UUID
    :returntype: string

.. php:function:: Ramsey\\Uuid\\v3($ns, $name)

    Generates a string standard representation of a version 3, name-based (MD5) UUID.

    :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
    :param string $name: The name from which to generate an identifier
    :returns: A string standard representation of a version 3 UUID
    :returntype: string

.. php:function:: Ramsey\\Uuid\\v4()

    Generates a string standard representation of a version 4, random UUID.

    :returns: A string standard representation of a version 4 UUID
    :returntype: string

.. php:function:: Ramsey\\Uuid\\v5($ns, $name)

    Generates a string standard representation of a version 5, name-based (SHA-1) UUID.

    :param Ramsey\\Uuid\\UuidInterface|string $ns: The namespace for this identifier
    :param string $name: The name from which to generate an identifier
    :returns: A string standard representation of a version 5 UUID
    :returntype: string

.. php:function:: Ramsey\\Uuid\\v6([$node[, $clockSeq]])

    Generates a string standard representation of a version 6, reordered time UUID.

    :param Ramsey\\Uuid\\Type\\Hexadecimal|null $node: An optional hexadecimal node to use
    :param int|null $clockSeq: An optional clock sequence to use
    :returns: A string standard representation of a version 6 UUID
    :returntype: string
