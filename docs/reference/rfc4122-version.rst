.. _reference.rfc4122.version:

================
Rfc4122\\Version
================

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:enum:: Version : int

    The version number describes how the UUID was generated.

    .. php:case:: Time : 1

        A version 1 UUID uses a timestamp based on the Gregorian calendar epoch,
        along with the MAC address (or *node*) for a network interface on the
        local machine. For more details, see :ref:`rfc4122.version1`.

    .. php:case:: DceSecurity : 2

        Like a version 1 UUID, a version 2 UUID uses the current time, along
        with the MAC address (or *node*) for a network interface on the local
        machine. Additionally, a version 2 UUID replaces the low part of the
        time field with a local identifier such as the user ID or group ID of
        the local account that created the UUID. For more details, see
        :ref:`rfc4122.version2`.

    .. php:case:: HashMd5 : 3

        Name-based UUIDs combine a namespace with a name. This way, the UUIDs
        are unique to the namespace they're created in. Version 3 UUIDs use the
        MD5 hashing algorithm to combine the namespace and name. For more
        details, see :ref:`rfc4122.version3`.

    .. php:case:: Random : 4

        Version 4 UUIDs are randomly-generated identifiers. For more details,
        see :ref:`rfc4122.version4`.

    .. php:case:: HashSha1 : 5

        Name-based UUIDs combine a namespace with a name. This way, the UUIDs
        are unique to the namespace they're created in. Version 5 UUIDs use the
        SHA-1 hashing algorithm to combine the namespace and name. For more
        details, see :ref:`rfc4122.version5`.

    .. php:case:: ReorderedTime : 6

    .. php:case:: UnixTime : 7

    .. php:case:: Custom : 8

    .. php:const:: V1

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::Time`.

    .. php:const:: V2

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::DceSecurity`.

    .. php:const:: V3

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::HashMd4`.

    .. php:const:: V4

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::Random`.

    .. php:const:: V5

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::HashSha1`.

    .. php:const:: V6

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::ReorderedTime`.

    .. php:const:: V7

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::UnixTime`.

    .. php:const:: V8

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::Custom`.

    .. php:const:: Peabody

        An alias for :php:case:`Ramsey\\Uuid\\Rfc4122\\Version::ReorderedTime`.
