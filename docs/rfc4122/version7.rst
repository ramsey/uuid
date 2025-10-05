.. _rfc4122.version7:

==========================
Version 7: Unix Epoch Time
==========================

.. admonition:: ULIDs and Version 7 UUIDs
    :class: hint

    Version 7 UUIDs are binary-compatible with `ULIDs`_ (universally unique lexicographically-sortable identifiers).

    Both use a 48-bit timestamp in milliseconds since the Unix Epoch, filling the rest with random data. Version 7 UUIDs
    then add the version and variant bits required by the UUID specification, which reduces the randomness from 80 bits
    to 74. Otherwise, they are identical.

    You may even convert a version 7 UUID to a ULID. :ref:`See below for an example. <rfc4122.version7.ulid>`

Version 7 UUIDs solve `two problems that have long existed`_ with the use of :ref:`version 1 <rfc4122.version1>` UUIDs:

1. Scattered database records
2. Inability to sort by an identifier in a meaningful way (i.e., insert order)

To overcome these issues, we need the ability to generate UUIDs that are *monotonically increasing*.

:ref:`Version 6 UUIDs <rfc4122.version6>` provide an excellent solution for those who need monotonically increasing,
sortable UUIDs with the features of version 1 UUIDs (MAC address and clock sequence), but if those features aren't
necessary for your application, using a version 6 UUID might be overkill.

Version 7 UUIDs combine random data (like version 4 UUIDs) with a timestamp (in milliseconds since the Unix Epoch, i.e.,
1970-01-01 00:00:00 UTC) to create a monotonically increasing, sortable UUID that doesn't have any privacy concerns,
since it doesn't include a MAC address.

For this reason, implementations should use version 7 UUIDs over versions 1 and 6, if possible.

.. code-block:: php
    :caption: Generate a version 7, Unix Epoch time UUID
    :name: rfc4122.version7.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid7();

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
    );

This will generate a version 7 UUID and print out its string representation and the time it was created.

It will look something like this:

.. code-block:: text

    UUID: 01833ce0-3486-7bfd-84a1-ad157cf64005
    Version: 7
    Date: Wed, 14 Sep 2022 16:41:10 +0000

To use an existing date and time to generate a version 7 UUID, you may pass a ``\DateTimeInterface`` instance to the
``uuid7()`` method.

.. code-block:: php
    :caption: Generate a version 7 UUID from an existing date and time
    :name: rfc4122.version7.example-datetime

    use DateTimeImmutable;
    use Ramsey\Uuid\Uuid;

    $dateTime = new DateTimeImmutable('@281474976710.655');
    $uuid = Uuid::uuid7($dateTime);

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
    );

Which will print something like this:

.. code-block:: text

    UUID: ffffffff-ffff-7964-a8f6-001336ac20cb
    Version: 7
    Date: Tue, 02 Aug 10889 05:31:50 +0000

.. tip::

    Version 7 UUIDs generated in ramsey/uuid are instances of UuidV7. Check out the
    :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV7` API documentation to learn more about what you can do with a UuidV7
    instance.

.. _rfc4122.version7.ulid:

Convert a Version 7 UUID to a ULID
##################################

As mentioned in the callout above, version 7 UUIDs are binary-compatible with `ULIDs`_. This means you can encode a
version 7 UUID using `Crockford's Base 32 algorithm`_ and it will be a valid ULID, timestamp and all.

Using the third-party library `tuupola/base32`_, here's how we can encode a version 7 UUID as a ULID. Note that there's
a little bit of work to perform the conversion, since you're working with different bases.

.. code-block:: php
    :caption: Encode a version 7, Unix Epoch time UUID as a ULID
    :name: rfc4122.version7.example-ulid

    use Ramsey\Uuid\Uuid;
    use Tuupola\Base32;

    $crockford = new Base32([
        'characters' => Base32::CROCKFORD,
        'padding' => false,
        'crockford' => true,
    ]);

    $uuid = Uuid::uuid7();

    // First, we must pad the 16-byte string to 20 bytes
    // for proper conversion without data loss.
    $bytes = str_pad($uuid->getBytes(), 20, "\x00", STR_PAD_LEFT);

    // Use Crockford's Base 32 encoding algorithm.
    $encoded = $crockford->encode($bytes);

    // That 20-byte string was encoded to 32 characters to avoid loss
    // of data. We must strip off the first 6 characters--which are
    // all zeros--to get a valid 26-character ULID string.
    $ulid = substr($encoded, 6);

    printf("ULID: %s\n", $ulid);

This will print something like this:

.. code-block:: text

    ULID: 01GCZ05N3JFRKBRWKNGCQZGP44

.. caution::

    Be aware that all version 7 UUIDs may be converted to ULIDs but not all ULIDs may be converted to UUIDs.

    For that matter, all UUIDs of any version may be encoded as ULIDs, but they will not be monotonically increasing and
    sortable unless they are version 7 UUIDs. You will also not be able to extract a meaningful timestamp from the ULID,
    unless it was converted from a version 7 UUID.

.. _ULIDs: https://github.com/ulid/spec
.. _two problems that have long existed: https://www.percona.com/blog/store-uuid-optimized-way/
.. _Crockford's Base 32 algorithm: https://www.crockford.com/base32.html
.. _tuupola/base32: https://packagist.org/packages/tuupola/base32
