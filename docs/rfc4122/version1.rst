.. _rfc4122.version1:

=====================
Version 1: Time-based
=====================

A version 1 UUID uses the current time, along with the MAC address (or *node*)
for a network interface on the local machine. This serves two purposes:

1. You can know *when* the identifier was created.
2. You can know *where* the identifier was created.

In a distributed system, these two pieces of information can be valuable. Not
only is there no need for a central authority to generate identifiers, but you
can determine what nodes in your infrastructure created the UUIDs and at what
time.

.. tip::
    It is also possible to use a **randomly-generated node**, rather than a
    hardware address. This is useful for when you don't want to leak machine
    information, while still using a UUID based on time. Keep reading to find
    out how.


Default Mode
############

By default, ramsey/uuid will attempt to look up a MAC address for the machine it
is running on, using this value as the node. If it cannot find a MAC address, it
will generate a random node.

.. code-block:: php

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid1();

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
        $uuid->getFields()->getNode()->toString()
    );

This will generate a version 1 UUID and print out its string representation, the
time the UUID was created, and the node used to create the UUID.

It will look something like this:

.. code-block:: text

    UUID: e22e1622-5c14-11ea-b2f3-acde48001122
    Version: 1
    Date: Sun, 01 Mar 2020 23:32:15 +0000
    Node: acde48001122

After creating a ``UuidInterface`` object from a string (or bytes), you can
check to see if it's a version 1 UUID by checking its instance type.

.. code-block:: php

    use Ramsey\Uuid\Rfc4122\UuidV1;
    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::fromString('200e43fa-5c14-11ea-bc55-0242ac130003');

    if ($uuid instanceof UuidV1) {
        printf(
            "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\n",
            $uuid->toString(),
            $uuid->getFields()->getVersion(),
            $uuid->getDateTime()->format('r'),
            $uuid->getFields()->getNode()->toString()
        );
    }

.. tip::
    Check out the :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface` API
    documentation to learn more about what you can do with a ``UuidV1``
    instance.


Random or Custom Node
#####################

You may override the default behavior by passing your own node value when
generating a version 1 UUID.

In the following example, we use ``RandomNodeProvider`` to generate a random
node, which we pass when creating the UUID.

.. code-block:: php

    use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
    use Ramsey\Uuid\Uuid;

    $nodeProvider = new RandomNodeProvider();

    $uuid = Uuid::uuid1($nodeProvider->getNode());

You may also set the node value of your choice. In this example, we use
``StaticNodeProvider`` to do so.

.. code-block:: php

    use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
    use Ramsey\Uuid\Type\Hexadecimal;
    use Ramsey\Uuid\Uuid;

    $myCustomNode = new Hexadecimal('1234567890ab');
    $nodeProvider = new StaticNodeProvider($myCustomNode);

    $uuid = Uuid::uuid1($nodeProvider->getNode());

.. attention::
    According to RFC 4122, nodes that do not identify the host should set the
    unicast/multicast bit to one (``1``). This bit will never be set in IEEE 802
    addresses obtained from network cards, so it helps to distinguish it from a
    hardware MAC address.

    ``RandomNodeProvider`` and ``StaticNodeProvider`` of ramsey/uuid set this
    bit for you, so they’re the easiest to use, but if you use a custom node
    provider, be sure to set this bit.

    See `RFC 4122, section 4.5 <https://tools.ietf.org/html/rfc4122#section-4.5>`_,
    for more details.


Using the Factory
#################

It is possible to override the behavior of ``Uuid::uuid1()`` globally, by
overriding values on the ``FeatureSet`` and ``UuidFactory``.

For example, if you wish to always use a specific node whenever ``Uuid::uuid1()``
is called, you may do the following:

.. code-block:: php

    use Ramsey\Uuid\FeatureSet;
    use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
    use Ramsey\Uuid\Type\Hexadecimal;
    use Ramsey\Uuid\Uuid;
    use Ramsey\Uuid\UuidFactory;

    $nodeProvider = new StaticNodeProvider(new Hexadecimal('1234567890ab'));

    $featureSet = new FeatureSet();
    $featureSet->setNodeProvider($nodeProvider);

    $factory = new UuidFactory($featureSet);

    Uuid::setFactory($factory);

    $uuid = Uuid::uuid1();
