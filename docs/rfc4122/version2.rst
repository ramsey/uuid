.. _rfc4122.version2:

=======================
Version 2: DCE Security
=======================

.. tip::

    DCE Security UUIDs are so-called because they were defined as part of the "Authentication and Security Services" for
    the `Distributed Computing Environment`_ (DCE) in the early 1990s.

    Version 2 UUIDs are not widely used. See :ref:`rfc4122.version2.problems` before deciding whether to use them.

Like a :ref:`version 1 UUID <rfc4122.version1>`, a version 2 UUID uses the current time, along with the MAC address (or
*node*) for a network interface on the local machine. Additionally, a version 2 UUID replaces the low part of the time
field with a local identifier such as the user ID or group ID of the local account that created the UUID. This serves
three purposes:

1. You can know *when* the identifier was created (see :ref:`rfc4122.version2.timestamp-problems`).
2. You can know *where* the identifier was created.
3. You can know *who* created the identifier.

In a distributed system, these three pieces of information can be valuable. Not only is there no need for a central
authority to generate identifiers, but you can determine what nodes in your infrastructure created the UUIDs, at what
time they were created, and the account on the machine that created them.

By default, ramsey/uuid will attempt to look up a MAC address for the machine it is running on, using this value as the
node. If it cannot find a MAC address, it will generate a random node.

.. code-block:: php
    :caption: Use a domain to generate a version 2, DCE Security UUID
    :name: rfc4122.version2.example

    use Ramsey\Uuid\Uuid;

    $uuid = Uuid::uuid2(Uuid::DCE_DOMAIN_PERSON);

    printf(
        "UUID: %s\nVersion: %d\nDate: %s\nNode: %s\nDomain: %s\nID: %s\n",
        $uuid->toString(),
        $uuid->getFields()->getVersion(),
        $uuid->getDateTime()->format('r'),
        $uuid->getFields()->getNode()->toString(),
        $uuid->getLocalDomainName(),
        $uuid->getLocalIdentifier()->toString()
    );

This will generate a version 2 UUID and print out its string representation, the time the UUID was created, and the node
used to create it, as well as the name of the local domain specified and the local domain identifier (in this case, a
`POSIX`_ UID, automatically obtained from the local machine).

It will look something like this:

.. code-block:: text

    UUID: 000001f5-5e9a-21ea-9e00-0242ac130003
    Version: 2
    Date: Thu, 05 Mar 2020 04:30:10 +0000
    Node: 0242ac130003
    Domain: person
    ID: 501

Just as with version 1 UUIDs, you may provide custom values for version 2 UUIDs, including local identifier, node, and
clock sequence.

.. code-block:: php
    :caption: Provide custom identifier, node, and clock sequence to create a
              version 2, DCE Security UUID
    :name: rfc4122.version2.custom-example

    use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
    use Ramsey\Uuid\Type\Hexadecimal;
    use Ramsey\Uuid\Type\Integer;
    use Ramsey\Uuid\Uuid;

    $localId = new Integer(1001);
    $nodeProvider = new StaticNodeProvider(new Hexadecimal('121212121212'));
    $clockSequence = 63;

    $uuid = Uuid::uuid2(
        Uuid::DCE_DOMAIN_ORG,
        $localId,
        $nodeProvider->getNode(),
        $clockSequence
    );

.. tip::

    Version 2 UUIDs generated in ramsey/uuid are instances of UuidV2. Check out the :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV2`
    API documentation to learn more about what you can do with a UuidV2 instance.

.. _rfc4122.version2.domains:

Domains
#######

The *domain* value tells what the local identifier represents.

If using the *person* or *group* domains, ramsey/uuid will attempt to look up these values from the local machine. On
`POSIX`_ systems, it will use ``id -u`` and ``id -g``, respectively. On Windows, it will use ``whoami`` and ``wmic``.

The *org* domain is site-defined. Its intent is to identify the organization that generated the UUID, but since this can
have different meanings for different companies and projects, you get to define its value.

.. list-table:: DCE Security Domains
    :widths: 30 70
    :align: center
    :header-rows: 1
    :name: rfc4122.version2.table-domains

    * - Constant
      - Description
    * - :php:const:`Uuid::DCE_DOMAIN_PERSON <Ramsey\\Uuid\\Uuid::DCE_DOMAIN_PERSON>`
      - The local identifier refers to a *person* (e.g., UID).
    * - :php:const:`Uuid::DCE_DOMAIN_GROUP <Ramsey\\Uuid\\Uuid::DCE_DOMAIN_GROUP>`
      - The local identifier refers to a *group* (e.g., GID).
    * - :php:const:`Uuid::DCE_DOMAIN_ORG <Ramsey\\Uuid\\Uuid::DCE_DOMAIN_ORG>`
      - The local identifier refers to an *organization* (this is site-defined).

.. note::

    According to section 5.2.1.1 of `DCE 1.1: Authentication and Security Services <https://publications.opengroup.org/c311>`_,
    the domain "can potentially hold values outside the range [0, 2\ :sup:`8` -- 1]; however, the only values currently
    registered are in the range [0, 2]."

    As a result, ramsey/uuid supports only the *person*, *group*, and *org* domains.

