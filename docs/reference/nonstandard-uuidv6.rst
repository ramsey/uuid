.. _reference.nonstandard.uuidv6:

===================
Nonstandard\\UuidV6
===================

.. php:namespace:: Ramsey\Uuid\Nonstandard

.. php:class:: UuidV6

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    While in the Nonstandard sub-namespace, UuidV6 implements the same interface
    as the RFC 4122 UUIDs. This is because the definition for version 6 UUIDs is
    `currently in draft form`_, with the intent to update RFC 4122.

    UuidV6 represents a :ref:`version 6, ordered-time UUID
    <nonstandard.version6>`. In addition to providing the methods defined on the
    interface, this class additionally provides the following methods.

    .. php:method:: getDateTime()

        :returns: A date object representing the timestamp associated with the UUID
        :returntype: ``\DateTimeInterface``

    .. php:method:: toUuidV1()

        :returns: A version 1 UUID, converted from this version 6 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1

    .. php:staticmethod:: fromUuidV1()

        :param Ramsey\\Uuid\\Rfc4122\\UuidV1 $uuidV1: A version 1 UUID
        :returns: A version 6 UUID, converted from the given version 1 UUID
        :returntype: Ramsey\\Uuid\\Nonstandard\\UuidV6


.. _currently in draft form: https://tools.ietf.org/html/draft-peabody-dispatch-new-uuid-format-00
