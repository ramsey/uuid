.. _reference.rfc4122.uuidv2:

===============
Rfc4122\\UuidV2
===============

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:class:: UuidV2

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    UuidV2 represents a :ref:`version 2, DCE Security UUID <rfc4122.version2>`.
    In addition to providing the methods defined on the interface, this class
    additionally provides the following methods.

    .. php:method:: getDateTime()

        Returns a DateTimeInterface object representing the timestamp associated
        with the UUID

        .. caution::

            It is important to note that a version 2 UUID suffers from some loss of
            fidelity of the timestamp, due to replacing the ``time_low`` field with
            the local identifier. When constructing the timestamp value for date
            purposes, we replace the local identifier bits with zeros. As a result,
            the timestamp can be off by a range of 0 to 429.4967295 seconds (or
            about 7 minutes, 9 seconds, and 496730 microseconds).

            Astute observers might note this value directly corresponds to
            2\ :sup:`32` -- 1, or ``0xffffffff``. The local identifier is 32-bits,
            and we have set each of these bits to 0, so the maximum range of
            timestamp drift is ``0x00000000`` to ``0xffffffff`` (counted in
            100-nanosecond intervals).

        :returns: A date object representing the timestamp associated with the UUID
        :returntype: ``\DateTimeInterface``

    .. php:method:: getLocalDomain()

        :returns: The local domain identifier for this UUID, which is one of :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_PERSON`, :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_GROUP`, or :php:const:`Ramsey\\Uuid\\Uuid::DCE_DOMAIN_ORG`
        :returntype: ``int``

    .. php:method:: getLocalDomainName()

        :returns: A string name associated with the local domain identifier (one of "person," "group," or "org")
        :returntype: ``string``

    .. php:method:: getLocalIdentifier()

        :returns: The local identifier used when creating this UUID
        :returntype: Ramsey\\Uuid\\Type\\Integer
