.. _rfc4122.version1:

=========================
Version 1: Gregorian Time
=========================

.. attention::

    If you need a time-based UUID, and you don't need the other features included in version 1 UUIDs, we recommend using
    :ref:`version 7 UUIDs <rfc4122.version7>`.

A version 1 UUID uses the current time, along with the MAC address (or *node*) for a network interface on the local
machine. This serves two purposes:

1. You can know *when* the identifier was created.
2. You can know *where* the identifier was created.

In a distributed system, these two pieces of information can be valuable. Not only is there no need for a central
authority to generate identifiers, but you can determine what nodes in your infrastructure created the UUIDs and at what
time.

.. tip::

    It is also possible to use a **randomly-generated node**, rather than a hardware address. This is useful for when
    you don't want to leak machine information, while still using a UUID based on time. Keep reading to find out how.

By default, ramsey/uuid will attempt to look up a MAC address for the machine it is running on, using this value as the
node. If it cannot find a MAC address, it will generate a random node.

.. code-block:: php
    :caption: Generate a version 1, Gregorian time UUID
    :name: rfc4122.version1.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid1();

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
        $uuid->getFields()->getNode()->toString()
    );

This will generate a version 1 UUID and print out its string representation, the time the UUID was created, and the node
used to create the UUID.

It will look something like this:

.. code-block:: text

    UUID: e22e1622-5c14-11ea-b2f3-0242ac130003
    Version: 1
    Date: Sun, 01 Mar 2020 23:32:15 +0000
    Node: 0242ac130003

You may provide custom values for version 1 UUIDs, including node and clock sequence.

.. code-block:: php
    :caption: Provide custom node and clock sequence to create a version 1,
              Gregorian time UUID
    :name: rfc4122.version1.custom-example

    use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
    use Ramsey\Uuid\Type\Hexadecimal;
    use Ramsey\Uuid\Uuid;

    $nodeProvider = new StaticNodeProvider(new Hexadecimal('121212121212'));
    $clockSequence = 16383;

    $uuid = Uuid::uuid1($nodeProvider->getNode(), $clockSequence);

.. tip::

    Version 1 UUIDs generated in ramsey/uuid are instances of UuidV1. Check out the :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV1`
    API documentation to learn more about what you can do with a UuidV1 instance.

.. _rfc4122.version1.custom:

Providing a Custom Node
#######################

You may override the default behavior by passing your own node value when generating a version 1 UUID.

In the :ref:`example above <rfc4122.version1.custom-example>`, we saw how to pass a custom node and clock sequence. An
interesting thing to note about the example is its use of StaticNodeProvider. Why didn't we pass in a
:php:class:`Hexadecimal <Ramsey\\Uuid\\Type\\Hexadecimal>` value, instead?

According to `RFC 9562, section 6.10`_, node values that do not identify the host --- in other words, our own custom
node value --- should set the unicast/multicast bit to one (1). This bit will never be set in IEEE 802 addresses
obtained from network cards, so it helps to distinguish it from a hardware MAC address.

The StaticNodeProvider sets this bit for you. This is why we used it rather than providing a :php:class:`Hexadecimal
<Ramsey\\Uuid\\Type\\Hexadecimal>` value directly.

Recall from the example that the node value we set was ``121212121212``, but if you take a look at this value with
``$uuid->getFields()->getNode()->toString()``, it becomes:

.. code-block:: text

    131212121212

That's a result of this bit being set by the StaticNodeProvider.

.. _rfc4122.version1.random:

Generating a Random Node
########################

Instead of providing a custom node, you may also generate a random node each time you generate a version 1 UUID. The
RandomNodeProvider may be used to generate a random node value, and like the StaticNodeProvider, it also sets the
unicast/multicast bit for you.

.. code-block:: php
    :caption: Provide a random node value to create a version 1, Gregorian time UUID
    :name: rfc4122.version1.random-example

    use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
    use Ramsey\Uuid\Uuid;

    $nodeProvider = new RandomNodeProvider();

    $uuid = Uuid::uuid1($nodeProvider->getNode());

.. _rfc4122.version1.clock:

What's a Clock Sequence?
########################

The clock sequence part of a version 1 UUID helps prevent collisions. Since this UUID is based on a timestamp and a
machine node value, it is possible for collisions to occur for multiple UUIDs generated within the same microsecond on
the same machine.

The clock sequence is the solution to this problem.

The clock sequence is a 14-bit number --- this supports values from 0 to 16,383 --- which means it should be possible to
generate up to 16,384 UUIDs per microsecond with the same node value, before hitting a collision.

.. caution::

    ramsey/uuid does not use *stable storage* for clock sequence values. Instead, all clock sequences are
    randomly-generated. If you are generating a lot of version 1 UUIDs every microsecond, it is possible to hit
    collisions because of the random values. If this is the case, you should use your own mechanism for generating clock
    sequence values, to ensure against randomly-generated duplicates.

    See `section 6.3 of RFC 9562`_, for more information.

.. _rfc4122.version1.privacy:

Privacy Concerns
################

As discussed earlier in this section, version 1 UUIDs use a MAC address from a local hardware network interface. This
means it is possible to uniquely identify the machine on which a version 1 UUID was created.

If the value provided by the timestamp of a version 1 UUID is important to you, but you do not wish to expose the
interface address of any of your local machines, see :ref:`rfc4122.version1.random` or :ref:`rfc4122.version1.custom`.

If you do not need an identifier with a timestamp value embedded in it, see :ref:`rfc4122.version4` to learn about
random UUIDs.

.. _RFC 9562, section 6.10: https://www.rfc-editor.org/rfc/rfc9562#section-6.10
.. _section 6.3 of RFC 9562: https://www.rfc-editor.org/rfc/rfc9562#section-6.3
