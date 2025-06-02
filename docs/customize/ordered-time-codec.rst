.. _customize.ordered-time-codec:

==================
Ordered-time Codec
==================

.. attention::

    The :php:class:`Ramsey\\Uuid\\Codec\\OrderedTimeCodec` class is deprecated. Please migrate to
    :ref:`version 6, reordered Gregorian time UUIDs <rfc4122.version6>`.

UUIDs arrange their bytes according to the standard recommended by `RFC 9562`_ (formerly `RFC 4122`_). Unfortunately,
this means the bytes aren't in an arrangement that supports sorting by creation time or an otherwise incrementing value.
The Percona article, "`Storing UUID Values in MySQL`_," explains at length the problems this can cause. It also
recommends a solution: the *ordered-time UUID*.

`RFC 9562 version 1, Gregorian time UUIDs <https://www.rfc-editor.org/rfc/rfc9562#section-5.1>`_ rearrange the bytes of
the time fields so that the lowest bytes appear first, the middle bytes are next, and the highest bytes come last.
Logical sorting is not possible with this arrangement.

An ordered-time UUID is a version 1 UUID with the time fields arranged in logical order so that the UUIDs can be sorted
by creation time. These UUIDs are *monotonically increasing*, each one coming after the previously-created one, in a
proper sort order.

.. code-block:: php
    :caption: Use the ordered-time codec to generate a version 1 UUID
    :name: customize.ordered-time-codec-example

    use Ramsey\Uuid\Codec\OrderedTimeCodec;
    use Ramsey\Uuid\UuidFactory;

    $factory = new UuidFactory();
    $codec = new OrderedTimeCodec($factory->getUuidBuilder());

    $factory->setCodec($codec);

    $orderedTimeUuid = $factory->uuid1();

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\nBytes: %s\n",
        $orderedTimeUuid->toString(),
        $orderedTimeUuid->getFields()->getVersion(),
        $orderedTimeUuid->getDateTime()->format('r'),
        $orderedTimeUuid->getFields()->getNode()->toString(),
        bin2hex($orderedTimeUuid->getBytes())
    );

This will use the ordered-time codec to generate a version 1 UUID and will print out details about the UUID similar to these:

.. code-block:: text

    UUID: 593200aa-61ae-11ea-bbf2-0242ac130003
    Version: 1
    Date: Mon, 09 Mar 2020 02:33:23 +0000
    Node: 0242ac130003
    Bytes: 11ea61ae593200aabbf20242ac130003

.. attention::

    Only the byte representation is rearranged. The string representation follows the format of a standard version 1
    UUID. This means only the byte representation of an ordered-time codec encoded UUID may be used for sorting, such as
    with database results.

    To store the byte representation to a database field, see :ref:`database.bytes`.

.. hint::

    If you use this codec and store the bytes of the UUID to the database, as recommended above, you will need to use
    this codec to decode the bytes, as well. Otherwise, the UUID string value will be incorrect.

    .. code-block:: php

        // Using a factory configured with the OrderedTimeCodec, as shown above.
        $orderedTimeUuid = $factory->fromBytes($bytes);

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
.. _Storing UUID Values in MySQL: https://www.percona.com/blog/store-uuid-optimized-way/