.. _rfc4122.version2.nodes:

Custom and Random Nodes
#######################

In the :ref:`example above <rfc4122.version2.custom-example>`, we provided a custom node when generating a version 2
UUID. You may also generate random node values.

To learn more, see the :ref:`rfc4122.version1.custom` and :ref:`rfc4122.version1.random` sections under
:ref:`rfc4122.version1`.

.. _rfc4122.version2.clock:

Clock Sequence
##############

In a version 2 UUID, the clock sequence serves the same purpose as in a version 1 UUID. See :ref:`rfc4122.version1.clock`
to learn more.

.. warning::

    The clock sequence in a version 2 UUID is a 6-bit number. It supports values from 0 to 63. This is different from
    the 14-bit number used by version 1 UUIDs.

    See :ref:`rfc4122.version2.uniqueness-problems` to understand how this affects version 2 UUIDs.

.. _rfc4122.version2.problems:

Problems With Version 2 UUIDs
#############################

Version 2 UUIDs can be useful for the data they contain. However, there are trade-offs in choosing to use them.

.. _rfc4122.version2.privacy-problems:

Privacy
-------

Unless using a randomly-generated node, version 2 UUIDs use the MAC address for a local hardware interface as the node
value. In addition, they use a local identifier --- usually an account or group ID. Some may consider the use of these
identifying features a breach of privacy. The use of a timestamp further complicates the issue, since these UUIDs could
be used to identify a user account on a specific machine at a specific time.

If you don't need an identifier with a local identifier and timestamp value embedded in it, see :ref:`rfc4122.version4`
to learn about random UUIDs.

.. _rfc4122.version2.uniqueness-problems:

Limited Uniqueness
------------------

With the inclusion of the local identifier and domain comes a serious limitation in the number of unique UUIDs that may
be created. This is because:

1. The local identifier replaces the lower 32 bits of the timestamp.
2. The domain replaces the lower 8 bits of the clock sequence.

As a result, the timestamp advances --- the clock *ticks* --- only once every 429.49 seconds (about 7 minutes). This
means the clock sequence is important to ensure uniqueness, but since the clock sequence is only 6 bits, compared to 14
bits for version 1 UUIDs, **only 64 unique UUIDs per combination of node, domain, and identifier may be generated per
7-minute tick of the clock**.

You can overcome this lack of uniqueness by using a :ref:`random node <rfc4122.version2.nodes>`, which provides 47 bits
of randomness to the UUID --- after setting the unicast/multicast bit (see discussion on :ref:`rfc4122.version1.custom`)
--- increasing the number of UUIDs per 7-minute clock tick to 2\ :sup:`53` (or 9,007,199,254,740,992), at the expense of
remaining locally unique.

.. note::

    This lack of uniqueness did not present a problem for DCE, since:

        [T]he security architecture of DCE depends upon the uniqueness of security-version UUIDs *only within the
        context of a cell*; that is, only within the context of the local [Registration Service's] (persistent)
        datastore, and that degree of uniqueness can be guaranteed by the RS itself (namely, the RS maintains state in
        its datastore, in the sense that it can always check that every UUID it maintains is different from all other
        UUIDs it maintains). In other words, while security-version UUIDs are (like all UUIDs) specified to be "globally
        unique in space and time", security is not compromised if they are merely "locally unique per cell".

        -- `DCE 1.1: Authentication and Security Services, section 5.2.1.1 <https://publications.opengroup.org/c311>`_

.. _rfc4122.version2.timestamp-problems:

Lossy Timestamps
----------------

Version 2 UUIDs are generated in the same way as version 1 UUIDs, but the low part of the timestamp (the ``time_low``
field) is replaced by a 32-bit integer that represents a local identifier. Because of this, not only do version 2 UUIDs
have :ref:`limited uniqueness <rfc4122.version2.uniqueness-problems>`, but they also lack time precision.

When reconstructing the timestamp to return a `DateTimeInterface`_ instance from :php:meth:`UuidV2::getDateTime()
<Ramsey\\Uuid\\Rfc4122\\UuidV2::getDateTime>`, we replace the 32 lower bits of the timestamp with zeros, since the local
identifier should not be part of the timestamp. This results in a loss of precision, causing the timestamp to be off by
a range of 0 to 429.4967295 seconds (or 7 minutes, 9 seconds, and 496,730 microseconds).

When using version 2 UUIDs, treat the timestamp as an approximation. At worst, it could be off by about 7 minutes.

.. hint::

    If the value 429.4967295 looks familiar, it's because it directly corresponds to 2\ :sup:`32` -- 1, or ``0xffffffff``.
    The local identifier is 32-bits, and we have set each of these bits to 0, so the maximum range of timestamp drift is
    ``0x00000000`` to ``0xffffffff`` (counted in 100-nanosecond intervals).

.. _Distributed Computing Environment: https://en.wikipedia.org/wiki/Distributed_Computing_Environment
.. _POSIX: https://en.wikipedia.org/wiki/POSIX
.. _DateTimeInterface: https://www.php.net/datetimeinterface
