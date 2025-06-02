.. _reference.rfc4122.uuidinterface:

======================
Rfc4122\\UuidInterface
======================

.. php:namespace:: Ramsey\Uuid\Rfc4122

.. php:interface:: UuidInterface

    Implements :php:interface:`Ramsey\\Uuid\\UuidInterface`.

    Rfc4122\\UuidInterface represents an `RFC 9562`_ (formerly `RFC 4122`_) UUID. In addition to the methods defined on
    the interface, this interface additionally defines the following methods.

    .. php:method:: getFields()

        :returns: The fields that comprise this UUID.
        :returntype: Ramsey\\Uuid\\Rfc4122\\FieldsInterface

.. _RFC 4122: https://www.rfc-editor.org/rfc/rfc4122
.. _RFC 9562: https://www.rfc-editor.org/rfc/rfc9562
