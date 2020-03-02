.. _reference.rfc4122.uuidinterface:

======================
Rfc4122\\UuidInterface
======================

All RFC 4122 UUID instances in ramsey/uuid implement the following interface.

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:interface:: UuidInterface

    Represents an RFC 4122 UUID.

    .. php:method:: compareTo($other)

        :param Ramsey\\Uuid\\UuidInterface $other: The UUID to compare
        :returns: (*int*) Returns ``-1``, ``0``, or ``1`` if the UUID is less than, equal to, or greater than the other UUID.

    .. php:method:: equals($other)

        :param object|null $other: An object to test for equality with this UUID.
        :returns: (*bool*) Returns true if the UUID is equal to the provided object.

    .. php:method:: getBytes()

        :returns: (*string*) A binary string representation of the UUID.

    .. php:method:: getFields()

        :returns: (:php:interface:`Ramsey\\Uuid\\Rfc4122\\FieldsInterface`) The fields that comprise this UUID.

    .. php:method:: getHex()

        :returns: (*Ramsey\\Uuid\\Type\\Hexadecimal*) The hexadecimal representation of the UUID.

    .. php:method:: getInteger()

        :returns: (*Ramsey\\Uuid\\Type\\Integer*) The integer representation of the UUID.

    .. php:method:: toString()

        :returns: (*string*) The string standard representation of the UUID.

    .. php:method:: getUrn()

        :returns: (*string*) The string standard representation of the UUID as a `URN`_.


.. _URN: https://tools.ietf.org/html/rfc8141
