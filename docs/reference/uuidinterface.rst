.. _reference.uuidinterface:

=============
UuidInterface
=============

.. php:namespace:: Ramsey\Uuid

.. php:interface:: UuidInterface

    Represents a UUID.

    .. php:method:: compareTo($other)

        :param Ramsey\\Uuid\\UuidInterface $other: The UUID to compare
        :returns: Returns ``-1``, ``0``, or ``1`` if the UUID is less than, equal to, or greater than the other UUID.
        :returntype: ``int``

    .. php:method:: equals($other)

        :param object|null $other: An object to test for equality with this UUID.
        :returns: Returns true if the UUID is equal to the provided object.
        :returntype: ``bool``

    .. php:method:: getBytes()

        :returns: A binary string representation of the UUID.
        :returntype: ``string``

    .. php:method:: getFields()

        :returns: The fields that comprise this UUID.
        :returntype: Ramsey\\Uuid\\Fields\\FieldsInterface

    .. php:method:: getHex()

        :returns: The hexadecimal representation of the UUID.
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: getInteger()

        :returns: The integer representation of the UUID.
        :returntype: Ramsey\\Uuid\\Type\\Integer

    .. php:method:: getUrn()

        :returns: The string standard representation of the UUID as a `URN`_.
        :returntype: ``string``

    .. php:method:: toString()

        :returns: The string standard representation of the UUID.
        :returntype: ``string``

    .. php:method:: __toString()

        :returns: The string standard representation of the UUID.
        :returntype: ``string``

.. _URN: https://www.rfc-editor.org/rfc/rfc8141
