.. _reference.nonstandard.uuidv6:

===================
Nonstandard\\UuidV6
===================

.. php:namespace:: Ramsey\Uuid\Nonstandard

.. php:class:: UuidV6

    .. attention::

        :php:class:`Ramsey\\Uuid\\Nonstandard\\UuidV6` is deprecated in favor of :php:class:`Ramsey\\Uuid\\Rfc4122\\UuidV6`.
        Please migrate any code using ``Nonstandard\UuidV6`` to ``Rfc4122\UuidV6``.

    Implements :php:interface:`Ramsey\\Uuid\\Rfc4122\\UuidInterface`.

    UuidV6 represents a :ref:`version 6, reordered Gregorian time UUID <nonstandard.version6>`. In addition to providing
    the methods defined on the interface, this class additionally provides the following methods.

    .. php:method:: getDateTime()

        :returns: A date object representing the timestamp associated with the UUID
        :returntype: ``\DateTimeInterface``

    .. php:method:: toUuidV1()

        :returns: A version 1 UUID, converted from this version 6 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV1

    .. php:staticmethod:: fromUuidV1()

        :param Ramsey\\Uuid\\Rfc4122\\UuidV1 $uuidV1: A version 1 UUID
        :returns: A version 6 UUID, converted from the given version 1 UUID
        :returntype: Ramsey\\Uuid\\Rfc4122\\UuidV6
