.. _rfc4122.version8:

========================
Version 8: Custom Format
========================

Version 8 UUIDs allow applications to create custom UUIDs in an RFC-compatible way. The only requirement is the version
and variant bits must be set according to the UUID specification. The bytes provided may contain any value according to
your application's needs. Be aware, however, that other applications may not understand the semantics of the value.

.. warning::

    The bytes should be a 16-byte octet string, an open blob of data that you may fill with 128 bits of information.
    However, bits 48 through 51 will be replaced with the UUID version field, and bits 64 and 65 will be replaced with
    the UUID variant. You must not rely on these bits for your application needs.

.. code-block:: php
    :caption: Generate a version 8, custom UUID
    :name: rfc4122.version8.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid8("\x00\x11\x22\x33\x44\x55\x66\x77\x88\x99\xaa\xbb\xcc\xdd\xee\xff");

    printf(
        "UUID: %s\nVersion: %d\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion()
    );

This will generate a version 8 UUID and print out its string representation. It will look something like this:

.. code-block:: text

    UUID: 00112233-4455-8677-8899-aabbccddeeff
    Version: 8
