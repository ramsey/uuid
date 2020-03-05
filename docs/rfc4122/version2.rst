.. _rfc4122.version2:

=======================
Version 2: DCE Security
=======================

Like a :ref:`version 1 UUID <rfc4122.version1>`, a version 2 UUID uses the
current time, along with the MAC address (or *node*) for a network interface on
the local machine. Additionally, a version 2 UUID replaces the low part of the
time field with a local identifier such as the user ID or group ID of the local
account that created the UUID. This serves three purposes:

1. You can know *when* the identifier was created (see :ref:`rfc4122.version2.caveats`).
2. You can know *where* the identifier was created.
3. You can know *who* created the identifier.

In a distributed system, these three pieces of information can be valuable. Not
only is there no need for a central authority to generate identifiers, but you
can determine what nodes in your infrastructure created the UUIDs, at what time
they were created, and the account on the machine that created them.

.. code-block::
    :caption: Use a domain to generate a version 2, DCE Security UUID

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

This will generate a version 2 UUID and print out its string representation, the
time the UUID was created, and the node used to create it, as well as the name
of the local domain specified and the local domain identifier (in this case, a
POSIX UID, automatically obtained from the local machine).

It will look something like this:

.. code-block:: text

    UUID: 000001f5-5e9a-21ea-9e00-0242ac130003
    Version: 2
    Date: Thu, 05 Mar 2020 04:30:10 +0000
    Node: 0242ac130003
    Domain: person
    ID: 501

.. todo::

    Needs discussion on domains (list the domains), ability to specify the node
    and clock sequence (though the lower 8 bits of the clock sequence, originally
    a 14-bit integer, are replaced with the domain). In theory, 2^8-1 domains
    could be defined, but only 3 are registered by the DCE specification.
    Discuss ability to pass the local identifier.

    .. epigraph::

        Note that the [domain] can potentially hold values outside the range
        [0, 2\ :sup:`8` -- 1]; however, the only values currently registered are in
        the range [0, 2]… [DCE11SEC]_


.. _rfc4122.version2.caveats:

Problems With Version 2 UUIDs
#############################

Version 2 UUIDs can be useful for the data they contain. However, there are
trade-offs in choosing to use them.

Lossy Timestamps
----------------

Version 2 UUIDs are first generated in the same way as version 1 UUIDs, but then
the low part of the timestamp (the ``time_low`` field) is replaced by a 32-bit
integer that represents a local identifier, which refers to

It is important to note that a version 2 UUID suffers from some loss of
fidelity of the timestamp, due to replacing the time_low field with the
local identifier. When constructing the timestamp value for date
purposes, we replace the local identifier bits with zeros. As a result,
the timestamp can be off by a range of 0 to 429.4967295 seconds (or 7
minutes, 9 seconds, and 496730 microseconds).

Astute observers might note this value directly corresponds to
2\ :sup:`32` -- 1, or ``0xffffffff``. The local identifier is 32-bits, and we
have set each of these bits to 0, so the maximum range of timestamp drift is
``0x00000000`` to ``0xffffffff`` (counted in 100-nanosecond intervals).

Limited Unique UUIDs
--------------------

With the inclusion of the local identifier comes a serious limitation in the
amount of unique UUIDs that may be created.
