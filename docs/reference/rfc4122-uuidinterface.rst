.. _reference.rfc4122.uuidinterface:

======================
Rfc4122\\UuidInterface
======================

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:interface:: UuidInterface

    Implements :php:interface:`Ramsey\\Uuid\\UuidInterface`.

    Rfc4122\UuidInterface represents an RFC 4122 UUID. In addition to the
    methods defined on the interface, this interface additionally defines the
    following methods.

    .. php:method:: getFields()

        :returns: The fields that comprise this UUID.
        :returntype: Ramsey\\Uuid\\Rfc4122\\FieldsInterface

    .. php:method:: getUrn()

        :returns: The string standard representation of the UUID as a `URN`_.
        :returntype: ``string``


.. _URN: https://tools.ietf.org/html/rfc8141
