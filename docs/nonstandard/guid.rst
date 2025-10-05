.. _nonstandard.guid:

===================================
Globally Unique Identifiers (GUIDs)
===================================

.. tip::

    Using these techniques to work with GUIDs is useful if you're working with identifiers that have been stored in GUID
    byte order. For example, this is the case if working with the ``UNIQUEIDENTIFIER`` data type in Microsoft SQL Server.
    This is a GUID, stored as a 16-byte binary string. If working directly with the bytes, you may use the GUID
    functionality in ramsey/uuid to properly handle this data type.

According to the Windows Dev Center article on `GUID structure`_, "GUIDs are the Microsoft implementation of the
distributed computing environment (DCE) universally unique identifier." For all intents and purposes, a GUID string
representation is identical to that of an `RFC 9562`_ (formerly `RFC 4122`_) UUID. For historical reasons, *the byte
order is not*.

The `.NET Framework documentation`_ explains:

    Note that the order of bytes in the returned byte array is different from the string representation of a Guid value.
    The order of the beginning four-byte group and the next two two-byte groups is reversed, whereas the order of the
    last two-byte group and the closing six-byte group is the same.

This is best explained by example.

.. code-block:: php
    :caption: Decoding a GUID from byte representation
    :name: nonstandard.guid.decode-bytes-example

    use Ramsey\Uuid\FeatureSet;
    use Ramsey\Uuid\UuidFactory;

    // The bytes of a GUID previously stored in some datastore.
    $guidBytes = hex2bin('0eab93fc9ec9584b975e9c5e68c53624');

    $useGuids = true;
    $featureSet = new FeatureSet($useGuids);
    $factory = new UuidFactory($featureSet);

    $guid = $factory->fromBytes($guidBytes);

    printf(
        "Class: %s\nGUID: %s\nVersion: %d\nBytes: %s\n",
        get_class($guid),
        $guid->toString(),
        $guid->getFields()->getVersion(),
        bin2hex($guid->getBytes())
    );

This transforms the bytes of a GUID, as represented by ``$guidBytes``, into a :php:class:`Ramsey\\Uuid\\Guid\\Guid`
instance and prints out some details about it. It looks something like this:

.. code-block:: text

    Class: Ramsey\Uuid\Guid\Guid
    GUID: fc93ab0e-c99e-4b58-975e-9c5e68c53624
    Version: 4
    Bytes: 0eab93fc9ec9584b975e9c5e68c53624

Note the difference between the string GUID and the bytes. The bytes are arranged like this:

.. code-block:: text

    0e ab 93 fc 9e c9 58 4b 97 5e 9c 5e 68 c5 36 24

In an `RFC 9562`_ (formerly `RFC 4122`_) UUID, the bytes are stored in the same order as you see presented in the string
representation. This is often called *network byte order*, or *big-endian* order. In a GUID, the order of the bytes are
reversed in each grouping for the first 64 bits and stored in *little-endian* order. The remaining 64 bits are stored in
network byte order. See `Endianness <#nonstandard-guid-endianness>`_ to learn more.

.. caution::

    The bytes themselves do not indicate their order. If you decode GUID bytes as a UUID or UUID bytes as a GUID, you
    will get the wrong values. However, you can always create a GUID or UUID from the same string value; the bytes for
    each will be in a different order, even though the string is the same.

    The key is to know ahead of time in what order the bytes are stored. Then, you will be able to decode them using the
    correct approach.

Converting GUIDs to UUIDs
#########################

Continuing from the example, :ref:`nonstandard.guid.decode-bytes-example`, we can take the GUID string representation
and convert it into a standard UUID.

.. code-block:: php
    :caption: Convert a GUID to a UUID
    :name: nonstandard.guid.convert-example

    $uuid = Uuid::fromString($guid->toString());

    printf(
        "Class: %s\nUUID: %s\nVersion: %d\nBytes: %s\n",
        get_class($uuid),
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        bin2hex($uuid->getBytes())
    );

Because the GUID was a version 4, random UUID, this creates an instance of :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV4`
from the GUID string and prints out a few details about it. It looks something like this:

.. code-block:: text

    Class: Ramsey\Uuid\Rfc4122\UuidV4
    UUID: fc93ab0e-c99e-4b58-975e-9c5e68c53624
    Version: 4
    Bytes: fc93ab0ec99e4b58975e9c5e68c53624

Note how the UUID string is identical to the GUID string. However, the byte order is different, since they are in
big-endian order. The bytes are now arranged like this:

.. code-block:: text

    fc 93 ab 0e c9 9e 4b 58 97 5e 9c 5e 68 c5 36 24

.. admonition:: Endianness
    :name: nonstandard.guid.endianness

    Big-endian and little-endian refer to the ordering of bytes in a multi-byte number. Big-endian order places the most
    significant byte first, followed by the other bytes in descending order. Little-endian order places the least
    significant byte first, followed by the other bytes in ascending order.

    Take the hexadecimal number ``0x1234``, for example. In big-endian order, the bytes are stored as ``12 34``, and in
    little-endian order, they are stored as ``34 12``. In either case, the number is still ``0x1234``.

    Networking protocols usually use big-endian ordering, while computer processor architectures often use little-endian
    ordering.

    The terms originated in Jonathan Swift's *Gulliver's Travels*, where the Lilliputians argue over which end of a
    hard-boiled egg is the best end to crack.

.. _GUID structure: https://learn.microsoft.com/en-us/windows/win32/api/guiddef/ns-guiddef-guid
.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _.NET Framework documentation: https://learn.microsoft.com/en-us/dotnet/api/system.guid.tobytearray
