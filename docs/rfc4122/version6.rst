.. _rfc4122.version6:

===================================
Version 6: Reordered Gregorian Time
===================================

.. attention::

    If you need a time-based UUID, and you don't need the other features included in version 6 UUIDs, we recommend using
    :ref:`version 7 UUIDs <rfc4122.version7>`.

Version 6 UUIDs solve `two problems that have long existed`_ with the use of :ref:`version 1 <rfc4122.version1>` UUIDs:

1. Scattered database records
2. Inability to sort by an identifier in a meaningful way (i.e., insert order)

To overcome these issues, we need the ability to generate UUIDs that are *monotonically increasing* while still
providing all the benefits of version 1 UUIDs.

Version 6 UUIDs do this by storing the time in standard byte order, instead of breaking it up and rearranging the time
bytes, according to the `RFC 9562`_ (formerly `RFC 4122`_) definition for version 1 UUIDs. All other fields remain the
same.

In all other ways, version 6 UUIDs function like version 1 UUIDs.

.. tip::

    Prior to version 4.0.0, ramsey/uuid provided a solution for this with the :ref:`ordered-time codec
    <customize.ordered-time-codec>`. The ordered-time codec is now deprecated and will be removed in ramsey/uuid 5.0.0.
    However, you may replace UUIDs generated using the ordered-time codec with version 6 UUIDs. Keep reading to find out
    how.

.. code-block:: php
    :caption: Generate a version 6, reordered Gregorian time UUID
    :name: rfc4122.version6.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid6();

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
        $uuid->getFields()->getNode()->toString()
    );

This will generate a version 6 UUID and print out its string representation, the time the UUID was created, and the node
used to create the UUID.

It will look something like this:

.. code-block:: text

    UUID: 1ea60f56-b67b-61fc-829a-0242ac130003
    Version: 6
    Date: Sun, 08 Mar 2020 04:29:37 +0000
    Node: 0242ac130003

You may provide custom values for version 6 UUIDs, including node and clock sequence.

.. code-block:: php
    :caption: Provide custom node and clock sequence to create a version 6, reordered Gregorian time UUID
    :name: rfc4122.version6.custom-example

    use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
    use Ramsey\Uuid\Type\Hexadecimal;
    use Ramsey\Uuid\Uuid;

    $nodeProvider = new StaticNodeProvider(new Hexadecimal('121212121212'));
    $clockSequence = 16383;

    $uuid = Uuid::uuid6($nodeProvider->getNode(), $clockSequence);

.. tip::

    Version 6 UUIDs generated in ramsey/uuid are instances of UuidV6. Check out the
    :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV6` API documentation to learn more about what you can do with a UuidV6
    instance.

.. _rfc4122.version6.nodes:

Custom and Random Nodes
#######################

In the :ref:`example above <rfc4122.version6.custom-example>`, we provided a custom node when generating a version 6
UUID. You may also generate random node values.

To learn more, see the :ref:`rfc4122.version1.custom` and :ref:`rfc4122.version1.random` sections under
:ref:`rfc4122.version1`.

.. _rfc4122.version6.clock:

Clock Sequence
##############

In a version 6 UUID, the clock sequence serves the same purpose as in a version 1 UUID. See :ref:`rfc4122.version1.clock`
to learn more.

.. _rfc4122.version6.version1-conversion:

Version 1-to-6 Conversion
#########################

It is possible to convert back-and-forth between version 6 and version 1 UUIDs.

.. code-block:: php
    :caption: Convert a version 1 UUID to a version 6 UUID
    :name: rfc4122.version6.convert-version1-example

    use Ramsey\Uuid\Rfc4122\UuidV1;
    use Ramsey\Uuid\Rfc4122\UuidV6;
    use Ramsey\Uuid\Uuid;

    $uuid1 = Uuid::fromString('3960c5d8-60f8-11ea-bc55-0242ac130003');

    if ($uuid1 instanceof UuidV1) {
        $uuid6 = UuidV6::fromUuidV1($uuid1);
    }

.. code-block:: php
    :caption: Convert a version 6 UUID to a version 1 UUID
    :name: rfc4122.version6.convert-version6-example

    use Ramsey\Uuid\Rfc4122\UuidV6;
    use Ramsey\Uuid\Uuid;

    $uuid6 = Uuid::fromString('1ea60f83-960c-65d8-bc55-0242ac130003');

    if ($uuid6 instanceof UuidV6) {
        $uuid1 = $uuid6->toUuidV1();
    }

.. _rfc4122.version6.ordered-time-conversion:

Ordered-time to Version 6 Conversion
####################################

You may convert UUIDs previously generated and stored using the :ref:`ordered-time codec <customize.ordered-time-codec>`
into version 6 UUIDs.

.. caution::

    If you perform this conversion, the bytes and string representation of your UUIDs will change. This will break any
    software that expects your identifiers to be fixed.

.. code-block:: php
    :caption: Convert an ordered-time codec encoded UUID to a version 6 UUID
    :name: rfc4122.version6.convert-ordered-time-example

    use Ramsey\Uuid\Codec\OrderedTimeCodec;
    use Ramsey\Uuid\Rfc4122\UuidV1;
    use Ramsey\Uuid\Rfc4122\UuidV6;
    use Ramsey\Uuid\UuidFactory;

    // The bytes of a version 1 UUID previously stored in some datastore
    // after encoding to bytes with the OrderedTimeCodec.
    $bytes = hex2bin('11ea60faf17c8af6ad23acde48001122');

    $factory = new UuidFactory();
    $codec = new OrderedTimeCodec($factory->getUuidBuilder());

    $factory->setCodec($codec);

    $orderedTimeUuid = $factory->fromBytes($bytes);

    if ($orderedTimeUuid instanceof UuidV1) {
        $uuid6 = UuidV6::fromUuidV1($orderedTimeUuid);
    }

.. _rfc4122.version6.privacy:

Privacy Concerns
################

Like :ref:`version 1 UUIDs <rfc4122.version1>`, version 6 UUIDs use a MAC address from a local hardware network
interface. This means it is possible to uniquely identify the machine on which a version 6 UUID was created.

If the value provided by the timestamp of a version 6 UUID is important to you, but you do not wish to expose the
interface address of any of your local machines, see :ref:`rfc4122.version6.nodes`.

If you do not need an identifier with a node value embedded in it, but you still need the benefit of a monotonically
increasing unique identifier, see :ref:`rfc4122.version7`.

.. _two problems that have long existed: https://www.percona.com/blog/store-uuid-optimized-way/
.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
