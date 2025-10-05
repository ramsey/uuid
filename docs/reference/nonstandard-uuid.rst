.. _reference.nonstandard.uuid:

=================
Nonstandard\\Uuid
=================

.. php:namespace:: Ramsey\Uuid\Nonstandard

.. php:class:: Uuid

    Implements :php:interface:`Ramsey\\Uuid\\UuidInterface`.

    Nonstandard\\Uuid represents :ref:`nonstandard.other`. In addition to providing the methods defined on the interface,
    this class additionally provides the following methods.

    .. php:method:: getFields()

        :returns: The fields that comprise this UUID
        :returntype: Ramsey\\Uuid\\Nonstandard\\Fields
